<?php
namespace OAuth2;


class GoogleAPI extends Token {

	const OAUTH2_TOKEN_EXPIRY_WINDOW = 300; // if token expiry is within this time window (seconds) then re-token

	private $clientID;
	private $clientSecret;

	private $accessToken = false;
	private $tokenType = false;
	private $expiresAt = false;
	private $refreshToken = false;
	private $refreshTokenHandler = null;


	public function __construct($tokenURL,$redirectURL,$clientID,$clientSecret) {

		parent::__construct($tokenURL,$redirectURL);

		// Google OAuth2 has the addition of 'client id' and 'client secret'
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
	}

	public function setTokenData($access,$type,$expiresAt,$refresh = false) {

		$this->accessToken = $access;
		$this->tokenType = $type;
		$this->expiresAt = intval($expiresAt);
		$this->refreshToken = $refresh;
	}

	public function setTokenRefreshHandler(callable $handler) {

		$this->refreshTokenHandler = $handler;
	}

	public function getAuthHTTPHeader() {

		// ensure we have the right bits of OAuth2 data available/previously set
		if (
			($this->accessToken === false) ||
			($this->tokenType === false) ||
			($this->expiresAt === false)
		) {
			// missing data
			throw new \Exception ('Unable to build header - missing OAuth2 token information');
		}

		if (time() >= ($this->expiresAt - self::OAUTH2_TOKEN_EXPIRY_WINDOW)) {
			// token is considered expired
			if ($this->refreshToken === false) {
				// we don't have a refresh token - can't build OAuth2 HTTP header
				return false;
			}

			// get new access token (will be stored in $this->accessToken)
			$tokenData = $this->getAccessTokenFromRefreshToken($this->refreshToken);

			// if callback handler defined for token refresh events call it now
			if ($this->refreshTokenHandler !== null) {
				// include the refresh token in call to handler
				$handler = $this->refreshTokenHandler;
				$handler($tokenData + ['refreshToken' => $this->refreshToken]);
			}
		}

		// return OAuth2 HTTP header as a name/value pair
		return [
			'Authorization',
			sprintf('%s %s',$this->tokenType,$this->accessToken)
		];
	}

	public function getAccessTokenFromAuthCode($code) {

		return $this->storeTokenData(
			parent::requestAccessTokenFromAuthCode(
				$code,
				$this->getAuthCredentialList()
			),
			true
		);
	}

	public function getAccessTokenFromRefreshToken($token) {

		return $this->storeTokenData(
			parent::requestAccessTokenFromRefreshToken(
				$token,
				$this->getAuthCredentialList()
			)
		);
	}

	private function getAuthCredentialList() {

		// all Google OAuth2 API requests need 'client id' and 'client secret' in their payloads
		return [
			'client_id' => $this->clientID,
			'client_secret' => $this->clientSecret
		];
	}

	private function storeTokenData(array $data,$hasRefreshKey = false) {

		// save values - false for any key(s) that don't exist
		$getValue = function($key) use ($data) {

			return (isset($data[$key])) ? $data[$key] : false;
		};

		$this->accessToken = $getValue('accessToken');
		$this->tokenType = $getValue('tokenType');
		$this->expiresAt = $getValue('expiresAt');

		if ($hasRefreshKey) {
			// only save refresh token if expecting it
			$this->refreshToken = $getValue('refreshToken');
		}

		// return $data for chaining
		return $data;
	}
}
