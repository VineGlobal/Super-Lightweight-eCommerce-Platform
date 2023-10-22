<?php 

$currentDir = __DIR__;

$files = glob($currentDir.'/cached-files/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}
?>