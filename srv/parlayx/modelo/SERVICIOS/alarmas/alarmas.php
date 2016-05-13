<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>      	        	*
 *   @version:       1.0                                     					*
 *   @created:       12/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Servicio que lleva a cabo el procesamiento de alarmas             *
 * ****************************************************************************** */

error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_WARNING));
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//
//---- CONFIG ----//
require_once '/var/www/dev/ial/srv/parlayx/v1.0.1/modelo/config.php';
include(ROOT.'modelo/SERVICIOS/alarmas/alarmasProcess.php');
include(ROOT.'modelo/SERVICIOS/config.php');

//---- PAQUETES ----//
//---- BD ----//
Package::usePackage('BD');
//---- Tables ----//
Package::import('tables');
//---- Classes ----//
Package::usePackage('Classes');
//---- Debug ----//
Package::import('Debug');
//---- Debug ----//
Package::import('Utilities');


///Validar si se esta accediendo al script via ajax ó web
if (Debug::ajaxRequest() || isset($_SERVER['HTTP_USER_AGENT'])) {
    die('Acceso denegado!!!');
    exit();
}

//---- Parlayx ----//-
Package::import('Parlayx');
    //---- WS ----//-
Package::import('ws');

///Instancias	  
$alarmasProcess = new alarmasProcess($_WS);
$alarmasProcess->alarmaStartProcess();

?>