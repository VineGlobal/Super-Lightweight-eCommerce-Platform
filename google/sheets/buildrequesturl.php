#!/usr/bin/env php
<?php
require('base.php');
require('googlespreadsheet/api.php');


class BuildRequestURL extends Base {

	public function execute() {

		echo(
			"Visit the following URL in a browser to authenticate your Google account against the OAuth2 API:\n\n" .

			$this->buildURL([
				GoogleSpreadsheet\API::API_BASE_URL
			]) .

			"After successful authentication, make note of the value of 'code=' on the URL query string for next steps.\n"
		);
	}

	private function buildURL(array $scopeList) {

		$OAuth2URLList = $this->config['OAuth2URL'];

		// ensure all scopes have trailing forward slash
		foreach ($scopeList as &$scopeItem) $scopeItem = rtrim($scopeItem,'/') . '/';

		$buildQuerystring = function(array $list) {

			$querystringList = [];
			foreach ($list as $key => $value) {
				$querystringList[] = rawurlencode($key) . '=' . rawurlencode($value);
			}

			return implode('&',$querystringList);
		};

		return sprintf(
			"%s/%s?%s\n\n",
			$OAuth2URLList['base'],$OAuth2URLList['auth'],
			$buildQuerystring([
				'access_type' => 'offline',
				'approval_prompt' => 'force',
				'client_id' => $this->config['clientID'],
				'redirect_uri' => $OAuth2URLList['redirect'],
				'response_type' => 'code',
				'scope' => implode(' ',$scopeList)
			])
		);
	}
}


(new BuildRequestURL(require('config.php')))->execute();
