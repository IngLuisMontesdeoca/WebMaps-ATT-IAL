<?php

/* * ******************************************************************************
 *                                                           					*
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>   					*
 *   @version:       1.0                                     					*
 *   @created:       25/11/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Exportar un reporte en formato xls								*
 * ****************************************************************************** */

//---- CONFIG ----//
ini_set('memory_limit', '1500M');
ini_set("max_execution_time", "120");
require_once '../../config.php';

$fileName = trim($_SESSION['TIPOREPORTE']) . "_" . date("YmdHi");
$fileName = str_replace(' ', '', $fileName);


//require_once '../../lib/PHP/kexcel/kexcel.php';

if ($_GET['f'] == 'dvBtnExportXLS') {
    //header("Content-type: application/vnd.ms-excel");
    //header("Content-Disposition: attachment; filename=" . $fileName . ".xls");  
    require_once("../../../lib/PHP/excel.php");
    require_once("../../../lib/PHP/excel-ext.php");
}else{
    require_once("../../../lib/PHP/txt.php");
}

//---- BD ----//
Package::usePackage('BD');
//---- Tables ----//
Package::import('tables');
//---- Classes ----//
Package::usePackage('Classes');
//---- template ----//
Package::import('Template');
//---- debig ----//
Package::import('Debug');

//---- Validar session existente ----//
if (!(isset($_SESSION['idUsuario']))) {
    echo utf8_encode('SIN SESION');
    exit();
}

if (isset($_GET['fechaFin']))
    $fFin = $_GET['fechaFin'];
else {
    echo $zero;
    exit();
}

if (isset($_GET['fechaIni']))
    $fIni = $_GET['fechaIni'];
else {
    echo $zero;
    exit();
}

$tipoReporte = $_GET['tipoReporte'];
//---- Registro en el Activity Log----//
$baseActivityLog = new Base_Dat_ActivityLog();
$baseActivityLog->setPk(0);
$baseActivityLog->c_activitylog_desc = 'Exportar reporte de ' . $_SESSION['TIPOREPORTE'] . ',Archivo=' . $fileName;
$baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
$baseActivityLog->n_activity_id = 28;
$baseActivityLog->n_accesslog_id = (int) $_SESSION['idLog'];
$isSave = $baseActivityLog->save() ? TRUE : FALSE;

if($tipoReporte == "Message"){
    $baseDatHistoricoPayment = new Base_Dat_HistoricoMensajes();
    $data = $baseDatHistoricoPayment->getHistoricoMensajesExport($fIni,$fFin);
}else{
    $baseDatHistoricoPayment = new Base_Dat_HistoricoPayment();
    $data = $baseDatHistoricoPayment->getHistoricoCobroExport($fIni,$fFin);
}

if ($_GET['f'] == 'dvBtnExportXLS') {
    createExcel($fileName . ".xls", $data);
} else {
    if( sizeof($data) > 0){
        createTxt($fileName . ".txt", $data,$tipoReporte);
    }
}

/*
  unset($_SESSION['EXCEL']);
  unset($_SESSION['TIPOREPORTE']); */
exit;
?>	








