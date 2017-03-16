<?php
namespace GoogleSpreadsheet;


class API {

	const HTTP_CODE_OK = 200;
	const CURL_BUFFER_SIZE = 16384;

	const CONTENT_TYPE_ATOMXML = 'application/atom+xml';
	const API_BASE_URL = 'https://spreadsheets.google.com/feeds';
	// note: not using this HTTP header in requests - causes issue with getWorksheetCellList() method (cell versions not returned in XML)
	const API_VERSION_HTTP_HEADER = 'GData-Version: 3.0';

	private $OAuth2GoogleAPI;


	public function __construct(\OAuth2\GoogleAPI $OAuth2GoogleAPI) {

		$this->OAuth2GoogleAPI = $OAuth2GoogleAPI;
	}

	public function getSpreadsheetList() {

		// init XML parser
		$parser = new API\Parser\SimpleEntry('/\/(?P<index>[a-zA-Z0-9-_]+)$/');
		$hasResponseData = false;

		// make request
		list($responseHTTPCode,$responseBody) = $this->OAuth2Request(
			self::API_BASE_URL . '/spreadsheets/private/full',
			null,
			function($data) use ($parser,&$hasResponseData) {

				$parser->process($data);
				$hasResponseData = true;
			}
		);

		// end of XML parse
		$parser->close();

		// HTTP code always seems to be 200 - so check for empty response body when in error
		if (!$hasResponseData) {
			throw new \Exception('Unable to retrieve spreadsheet listing');
		}

		return $parser->getList();
	}

	public function getWorksheetList($spreadsheetKey) {

		// init XML parser
		$parser = new API\Parser\SimpleEntry(
			'/\/(?P<index>[a-z0-9]+)$/',[
				'FEED/ENTRY/GS:COLCOUNT' => 'columnCount',
				'FEED/ENTRY/GS:ROWCOUNT' => 'rowCount'
			]
		);

		// make request
		list($responseHTTPCode,$responseBody) = $this->OAuth2Request(
			sprintf(
				'%s/worksheets/%s/private/full',
				self::API_BASE_URL,
				$spreadsheetKey
			),
			null,
			function($data) use ($parser) { $parser->process($data); }
		);

		// end of XML parse
		$parser->close();

		$this->checkAPIResponseError(
			$responseHTTPCode,$responseBody,
			'Unable to retrieve worksheet listing'
		);

		return $parser->getList();
	}

	public function getWorksheetDataList($spreadsheetKey,$worksheetID) {

		// supporting code for XML parse
		$worksheetHeaderList = [];
		$worksheetDataList = [];
		$dataItem = [];
		$addDataItem = function(array $dataItem) use (&$worksheetHeaderList,&$worksheetDataList) {

			if ($dataItem) {
				// add headers found to complete header list
				foreach ($dataItem as $headerName => $void) {
					$worksheetHeaderList[$headerName] = true;
				}

				// add list item to collection
				$worksheetDataList[] = $dataItem;
			}
		};

		// init XML parser
		$parser = new API\Parser(
			function($name,$elementPath) use ($addDataItem,&$dataItem) {

				if ($elementPath == 'FEED/ENTRY') {
					// store last data row and start new row
					$addDataItem($dataItem);
					$dataItem = [];
				}
			},
			function($elementPath,$data) use (&$dataItem) {

				// looking for a header element type
				if (preg_match('/^FEED\/ENTRY\/GSX:(?P<name>[^\/]+)$/',$elementPath,$match)) {
					$dataItem[strtolower($match['name'])] = trim($data);
				}
			}
		);

		// make request
		list($responseHTTPCode,$responseBody) = $this->OAuth2Request(
			sprintf(
				'%s/list/%s/%s/private/full',
				self::API_BASE_URL,
				$spreadsheetKey,
				$worksheetID
			),
			null,
			function($data) use ($parser) { $parser->process($data); }
		);

		// end of XML parse - add final parsed data row
		$parser->close();
		$addDataItem($dataItem);

		$this->checkAPIResponseError(
			$responseHTTPCode,$responseBody,
			'Unable to retrieve worksheet data listing'
		);

		// return worksheet headers and data list
		return [
			'headerList' => array_keys($worksheetHeaderList),
			'dataList' => $worksheetDataList
		];
	}

	public function getWorksheetCellList($spreadsheetKey,$worksheetID,array $cellRangeCriteriaList = []) {

		// build cell fetch range criteria for URL if given
		$cellRangeCriteriaQuerystringList = [];
		if ($cellRangeCriteriaList) {
			$rangeCriteriaMapList = [
				'columnEnd' => 'max-col',
				'columnStart' => 'min-col',
				'rowEnd' => 'max-row',
				'rowStart' => 'min-row'
			];

			// ensure all given keys are valid
			if ($invalidCriteriaList = array_diff(array_keys($cellRangeCriteriaList),array_keys($rangeCriteriaMapList))) {
				// invalid keys found
				throw new \Exception('Invalid cell range criteria [' . implode(',',$invalidCriteriaList) . ']');
			}

			// all valid, build querystring
			foreach ($rangeCriteriaMapList as $key => $mapTo) {
				if (isset($cellRangeCriteriaList[$key])) {
					$cellRangeCriteriaQuerystringList[] = $mapTo . '=' . intval($cellRangeCriteriaList[$key]);
				}
			}
		}

		// supporting code for XML parse
		$worksheetCellList = [];
		$cellItemData = [];
		$addCellItem = function(array $cellItemData) use (&$worksheetCellList) {

			if (isset(
				$cellItemData['ref'],
				$cellItemData['value'],
				$cellItemData['URL']
			)) {
				// add cell item instance to list
				$cellReference = strtoupper($cellItemData['ref']);

				$worksheetCellList[$cellReference] = new CellItem(
					$cellItemData['URL'],
					$cellReference,
					$cellItemData['value']
				);
			}
		};

		// init XML parser
		$parser = new API\Parser(
			function($name,$elementPath,array $attribList) use ($addCellItem,&$cellItemData) {

				switch ($elementPath) {
					case 'FEED/ENTRY':
						// store last data row and start new row
						$addCellItem($cellItemData);
						$cellItemData = [];
						break;

					case 'FEED/ENTRY/LINK':
						if (
							(isset($attribList['REL'],$attribList['HREF'])) &&
							($attribList['REL'] == 'edit')
						) {
							// store versioned cell url
							$cellItemData['URL'] = $attribList['HREF'];
						}

						break;
				}
			},
			function($elementPath,$data) use (&$cellItemData) {

				switch ($elementPath) {
					case 'FEED/ENTRY/TITLE':
						$cellItemData['ref'] = $data; // cell reference (e.g. "B1")
						break;

					case 'FEED/ENTRY/CONTENT':
						$cellItemData['value'] = $data; // cell value
						break;
				}
			}
		);

		// make request
		list($responseHTTPCode,$responseBody) = $this->OAuth2Request(
			sprintf(
				'%s/cells/%s/%s/private/full%s',
				self::API_BASE_URL,
				$spreadsheetKey,
				$worksheetID,
				($cellRangeCriteriaQuerystringList)
					? '?' . implode('&',$cellRangeCriteriaQuerystringList)
					: ''
			),
			null,
			function($data) use ($parser) { $parser->process($data); }
		);

		// end of XML parse - add final parsed cell item
		$parser->close();
		$addCellItem($cellItemData);

		$this->checkAPIResponseError(
			$responseHTTPCode,$responseBody,
			'Unable to retrieve worksheet cell listing'
		);

		// return worksheet cell list
		return $worksheetCellList;
	}

	public function updateWorksheetCellList($spreadsheetKey,$worksheetID,array $worksheetCellList) {

		// scan cell list - at least one cell must be in 'dirty' state
		$hasDirty = false;
		foreach ($worksheetCellList as $cellItem) {
			if ($cellItem->isDirty()) {
				$hasDirty = true;
				break;
			}
		}

		if (!$hasDirty) {
			// no work to do
			return false;
		}

		// make request
		$cellIDCounter = -1;
		$excessBuffer = false;
		$finalCellSent = false;
		$splitBuffer = function($bytesReadMax,$buffer) {

			if (strlen($buffer) > $bytesReadMax) {
				// split buffer to maximum read bytes and remainder
				return [
					substr($buffer,0,$bytesReadMax),
					substr($buffer,$bytesReadMax)
				];
			}

			// can send the full buffer at once
			return [$buffer,false];
		};

		list($responseHTTPCode,$responseBody) = $this->OAuth2Request(
			sprintf(
				'%s/cells/%s/%s/private/full/batch',
				self::API_BASE_URL,
				$spreadsheetKey,
				$worksheetID
			),
			function($bytesReadMax)
				use (
					$spreadsheetKey,$worksheetID,
					&$worksheetCellList,&$cellIDCounter,&$excessBuffer,&$finalCellSent,$splitBuffer
				) {

				if ($finalCellSent) {
					// end of read buffer
					return '';
				}

				if ($excessBuffer !== false) {
					// send more of read buffer from previous callback run
					list($readBuffer,$excessBuffer) = $splitBuffer($bytesReadMax,$excessBuffer);
					return $readBuffer;
				}

				if ($cellIDCounter < 0) {
					// emit XML header
					$cellIDCounter++;

					return sprintf(
						'<feed xmlns="http://www.w3.org/2005/Atom" ' .
							'xmlns:batch="http://schemas.google.com/gdata/batch" ' .
							'xmlns:gs="http://schemas.google.com/spreadsheets/2006">' .
						'<id>%s/cells/%s/%s/private/full</id>',
						self::API_BASE_URL,
						$spreadsheetKey,$worksheetID
					);
				}

				// find next cell update to send
				while ($worksheetCellList) {
					$cellItem = array_shift($worksheetCellList);
					if ($cellItem->isDirty()) {
						// cell to be updated
						break;
					}

					$cellItem = false;
				}

				if ($cellItem === false) {
					// no more cells - send XML </feed> tail
					$finalCellSent = true;
					return '</feed>';
				}

				$cellIDCounter++;
				list($readBuffer,$excessBuffer) = $splitBuffer(
					$bytesReadMax,
					$this->updateWorksheetCellListBuildBatchUpdateEntry(
						$spreadsheetKey,$worksheetID,
						$cellIDCounter,$cellItem
					)
				);

				// send read buffer
				return $readBuffer;
			}
		);

		$this->checkAPIResponseError(
			$responseHTTPCode,$responseBody,
			'Unable to update worksheet cell(s)'
		);

		// all done
		return true;
	}

	private function updateWorksheetCellListBuildBatchUpdateEntry(
		$spreadsheetKey,$worksheetID,
		$cellBatchID,CellItem $cellItem
	) {

		$cellBaseURL = sprintf(
			'%s/cells/%s/%s/private/full/R%dC%d',
			self::API_BASE_URL,
			$spreadsheetKey,$worksheetID,
			$cellItem->getRow(),
			$cellItem->getColumn()
		);

		return sprintf(
			'<entry>' .
				'<batch:id>batchItem%d</batch:id>' .
				'<batch:operation type="update" />' .
				'<id>%s</id>' .
				'<link rel="edit" type="%s" href="%s/%s" />' .
				'<gs:cell row="%d" col="%d" inputValue="%s" />' .
			'</entry>',
			$cellBatchID,
			$cellBaseURL,
			self::CONTENT_TYPE_ATOMXML,
			$cellBaseURL,$cellItem->getVersion(),
			$cellItem->getRow(),$cellItem->getColumn(),
			htmlspecialchars($cellItem->getValue())
		);
	}

	private function OAuth2Request(
		$URL,
		callable $readHandler = null,callable $writeHandler = null
	) {

		$responseHTTPCode = false;
		$responseBody = '';

		// build option list
		$optionList = [
			CURLOPT_BUFFERSIZE => self::CURL_BUFFER_SIZE,
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => [
				'Accept: ',
				'Expect: ', // added by CURLOPT_READFUNCTION
				// Google OAuth2 credentials
				implode(': ',$this->OAuth2GoogleAPI->getAuthHTTPHeader())
			],
			CURLOPT_RETURNTRANSFER => ($writeHandler === null), // only return response from curl_exec() if no $writeHandler given
			CURLOPT_URL => $URL
		];

		// add optional read and/or write data handlers
		if ($readHandler !== null) {
			// POST data with XML content type if using a read handler
			$optionList += [
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_PUT => true, // required to enable CURLOPT_READFUNCTION
				CURLOPT_READFUNCTION =>
					// don't need curl instance/stream resource - so proxy handler in closure to remove
					function($curlConn,$stream,$bytesReadMax) use ($readHandler) {
						return $readHandler($bytesReadMax);
					}
			];

			$optionList[CURLOPT_HTTPHEADER][] = 'Content-Type: ' . self::CONTENT_TYPE_ATOMXML;
		}

		if ($writeHandler !== null) {
			$optionList[CURLOPT_WRITEFUNCTION] =
				// proxy so we can capture HTTP response code before using given write handler
				function($curlConn,$data) use ($writeHandler,&$responseHTTPCode,&$responseBody) {

					// fetch HTTP response code if not known yet
					if ($responseHTTPCode === false) {
						$responseHTTPCode = curl_getinfo($curlConn,CURLINFO_HTTP_CODE);
					}

					if ($responseHTTPCode == self::HTTP_CODE_OK) {
						// call handler
						$writeHandler($data);

					} else {
						// bad response - put all response data into $responseBody
						$responseBody .= $data;
					}

					// return byte count processed (all of it)
					return strlen($data);
				};
		}

		$curlConn = curl_init();
		curl_setopt_array($curlConn,$optionList);

		// make request, close curl session
		// mute curl warnings that could fire from read/write handlers that throw exceptions
		set_error_handler(function() {},E_WARNING);
		$curlExecReturn = curl_exec($curlConn);
		restore_error_handler();

		if ($responseHTTPCode === false) {
			$responseHTTPCode = curl_getinfo($curlConn,CURLINFO_HTTP_CODE);
		}

		curl_close($curlConn);

		// return HTTP code and response body
		return [
			$responseHTTPCode,
			($writeHandler === null) ? $curlExecReturn : $responseBody
		];
	}

	private function checkAPIResponseError($HTTPCode,$body,$errorMessage) {

		if ($HTTPCode != self::HTTP_CODE_OK) {
			// error with API call - throw error with returned message
			$body = trim(htmlspecialchars_decode($body,ENT_QUOTES));

			throw new \Exception(
				$errorMessage .
				(($body != '') ? ' - ' . $body : '')
			);
		}

		// all good
	}
}
