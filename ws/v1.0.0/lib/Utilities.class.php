<?php

   class Utilities{
      
	  function __construct(){}
	  
	  public function limpiaPTN($PTN = ''){
	     $PTN = str_replace("-", "", $PTN);
		 $PTN = str_replace(" ", "", $PTN);
		 $PTN = str_replace("(", "", $PTN);
		 $PTN = str_replace(")", "", $PTN);
		 $PTN = str_replace("_", "", $PTN);
		 $PTN = str_replace("+", "", $PTN);
		 
		 return $PTN;
	  }
	  
	  public function saveToLog($msg = "", $fileName = "log.log"){
		  $file  = fopen($fileName, "a+");
		  fwrite($file, date("d-m-Y H:i:s").":".$msg."\n");
		  fclose($file);
	  }
	  
   }

?>