<?php
	require_once "lib/lookandfeel/estilos.php";
    require_once "lib/MobileDetect/Mobile_Detect.class.php";
	
	if($_ESTILOP["mobile_redirect"]){
            $detect = new Mobile_Detect();
            if ($detect->isMobile()){
               header("Location: Mobile");
            }
            else{
               header("Location: Login");
            }
        }
        else{
            header("Location: Login");
        }
?>