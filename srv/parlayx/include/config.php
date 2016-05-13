<?php
    /************************************************************
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   *
    *   @version:       1.0                                     *
    *   @created:       04/02/2008                              *
    *   @copiright:     Copyright (c) 2009, SkyTel              *
    *   @description    Acceso a la configuraci�n del Sistema.  *
    ************************************************************/
	
	//---- Config File ----//
	//require_once $_SERVER['DOCUMENT_ROOT']."lib/lookandfeel/estilos.php";
	
	//---- SESSION ----//
	//session_name($_ESTILOP['session_name']);
	//session_start();
  
	//---- HEADERS ----//
	//header("Expires: 0");
	//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	 
	//---- PACKAGES ----//	  
	define('access', true);
	require_once '/var/www/nxt/ial/srv/parlayx/v1.0.1/getPackage.php'; 	 
?>