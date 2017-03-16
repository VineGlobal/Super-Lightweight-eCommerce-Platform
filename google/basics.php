<?php

die("SHOULD NOT BE CALLED")  ;
   error_reporting(E_ALL);
ini_set('display_errors', 1);
   
    session_start();   
  // include('GoogleAnalyticsAPI.class.php');
  
   include('GoogleSpreadsheetAPI.class.php');
 
	
$ga = new GoogleSpreadsheetAPI('service'); 
$ga->auth->setClientId('604518421806-4qqva19c2uj51a3r579imfp44d9v5hm9.apps.googleusercontent.com'); // From the APIs console
 
$ga->auth->setEmail('604518421806-4qqva19c2uj51a3r579imfp44d9v5hm9@developer.gserviceaccount.com'); // From the APIs console
 
$ga->auth->setPrivateKey('/home/jill/web/store-vineos-io/trunk/google/google_private_key/vineglobal-aafe2aeec6a6.p12'); // Path to the .p12 file
 
$auth = $ga->auth->getAccessToken();
 
print_r($auth);

// Try to get the AccessToken
if ($auth['http_code'] == 200) {
    $accessToken = $auth['access_token'];
    $tokenExpires = $auth['expires_in'];
    $tokenCreated = time();
} else {
    // error...
	die('AUTH FAILED');
} 

$ga->setAccessToken($accessToken);

$sheets = $ga->getSpreadsheets();
print_r($sheets);

//https://spreadsheets.google.com/feeds/worksheets/12Ljjd8dChaHq2YOExKXw77ri2UDgixQTVuZBgMY-o30/private/full?v=3.0'

$sheet = $ga->getSpreadsheet('https://spreadsheets.google.com/feeds/worksheets/12Ljjd8dChaHq2YOExKXw77ri2UDgixQTVuZBgMY-o30/private/full');
//print_r($sheet);


$params = array('gid'=>'1903438878', 'format'=> 'csv');
$sheet = $ga->getWorksheet('https://docs.google.com/spreadsheets/d/12Ljjd8dChaHq2YOExKXw77ri2UDgixQTVuZBgMY-o30/export?gid=1903438878&amp;format=csv',$params);
print_r($sheet);




die();
//$ga->setAccountId('ga:84210115'); 
 

// Set the default params. For example the start/end dates and max-results
$defaults = array(
    'start-date' => date('Y-m-d', strtotime('-2 month')),
    'end-date' => date('Y-m-d'),
);
$ga->setDefaultQueryParams($defaults);

// Example1: Get visits by date
$params = array(
    'metrics' => 'ga:visits',
    'dimensions' => 'ga:date',
);
$visits = $ga->query($params);
print_r($visits);die();

// Example2: Get visits by country
$params = array(
    'metrics' => 'ga:visits',
    'dimensions' => 'ga:country',
    'sort' => '-ga:visits',
    'max-results' => 30,
    'start-date' => '2013-01-01' //Overwrite this from the defaultQueryParams
); 
$visitsByCountry = $ga->query($params);

// Example3: Same data as Example1 but with the built in method:
$visits = $ga->getVisitsByDate();

// Example4: Get visits by Operating Systems and return max. 100 results
$visitsByOs = $ga->getVisitsBySystemOs(array('max-results' => 100));

// Example5: Get referral traffic
$referralTraffic = $ga->getReferralTraffic();

// Example6: Get visits by languages
$visitsByLanguages = $ga->getVisitsByLanguages();

// Load profiles
$profiles = $ga->getProfiles();
$accounts = array();
foreach ($profiles['items'] as $item) {
    $id = "ga:{$item['id']}";
    $name = $item['name'];
    $accounts[$id] = $name;
}
// Print out the Accounts with Id => Name. Save the Id (array-key) of the account you want to query data. 
// See next chapter how to set the account-id.
print_r($accounts);
die();
 
?>