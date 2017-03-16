<?php
return [
	'OAuth2URL' => [
		'base' => 'https://accounts.google.com/o/oauth2',
		'auth' => 'auth', // for Google authorization
		'token' => 'token', // for OAuth2 token actions
		'redirect' => 'https://domain.com/oauth'
	],

	'clientID' => '',
	'clientSecret' => '',
	'tokenDataFile' => '.tokendata'
];
