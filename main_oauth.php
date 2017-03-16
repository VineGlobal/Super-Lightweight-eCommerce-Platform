<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

     
require("phpfastcache.php");
include('GoogleSpreadsheetAPI.class.php');
 
 function set_google_auth($ini) {
	
	$ga = new GoogleSpreadsheetAPI('service'); 
	$ga->auth->setClientId($ini['google_client_id']); 
	$ga->auth->setEmail($ini['google_email']);  
	$ga->auth->setPrivateKey($ini['google_private_key']); 
	 
	$auth = $ga->auth->getAccessToken(); 
	
	// Try to get the AccessToken
	if ($auth['http_code'] == 200) {
	    $accessToken 	= $auth['access_token'];
	    $tokenExpires 	= $auth['expires_in'];
	    $tokenCreated 	= time();
	} else {
	    // error...
		die('AUTH FAILED');
	} 
	
	$ga->setAccessToken($accessToken);
	return $ga;
 }    

 //** fetch the ini config variables **/ 
$ini         = parse_ini_file("app_config.ini");  
 
$timeout 	= $ini['cachettl'];

// simple Caching with:
$cache 			= phpFastCache();

$store_config 	= $cache->get("store_config_cache");

if($store_config == null) {
	
	if (!isset($ga)) { 
		$ga = set_google_auth($ini); 
	} 
	
	$params = array('gid'=>$ini['google_tab_store_config'], 'format'=> 'csv');
	$_config = $ga->getWorksheet($ini['google_worksheet_url'],$params);
	
	$store_config = array();
	foreach ($_config as $arr) { 
	 	$store_config[$arr['key']] = $arr['value'];
	}
	 
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("store_config_cache",$store_config , $timeout);

} else {
///	echo " --> USE CACHE --> store_config_cache  FROM CACHE ---> ";
}     

// Try to get $products from Caching First   
$products = $cache->get("products_cache");

if($products == null) {  
	
	if (!isset($ga)) { 
		$ga = set_google_auth(); 
	} 
	
	$params = array('gid'=>$ini['google_tab_product_catalog'], 'format'=> 'csv');
	$temp_products = $ga->getWorksheet($ini['google_worksheet_url'],$params);
	 
	$products 		= array();	
	foreach ($temp_products as $product) {
		/** Parent, and Simple with no parentsku **/
	 
		if ($product['producttype'] == 'Parent' || ($product['producttype'] == 'Simple' && $product['parentsku'] == ''  )) {
			$products[] = $product;
		}
	}
	
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("products_cache",$products , $timeout);
	 

} else {
	//echo " --> USE CACHE --> products_cache Visitors FROM CACHE ---> ";
}
 

$language 		= get_language();
$translations 	= $cache->get("translations_cache_".$language);

if($translations == null) {
	
	if (!isset($ga)) { 
		$ga = set_google_auth(); 
	} 		
	
	if ($language == 'es') {
		$params = array('gid'=>$ini['google_tab_es'], 'format'=> 'csv');
		$_trans = $ga->getWorksheet($ini['google_worksheet_url'],$params);
	}
	else if ($language == 'zh') {
		$params = array('gid'=>$ini['google_tab_zh'], 'format'=> 'csv');
		$_trans = $ga->getWorksheet($ini['google_worksheet_url'],$params);
	} 
	 else {
		$params = array('gid'=>$ini['google_tab_en'], 'format'=> 'csv');
		$_trans = $ga->getWorksheet($ini['google_worksheet_url'],$params);
	}
	
	$translations = array();
	foreach ($_trans as $arr) { 
	 	$translations[$arr['key']] = $arr['value'];
	}
	 
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("translations_cache_".$language,$translations , $timeout);
	 

} else {
	//echo " --> USE CACHE --> translations  FROM CACHE ---> ";
}     

$types = $cache->get("types_cache");

if($types == null) {
		
	if (!isset($ga)) { 
		$ga = set_google_auth(); 
	} 
	
	$params = array('gid'=>$ini['google_tab_types'], 'format'=> 'csv');
	$types = $ga->getWorksheet($ini['google_worksheet_url'],$params);	 
	
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
