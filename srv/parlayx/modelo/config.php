<?php
    /************************************************************
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   *
    *   @version:       1.0                                     *
    *   @created:       04/02/2008                              *
    *   @copiright:     Copyright (c) 2009, SkyTel              *
    *   @description    Acceso a la configuraci�n del Sistema.  *
    ************************************************************/
		 
	//---- PACKAGES ----//	  
	define('access', true);
        //var_dump( $_SERVER['DOCUMENT_ROOT'] ); 
	define( 'ROOT', '/var/www/dev/ial/srv/parlayx/v1.0.1/' );
        $_SERVER['SERVER_NAME'] = '192.168.100.151';
	require_once '/var/www/dev/ial/srv/parlayx/v1.0.1/getPackage.php'; 	 
?>