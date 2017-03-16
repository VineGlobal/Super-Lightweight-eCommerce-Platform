#!/usr/bin/env php
<?php
require('base.php');
require('oauth2/token.php');
require('oauth2/googleapi.php');


class ExchangeCodeForTokens extends Base {

	public function execute() {

		// get code from CLI parameter - exit if false (bad/no data)
		if (($authCode = $this->getAuthCodeFromCLI()) === false) return;

		$OAuth2GoogleAPI = $this->getOAuth2GoogleAPIInstance();

		// make request for OAuth2 tokens
		echo(sprintf(
			"Requesting OAuth2 tokens via authorization code: %s\n",
			$authCode
		));

		try {
			$tokenData = $OAuth2GoogleAPI->getAccessTokenFromAuthCode($authCode);

			// save token data to disk
			echo(sprintf(
				"Success! Saving token data to [%s]\n",
				$this->config['tokenDataFile']
			));

			$this->saveOAuth2TokenData($tokenData);
			// all done

		} catch (Exception $e) {
			// token fetch error
			echo(sprintf("Error: %s\n",$e->getMessage()));
		}
	}

	private function getAuthCodeFromCLI() {

		// attempt to get code from CLI
		if (!($optList = getopt('c:'))) {
			// no -c switch or switch without value
			echo(sprintf("Usage: %s -c AUTHORIZATION CODE\n",basename(__FILE__)));
			return false;
		}

		// validate authorization code format
		$authCode = $optList['c'];
		if (!preg_match('/^[0-9A-Za-z._\/-]{30,62}$/',$authCode)) {
			echo("Error: invalid Google authorization code format\n\n");
			return false;
		}

		// all valid text wise
		return $authCode;
	}
}


(new ExchangeCodeForTokens(require('config.php')))->execute();
