<?php

require("phpfastcache.php");

// Zend library include path
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . "ZendGdata-1.12.11/library/");
     
include("Google_Spreadsheet.php");  
/** google drive credentials **/ 
$ini = parse_ini_file("app_config.ini"); 
$u 			= $ini['user'];
$p 			= $ini['password'];
$timeout 	= $ini['cachettl'];

// simple Caching with:
$cache = phpFastCache();

$store_config = $cache->get("store_config_cache");

if($store_config == null) {
	
	
	$ss = new Google_Spreadsheet($u,$p);
	$ss->useSpreadsheet($ini['spreadsheet']); 	
	
	$ss->useWorksheet("store_config"); 
	$_config = $ss->getRows(); 
	
	$store_config = array();
	foreach ($_config as $arr) { 
	 	$store_config[$arr['key']] = $arr['value'];
	 
	}
	 
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("store_config_cache",$store_config , $timeout);
	///echo " --> NO CACHE ---> store_config_cache API RUN FIRST TIME ---> ";

} else {
///	echo " --> USE CACHE --> store_config_cache  FROM CACHE ---> ";
}

//print_r($store_config);


// Try to get $products from Caching First
// product_page is "identity keyword";
$products = $cache->get("products_cache");

if($products == null) {  
	
	if (!isset($ss)) {
		$ss = new Google_Spreadsheet($u,$p);
		$ss->useSpreadsheet($ini['spreadsheet']);
	} 
	
	// if not setting worksheet, "Sheet1" is assumed
	$ss->useWorksheet("product_catalog");
	$temp_products 	= $ss->getRows(); 
	$products 		= array();	
	foreach ($temp_products as $product) {
		/** Parent, and Simple with no parentsku **/
	 
		if ($product['producttype'] == 'Parent' || ($product['producttype'] == 'Simple' && $product['parentsku'] == ''  )) {
			$products[] = $product;
		}
	}
	
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("products_cache",$products , $timeout);
	//echo " --> NO CACHE ---> products_cache API RUN FIRST TIME ---> ";

} else {
	//echo " --> USE CACHE --> products_cache Visitors FROM CACHE ---> ";
}
 


// use your products here or return it;
//print_r($products); 
//die();

$language 	= get_language();

$translations = $cache->get("translations_cache_".$language);

if($translations == null) {
			
	if (!isset($ss)) {
		$ss = new Google_Spreadsheet($u,$p);
		$ss->useSpreadsheet($ini['spreadsheet']); 	
	}	

	$ss->useWorksheet($language); 
	$_trans = $ss->getRows(); 
	
	$translations = array();
	foreach ($_trans as $arr) { 
	 	$translations[$arr['key']] = $arr['value'];
	}
	 
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("translations_cache_".$language,$translations , $timeout);
	//echo " --> NO CACHE ---> store_config_cache API RUN FIRST TIME ---> ";

} else {
	//echo " --> USE CACHE --> translations  FROM CACHE ---> ";
}

//print_r( $translations);


$types = $cache->get("types_cache");

if($types == null) {
	
	if (!isset($ss)) {
		$ss = new Google_Spreadsheet($u,$p);
		$ss->useSpreadsheet($ini['spreadsheet']); 	
	}
	
	$ss->useWorksheet("types"); 
	$types = $ss->getRows(); 
	
	$categories 		= array();
	$product_types 		= array();
	$colors				= array();
	foreach ($types as $data) { 
	 	$categories[$data['categories']] 		= _l($data['categories']);
		
		if (isset($data['producttype']) && $data['producttype'] != "") {
			$product_types[] 	= $data['producttype'];	
		}
		
		if (isset($data['colors']) && $data['colors'] != "") {
			$colors[]			= $data['colors'];
		}
	 
	}
	
	$types['categories'] 		= $categories;
	$types['product_types'] 	= $product_types;
	$types['colors'] 			= $colors;
	 
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("types_cache",$types , $timeout);
	
} else {
///	echo " --> USE CACHE --> store_config_cache  FROM CACHE ---> ";
}

//print_r($types);


function _m($key) {
	global $store_config;
	if (isset($store_config[$key])) {
		return $store_config[$key];
	} else {
		return $key;
	}
}


function _l($key) {
	global $translations;
	if (isset($translations[$key])) {
		return $translations[$key];	
	} else {
		return $key;
	}
	
}

function get_language() {
	$lang 	= 'en';		
	$domain = explode('-',$_SERVER['SERVER_NAME']);
	
	if (isset($domain[1])) {
		$parts 	= explode('.',$domain[1]);
		$lang 	= $parts[0];
	}
	
	return $lang;	
} 


?>
