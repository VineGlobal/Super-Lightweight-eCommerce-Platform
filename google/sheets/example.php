
<?php

phpinfo();die();
 error_reporting(E_ALL);
ini_set('display_errors', 1);
  

require('base.php');
require('googlespreadsheet/api.php');
require('googlespreadsheet/api/parser.php');
require('googlespreadsheet/api/parser/simpleentry.php');
require('googlespreadsheet/cellitem.php');
require('oauth2/token.php');
require('oauth2/googleapi.php');


class Example extends Base {


	public function execute() {

		// load OAuth2 token data - exit if false
		if (($tokenData = $this->loadOAuth2TokenData()) === false) {
			return;
		}

		// setup Google OAuth2 handler
		$OAuth2GoogleAPI = $this->getOAuth2GoogleAPIInstance();

		$OAuth2GoogleAPI->setTokenData(
			$tokenData['accessToken'],
			$tokenData['tokenType'],
			$tokenData['expiresAt'],
			$tokenData['refreshToken']
		);

		$OAuth2GoogleAPI->setTokenRefreshHandler(function(array $tokenData) {

			// save updated OAuth2 token data back to file
			$this->saveOAuth2TokenData($tokenData);
		});

		$spreadsheetAPI = new GoogleSpreadsheet\API($OAuth2GoogleAPI);

		// fetch all spreadsheets and display
		$spreadsheetList = $spreadsheetAPI->getSpreadsheetList();
		print_r($spreadsheetList);

		if (!$spreadsheetList) {
			echo("Error: No spreadsheets found\n");
			exit();
		}

		// fetch key of first spreadsheet
		$spreadsheetKey = array_keys($spreadsheetList)[0];

		// fetch all worksheets and display
		$worksheetList = $spreadsheetAPI->getWorksheetList($spreadsheetKey);
		print_r($worksheetList);

		// fetch ID of first worksheet
		$worksheetID = array_keys($worksheetList)[0];

		// fetch worksheet data list and display
		print_r($spreadsheetAPI->getWorksheetDataList(
			$spreadsheetKey,
			$worksheetID
		));

		// fetch worksheet cell list and display
		$cellList = $spreadsheetAPI->getWorksheetCellList(
			$spreadsheetKey,
			$worksheetID,[
				'columnStart' => 2,
				'columnEnd' => 10
			]
		);

		print_r($cellList);

		// update content of first cell
		/*
		$cellIndex = array_keys($cellList)[0];
		$cellList[$cellIndex]->setValue(
			$cellList[$cellIndex]->getValue() . ' updated'
		);

		$spreadsheetAPI->updateWorksheetCellList(
			$spreadsheetKey,
			$worksheetID,
			$cellList
		);
		*/
	}

	private function loadOAuth2TokenData() {

		$tokenDataFile = $this->config['tokenDataFile'];

		if (!is_file($tokenDataFile)) {
			echo(sprintf(
				"Error: unable to locate token file [%s]\n",
				$tokenDataFile
			));

			return false;
		}

		// load file, return data as PHP array
		return unserialize(file_get_contents($tokenDataFile));
	}
}


(new Example(require('config.php')))->execute();
