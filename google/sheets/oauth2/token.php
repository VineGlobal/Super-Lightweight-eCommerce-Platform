<?php
namespace OAuth2;


class Token {

	const HTTP_CODE_OK = 200;
	const GRANT_TYPE_AUTH = 'authorization_code';
	const GRANT_TYPE_REFRESH = 'refresh_token';

	protected $tokenURL;
	protected $redirectURL;


	public function __construct($tokenURL,$redirectURL) {

		$this->tokenURL = $tokenURL;
		$this->redirectURL = $redirectURL;
	}

	public function getAccessTokenFromAuthCode($code) {

		$this->requestAccessTokenFromAuthCode($code);
	}

	public function getAccessTokenFromRefreshToken($token) {

		$this->requestAccessTokenFromRefreshToken($token);
	}

	protected function requestAccessTokenFromAuthCode($code,array $parameterList = []) {

		// build POST parameter list
		$POSTList = [
			'code' => $code,
			'grant_type' => self::GRANT_TYPE_AUTH,
			'redirect_uri' => $this->redirectURL
		];

		// add additional parameters
		$POSTList += $parameterList;

		// make request, parse JSON response
		$dataJSON = $this->HTTPRequestGetJSON(
			$this->HTTPRequest($POSTList)
		);

		// ensure required keys exist
		// note: key 'expires_in' is recommended but not required in RFC 6749
		foreach (['access_token','expires_in','token_type'] as $key) {
			if (!isset($dataJSON[$key])) {
				throw new \Exception('OAuth2 access token response expected [' . $key . ']');
			}
		}

		// return result - include refresh token if given
		$tokenData = $this->buildBaseTokenReturnData($dataJSON);
		if (isset($dataJSON['refresh_token'])) {
			$tokenData['refreshToken'] = $dataJSON['refresh_token'];
		}

		return $tokenData;
	}

	protected function requestAccessTokenFromRefreshToken($token,array $parameterList = []) {

		// build POST parameter list
		$POSTList = [
			'grant_type' => self::GRANT_TYPE_REFRESH,
			'refresh_token' => $token
		];

		// add additional parameters
		$POSTList += $parameterList;

		// make request, parse JSON response
		$dataJSON = $this->HTTPRequestGetJSON(
			$this->HTTPRequest($POSTList)
		);

		// ensure required keys exist
		foreach (['access_token','expires_in','token_type'] as $key) {
			if (!isset($dataJSON[$key])) {
				throw new \Exception('OAuth2 refresh access token response expected [' . $key . ']');
			}
		}

		// return result
		return $this->buildBaseTokenReturnData($dataJSON);
	}

	private function HTTPRequest(array $POSTList) {

		$curlConn = curl_init();

		curl_setopt_array(
			$curlConn,[
				CURLOPT_HEADER => false,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $POSTList,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => $this->tokenURL
			]
		);

		// make request, close connection
		$responseBody = curl_exec($curlConn);
		$responseHTTPCode = curl_getinfo($curlConn,CURLINFO_HTTP_CODE);
		curl_close($curlConn);

		// return HTTP code and response body
		return [$responseHTTPCode,$responseBody];
	}

	private function HTTPRequestGetJSON(array $responseData) {

		list($HTTPCode,$body) = $responseData;

		if ($HTTPCode != self::HTTP_CODE_OK) {
			// request error
			throw new \Exception('OAuth2 request access token failed');
		}

		// convert JSON response to array
		$JSON = json_decode($body,true);
		if ($JSON === null) {
			// bad JSON data
			throw new \Exception('OAuth2 request access token malformed response');
		}

		return $JSON;
	}

	private function buildBaseTokenReturnData(array $dataJSON) {

		return [
			'accessToken' => $dataJSON['access_token'],
			'expiresAt' => time() + intval($dataJSON['expires_in']), // convert to unix timestamp
			'tokenType' => $dataJSON['token_type']
		];
	}
}
