<?php         

class SimpleCache {
  // Properties
  public $name;
  

  // Methods
  function setName($name) {
    $this->name = $name;
  }
  function getName() {
    return $this->name;
  }
             


  function setCache($name,$content) {
			
			try {
			
				$cachefile =    (dirname(__FILE__) .'/cached-files/'.$name.'.php');
				$fp = fopen($cachefile, 'w');
			 
				if ($fp === false) {
					throw new Exception("Please set write permissions on the /cached-files folder<br/>");
				}  
				
				fwrite($fp, json_encode($content));
				fclose($fp);
			} catch (Exception $e) {
				// Handle the exception
				echo 'Error: ' . $e->getMessage();
			} 
	
            
  }
  
   function getCache($name) {             
            $cachefile =    (dirname(__FILE__) .'/cached-files/'.$name.'.php');  
		    if (file_exists($cachefile)) {		
				$fp = fopen($cachefile, 'r');
				$fileContents = fread($fp,filesize($cachefile));    
				fclose($fp);
				return json_decode($fileContents);         
			}
			
			return null;
  }     
}
          
  ?>    