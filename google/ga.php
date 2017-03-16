<?php
          die("SHOULD NOT BE CALLED")  ;
   error_reporting(E_ALL);
ini_set('display_errors', 1);
 session_start();	 	   
require_once 'Google/Client.php';
require_once 'Google/Service/Analytics.php';	 	
/************************************************	
 The following 3 values an befound in the setting	
 for the application you created on Google 	 	
 Developers console.	 	 Developers console.
 The Key file should be placed in a location	 
 that is not accessable from the web. outside of 
 web root.	 	 web root.
 	 	 
 In order to access your GA account you must	  
 Add the Email address as a user at the 	
 ACCOUNT Level in the GA admin. 	 	
 ************************************************/
$client_id = '59559608010-c65jr59pdmce1edlsaj40s9di7bsbts4.apps.googleusercontent.com';
$Email_address = '59559608010-c65jr59pdmce1edlsaj40s9di7bsbts4@developer.gserviceaccount.com';	 
$key_file_location = '/home/jill/web/store-vineos-io/trunk/google/google_analytics_certs/35e6cc91c40b34b0f7f44220c72661c570bdbafd-privatekey.p12';	 	
$client = new Google_Client();	 	
$client->setApplicationName("Client_Library_Examples");
$key = file_get_contents($key_file_location);	 
// seproate additional scopes with a comma	 
$scopes ="https://www.googleapis.com/auth/analytics.readonly"; 	
$cred = new Google_Auth_AssertionCredentials(	 
 $Email_address,	 	 
 array($scopes),	 	
 $key	 	 
 );	 	
$client->setAssertionCredentials($cred);
if($client->getAuth()->isAccessTokenExpired()) {	 	
 $client->getAuth()->refreshTokenWithAssertion($cred);	 	
}	 	
$service = new Google_Service_Analytics($client);
$accounts = $service->management_accountSummaries->listManagementAccountSummaries();
//calulating start date	 
$date = new DateTime(date("Y-m-d"));	 
$date->sub(new DateInterval('P10D'));	 
//Adding Dimensions
$params = array('dimensions' => 'ga:userType');	
// requesting the data	
$data = $service->data_ga->get("ga:78110423", $date->format('Y-m-d'), date("Y-m-d"), "ga:users,ga:sessions", $params );	 
?><html>	 
<?php echo $date->format('Y-m-d') . " - ".date("Y-m-d"). "\n";?>	
<table>	 
<tr>	 
<?php	 
//Printing column headers
foreach($data->getColumnHeaders() as $header){
 print "<td>".$header['name']."</td>"; 	 	
}	 	
?>	 	
</tr>	 	
<?php	 	
//printing each row.
foreach ($data->getRows() as $row) { 	 	
 print "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td></tr>"; 	 
}	 
//printing the total number of rows
?>	 	
<tr><td colspan="2">Rows Returned <?php print $data->getTotalResults();?> </td></tr>	 
</table>	 
</html>	 