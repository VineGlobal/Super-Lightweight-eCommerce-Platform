<?php
namespace GoogleSpreadsheet\API\Parser;


class SimpleEntry extends \GoogleSpreadsheet\API\Parser {

	private $entryList = [];
	private $entryItem = [];
	private $indexRegexp;
	private $additionalElementSaveList;


	public function __construct(
		$indexRegexp,
		array $additionalElementSaveList = []
	) {

		// save simple entry regexp and (optional) additional elements to save for an entry
		$this->indexRegexp = $indexRegexp;
		$this->additionalElementSaveList = $additionalElementSaveList;

		// init XML parser
		parent::__construct(
			function($name,$elementPath) {

				if ($elementPath == 'FEED/ENTRY') {
					// store last entry and start next
					$this->addItem($this->entryItem);
					$this->entryItem = [];
				}
			},
			function($elementPath,$data) {

				switch ($elementPath) {
					case 'FEED/ENTRY/ID':
						$this->entryItem['ID'] = $data;
						break;

					case 'FEED/ENTRY/UPDATED':
						$this->entryItem['updated'] = strtotime($data);
						break;

					case 'FEED/ENTRY/TITLE':
						$this->entryItem['name'] = $data;
						break;

					default:
						// additional elements to save
						if (
							$this->additionalElementSaveList &&
							(isset($this->additionalElementSaveList[$elementPath]))
						) {
							// found one - add to stack
							$this->entryItem[$this->additionalElementSaveList[$elementPath]] = $data;
						}
				}
			}
		);
	}

	public function getList() {

		// add final parsed entry and return list
		$this->addItem($this->entryItem);
		return $this->entryList;
	}

	private function addItem(array $entryItem) {

		if (isset(
			$entryItem['ID'],
			$entryItem['updated'],
			$entryItem['name']
		)) {
			// if additional element save critera - ensure they were found for entry
			$saveEntryOK = true;
			if ($this->additionalElementSaveList) {
				foreach ($this->additionalElementSaveList as $entryKey) {
					if (!isset($entryItem[$entryKey])) {
						// not found - skip entry
						$saveEntryOK = false;
						break;
					}
				}
			}

			// extract the entry index from the ID to use as array index
			if (
				$saveEntryOK &&
				preg_match($this->indexRegexp,$entryItem['ID'],$match)
			) {
				$this->entryList[$match['index']] = $entryItem;
			}
		}
	}
}
