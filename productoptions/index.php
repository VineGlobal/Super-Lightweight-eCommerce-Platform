<?php

header('Content-Type: application/json');

if (!isset($_GET['parentsku'])) {
	echo 'SKU NOT FOUND';die(); 
}

$parentsku = $_GET['parentsku'];


require("../phpfastcache.php"); 

include('../GoogleSpreadsheetAPI.class.php');  
/** google drive credentials **/ 
$ini = parse_ini_file("../app_config.ini");  
$timeout 	= $ini['cachettl'];

// simple Caching with:
$cache = phpFastCache();
 

$products_options = $cache->get("products_options_cache");

if($products_options == null) {  
	 
		
$ga = new GoogleSpreadsheetAPI('service'); 
$ga->auth->setClientId('604518421806-4qqva19c2uj51a3r579imfp44d9v5hm9.apps.googleusercontent.com'); // From the APIs console
$ga->auth->setEmail('604518421806-4qqva19c2uj51a3r579imfp44d9v5hm9@developer.gserviceaccount.com'); // From the APIs console
$ga->auth->setPrivateKey('/home/jill/web/store-vineos-io/trunk/google/google_private_key/vineglobal-aafe2aeec6a6.p12'); // Path to the .p12 file
 
$auth = $ga->auth->getAccessToken();
 
//print_r($auth);

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
	
	
	
	$params = array('gid'=>'0', 'format'=> 'csv');
	$temp_products_options = $ga->getWorksheet('https://docs.google.com/spreadsheets/d/12Ljjd8dChaHq2YOExKXw77ri2UDgixQTVuZBgMY-o30/export',$params);
	 
	$products_options		= array();	
	foreach ($temp_products_options as $product_option) {
		//print_r($product_option);
		/** simple type and not empty **/
		if ($product_option['producttype'] == 'Simple' && trim($product_option['parentsku']) != ''  ) {
			$products_options[$product_option['parentsku']][] = $product_option;
		} 
	}
	
	 
	$cache->set("products_options_cache",$products_options , $timeout);
	//echo " --> NO CACHE ---> products_cache API RUN FIRST TIME ---> ";

} else {
	//echo " --> USE CACHE --> products_cache Visitors FROM CACHE ---> ";
}

echo json_encode(array('productoptions' => $products_options[$parentsku]));

?>