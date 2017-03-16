<?php
namespace GoogleSpreadsheet\API;


class Parser {

	private $XMLParser;
	private $XMLParsedChunk = false;


	public function __construct(callable $elementStartHandler,callable $dataHandler) {

		// create new XML parser
		$this->XMLParser = xml_parser_create();
		$elementPathList = [];
		$elementPath = '';
		$elementData = false;

		// setup element start/end handlers
		xml_set_element_handler(
			$this->XMLParser,
			function($parser,$name,array $attribList)
				use ($elementStartHandler,&$elementPathList,&$elementPath,&$elementData) {

				// update element path (level down), start catching element data
				$elementPathList[] = $name;
				$elementPath = implode('/',$elementPathList);
				$elementData = '';

				// call $elementStartHandler with open element details
				$elementStartHandler($name,$elementPath,$attribList);
			},
			function($parser,$name)
				use ($dataHandler,&$elementPathList,&$elementPath,&$elementData) {

				if ($elementData !== false) {
					// call $dataHandler with element path and data
					$dataHandler($elementPath,$elementData);
				}

				// update element path (level up), stop catching element data
				array_pop($elementPathList);
				$elementPath = implode('/',$elementPathList);
				$elementData = false;
			}
		);

		// setup element data handler
		xml_set_character_data_handler(
			$this->XMLParser,
			function($parser,$data) use (&$elementData) {

				// the function here can be called multiple times for a single open element if linefeeds are found
				if ($elementData !== false) {
					$elementData .= $data;
				}
			}
		);
	}

	public function process($data) {

		if (!xml_parse(
			$this->XMLParser,
			($data === true) ? '' : $data, // ($data === true) to signify final chunk of XML
			($data === true)
		)) {
			// throw XML parse exception
			throw new \Exception(
				'XML parse error: ' .
				xml_error_string(xml_get_error_code($this->XMLParser))
			);
		}

		$this->XMLParsedChunk = true;
	}

	public function close() {

		if ($this->XMLParsedChunk) {
			// used to signify the final chunk of XML to be parsed
			$this->process(true);
		}

		xml_parser_free($this->XMLParser);
	}
}
