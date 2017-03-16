<?php 

 error_reporting(E_ALL);
ini_set('display_errors', 1);
  

require_once 'src/Google/autoload.php'; // or wherever autoload.php is located

  $client = new Google_Client();
  $client->setApplicationName("Client_Library_Examples");
  $client->setDeveloperKey("AIzaSyBj7JRln_EDd1bhxoh_IsetyMb5OdeTdVU"); //vine global

  $service = new Google_Service_Sheets($client);
  $optParams = array('filter' => 'free-ebooks');
  $results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);

  foreach ($results as $item) {
    echo $item['volumeInfo']['title'], "<br /> \n";
  }
  
  
  
  
 //use Google\Spreadsheet\DefaultServiceRequest;
 //use Google\Spreadsheet\ServiceRequestFactory;

$serviceRequest = new Google_Spreadsheet_DefaultServiceRequest($client);
ServiceRequestFactory::setInstance($serviceRequest);


$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
  
  
  
  
  
  
  
  
  
  
  
  ?>