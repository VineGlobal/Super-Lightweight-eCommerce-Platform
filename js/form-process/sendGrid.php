<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  
class Mail {
  
  private $sendGridAPIKey 	= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
  private $url 				= 'https://api.sendgrid.com/'; 
  private $template_id 		= '1';
  public $fromEmail         = "admin@email.com";
  public $fromName          = "Support";  
  public $toEmail;
  public $toName;
  public $text 			;
  public $html 			;
  public $subject		;


  function send() {

  		if ( ! defined('CURL_SSLVERSION_TLSv1_2')) {
			define('CURL_SSLVERSION_TLSv1_2', 6);
		}

  		$params = array(
	    'to'        => $this->toEmail,
	    'toname'    => $this->toName,
	    'from'      => $this->fromEmail,
	    'fromname'  => $this->fromName,
	    'subject'   => $this->subject,
	    'text'      => $this->text,
	    'html'      => $this->html,
	 //   'x-smtpapi' => json_encode($js),
	  );

	$request =  $this->url.'api/mail.send.json';

	// Generate curl request
	$session = curl_init($request);
	// Tell PHP not to use SSLv3 (instead opting for TLS)
	curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->sendGridAPIKey));
	// Tell curl to use HTTP POST
	curl_setopt ($session, CURLOPT_POST, true);
	// Tell curl that this is the body of the POST
	curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
	// Tell curl not to return headers, but do return the response
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

	// obtain response
	$response = curl_exec($session);
	curl_close($session);

	// print everything out
	//print_r($response);
	return true;

  }
}

