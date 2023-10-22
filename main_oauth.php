<?php
require("simpleCache.php");
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

$sc = new SimpleCache();

$store_config   =  $sc->getCache('store_config_cache');

if($store_config == null) {   
    
	if (!isset($ga)) { 
		$ga = set_google_auth($ini); 
	} 
	    
	$params = array('gid'=>$ini['google_tab_store_config'], 'format'=> 'csv');
	$_config = $ga->getWorksheet($ini['google_worksheet_url'],$params);
	          
	$store_config = array();
    $_config = (array) $_config;
	foreach ($_config as $arr) {  
	 	$store_config[$arr['key']] = str_replace("'", "", $arr['value']);
	}                
                      
    $sc->setCache('store_config_cache',$store_config);     
    $store_config   =  $sc->getCache('store_config_cache');

}   

 
$products = $sc->getCache("products_cache");    
       
         
if($products == null) {      
	if (!isset($ga)) { 
		$ga = set_google_auth($ini); 
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
	 
	$sc->setCache("products_cache",$products);  
    $products = $sc->getCache("products_cache");    //must call this line here, keep     
}  
    

$language 		= get_language();
$translations 	= $sc->getCache("translations_cache_".$language);

if($translations == null) {
	
	if (!isset($ga)) { 
		$ga = set_google_auth($ini); 
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
	$sc->setCache("translations_cache_".$language,$translations);
    $translations     = $sc->getCache("translations_cache_".$language);    

}     


$types = $sc->getCache("types_cache");

if($types == null) {
		
	if (!isset($ga)) { 
		$ga = set_google_auth($ini); 
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
	 
	 
	$sc->setCache("types_cache",$types );
	$types = $sc->getCache("types_cache");
}      
 

function _m($key) {
	global $store_config;
               
	if (isset($store_config->{$key})) {
		return $store_config->{$key};
	} else {
		return $key;
	}
}


function _l($key) {
	global $translations;
	if (isset($translations->{$key})) {
		return $translations->{$key};	
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
