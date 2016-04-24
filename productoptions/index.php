<?php

header('Content-Type: application/json');

if (!isset($_GET['parentsku'])) {
	echo 'SKU NOT FOUND';die(); 
}

$parentsku = $_GET['parentsku'];


require("../phpfastcache.php");
 
// Zend library include path
set_include_path("$_SERVER[DOCUMENT_ROOT]/ZendGdata-1.12.11/library");
     
include("../Google_Spreadsheet.php");  
/** google drive credentials **/ 
$ini = parse_ini_file("../app_config.ini"); 
$u 			= $ini['user'];
$p 			= $ini['password'];
$timeout 	= $ini['cachettl'];;


// simple Caching with:
$cache = phpFastCache();
 

$products_options = $cache->get("products_options_cache");

if($products_options == null) {  
	
	if (!isset($ss)) {
		$ss = new Google_Spreadsheet($u,$p);
		$ss->useSpreadsheet($ini['spreadsheet']);
	} 
	
	// specify the Google Sheet "product_catalog", if not setting worksheet, "Sheet1" is assumed
	$ss->useWorksheet("product_catalog");
	$temp_products_options 	= $ss->getRows(); 
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