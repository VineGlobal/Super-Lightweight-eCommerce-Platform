<?php 

/** this is the primary "webhook" file that receives the updates from Google Spreadsheet"
require("../phpfastcache.php");

$cache = phpFastCache();

$array = $cache->stats();
print_r($array);

$cache->clean();
echo '---after clean--';


$array = $cache->stats();
print_r($array);

echo 'VineOS Webhook: Cache Refreshed';

?>