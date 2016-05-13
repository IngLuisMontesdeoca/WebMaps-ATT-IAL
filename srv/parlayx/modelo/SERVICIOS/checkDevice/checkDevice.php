<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>      	        	*
 *   @version:       1.0                                     					*
 *   @created:       22/04/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Servicio que verifica si los equpos son nextel             *
 * ****************************************************************************** */

error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED));
//---- CONFIG ----//
require_once '/var/www/dev/ial/srv/parlayx/v1.0.1/modelo/config.php';
include(ROOT.'modelo/SERVICIOS/checkDevice/processCheckDevice.php');
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

///Validar si se esta accediendo al script via ajax o web
if (Debug::ajaxRequest() || isset($_SERVER['HTTP_USER_AGENT'])) {
    die('Acceso denegado!!!');
    exit();
}

//---- Parlayx ----//-
Package::import('Parlayx');
    //---- WS ----//-
Package::import('ws');

$logger = new logger($_WS['checkDevice']['logPath']);

$processCheckDevice = new processCheckDevice($_WS);

$logger->_saveLog("Iniciando proceso|Main-----------------------------");
$processCheckDevice->checkDeviceStartProcess();
$logger->_saveLog("Proceso terminado|Main-----------------------------");
$logger->_saveLog(" ");