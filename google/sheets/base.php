<?php
abstract class Base {

	protected $config;


	public function __construct(array $config) {

		$this->config = $config;
	}

	protected function getOAuth2GoogleAPIInstance() {

		$OAuth2URLList = $this->config['OAuth2URL'];

		return new OAuth2\GoogleAPI(
			$OAuth2URLList['base'] . '/' . $OAuth2URLList['token'],
			$OAuth2URLList['redirect'],
			$this->config['clientID'],
			$this->config['clientSecret']
		);
	}

	protected function saveOAuth2TokenData(array $data) {

		file_put_contents(
			$this->config['tokenDataFile'],
			serialize($data)
		);
	}
}
