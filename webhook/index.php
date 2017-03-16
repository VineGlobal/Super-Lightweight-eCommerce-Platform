<?php 

require("../phpfastcache.php");

$cache = phpFastCache();

$array = $cache->stats();

$cache->clean();
$array = $cache->stats();

?>