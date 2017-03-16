<?php
session_start();
require_once dirname(__FILE__).'/lib/GoogleClientApi/Google_Client.php';
require_once dirname(__FILE__).'/lib/GoogleClientApi/contrib/Google_AnalyticsService.php';

$scriptUri = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAccessType('online'); // default: offline
$client->setApplicationName('Mexican Cooking Network');
$client->setClientId('59559608010-21tmaio5qel8es88k15es28trq90tl2q.apps.googleusercontent.com');
$client->setClientSecret('7czxyLPXLqQvQrD4PG9KBo07');
$client->setRedirectUri($scriptUri);
$client->setDeveloperKey('6b507852d734ec292ecbcf0c66fc03fb3aff22db'); // API key

// $service implements the client interface, has to be set before auth call
$service = new Google_AnalyticsService($client);

if (isset($_GET['logout'])) { // logout: destroy token
     unset($_SESSION['token_ganalytics']);
    global $output_title, $output_body, $output_nav;
    $output_title = 'Analytics';
    $output_body = '<h1>You have been logged out.</h1>';
    $output_nav = '<li><a href="'.$scriptUri.'?login">Login</a></li>'."\n";
    include("output.php");
    die;
}

if (isset($_GET['login'])) {
    $authUrl = $client->createAuthUrl(); 
    header("Location: ".$authUrl);
}

if (isset($_GET['code'])) { // we received the positive auth callback, get the token and store it in session
    $client->authenticate();
    $_SESSION['token_ganalytics'] = $client->getAccessToken();
    header("Location: ".$scriptUri);
    die;
}

if (isset($_SESSION['token_ganalytics'])) { // extract token from session and configure client
    $token = $_SESSION['token_ganalytics'];
    $client->setAccessToken($token);
}

if (!$client->getAccessToken()) { // auth call to google
    global $output_title, $output_body, $output_nav;
    $output_title = 'Adwords';
    $output_body = '<h1>Login with your Google account</h1><p>When clicking on login, you are redirected to Google. Login with a Google account that has access to a Google Adsense account, otherwise an error will occur.</p><div class="alert alert-info">We do not store the login credentials nor the data being displayed. This is just a simple demo page.</div>';
    $output_nav = '<li><a href="'.$scriptUri.'?login">Login</a></li>'."\n";
    include("output.php");
    die;
}  
 


// http://code.google.com/apis/analytics/docs/mgmt/v3/mgmtReference.html#collection_webproperties
try {
    global $_params, $output_title, $output_body;
    $output_title = 'Analytics';
    $output_nav = '<li><a href="'.$scriptUri.'?logout">Logout</a></li>'."\n";
    $output_body = '<h1>Google Analytics Access demo</h1>
                    <p>The following domains are in your Google Analytics account</p><ul>';
    $props = $service->management_webproperties->listManagementWebproperties("~all");
    foreach($props['items'] as $item) {
        $output_body .= sprintf('<li>%1$s</li>', $item['name']);
    }
    $output_body .= '</ul>';
    include("output.php");
} catch (Exception $e) {
	die('<html><body><h1>An error occured: ' . $e->getMessage()."\n </h1></body></html>");
}


$projectId = '84210115';

// metrics
$_params[] = 'date';
$_params[] = 'date_year';
$_params[] = 'date_month';
$_params[] = 'date_day';
// dimensions
$_params[] = 'visits';
$_params[] = 'pageviews';
$_params[] = 'bounces';
$_params[] = 'entrance_bounce_rate';
$_params[] = 'visit_bounce_rate';
$_params[] = 'avg_time_on_site';

$from = date('Y-m-d', time()-2*24*60*60); // 2 days
$to = date('Y-m-d'); // today

$metrics = 'ga:visits,ga:pageviews,ga:bounces,ga:entranceBounceRate,ga:visitBounceRate,ga:avgTimeOnSite';
$dimensions = 'ga:date,ga:year,ga:month,ga:day';
$data = $service->data_ga->get('ga:'.$projectId, $from, $to, $metrics, array('dimensions' => $dimensions));

foreach($data['rows'] as $row) {
   $dataRow = array();
   foreach($_params as $colNr => $column)  {
	echo $column . ': '.$row[$colNr].' <br/>';
   }
   
   
}

?>