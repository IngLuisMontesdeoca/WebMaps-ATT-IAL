<?php
/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>      	        	*
 *   @version:       1.0                                     					*
 *   @created:       12/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Servicio que lleva a cabo el envio de mensajes              *
 * ****************************************************************************** */

error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_WARNING));
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

header('Content-Type: text/html; charset=UTF-8');
//---- CONFIG ----//
require_once '/var/www/dev/ial/srv/parlayx/v1.0.1/modelo/config.php';
include(ROOT . 'modelo/SERVICIOS/sendSms/sendSmsProcess.php');
include(ROOT . 'modelo/SERVICIOS/config.php');

//---- PAQUETES ----//
//---- BD ----//
Package::usePackage('BD');
//---- Tables ----//
Package::import('tables');
//---- Classes ----//
Package::usePackage('Classes');
//---- Utilities ----//
Package::import('Utilities');
//---- Utilities ----//
Package::import('Debug');

///Validar si se esta accediendo al script via ajax ó web
if (Debug::ajaxRequest() || isset($_SERVER['HTTP_USER_AGENT'])) {
    die('Acceso denegado!!!');
    exit();
}

//---- Parlayx ----//-
Package::import('Parlayx');
//---- WS ----//-
Package::import('ws');

$sendProcess = new sendProcess($_WS);
$sendProcess->run();