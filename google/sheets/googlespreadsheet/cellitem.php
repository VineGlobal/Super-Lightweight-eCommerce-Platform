<?php
namespace GoogleSpreadsheet;


class CellItem {

	private $cellRow;
	private $cellColumn;
	private $cellVersion;
	private $cellReference;
	private $value;
	private $valueInitial;


	public function __construct($URL,$cellReference,$value) {

		// extract key data from cell URL
		if (!preg_match(
			'/\/private\/full\/R(?P<row>[0-9]+)C(?P<column>[0-9]+)\/(?P<version>[a-z0-9]+)$/',
			$URL,$matchList
		)) {
			// invalid Google spreadsheet cell URL format
			throw new \Exception('Invalid spreadsheet cell item URL format');
			return;
		}

		// save data items from URL
		$this->cellRow = $matchList['row'];
		$this->cellColumn = $matchList['column'];
		$this->cellVersion = $matchList['version'];

		// save cell reference (e.g. 'B1') and current cell value
		$this->cellReference = $cellReference;
		$this->valueInitial = $this->value = $value;
	}

	public function getRow() {

		return $this->cellRow;
	}

	public function getColumn() {

		return $this->cellColumn;
	}

	public function getVersion() {

		return $this->cellVersion;
	}

	public function getReference() {

		return $this->cellReference;
	}

	public function getValue() {

		return $this->value;
	}

	public function setValue($value) {

		$this->value = $value;
	}

	public function isDirty() {

		return ($this->value !== $this->valueInitial);
	}
}
