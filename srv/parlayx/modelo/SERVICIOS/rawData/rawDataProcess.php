<?php

//---- CONFIG ----//
require_once '/var/www/dev/ial/srv/parlayx/v1.0.1/modelo/config.php';
include(ROOT . 'modelo/SERVICIOS/config.php');

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

_doRawData();

function _doRawData() {
    $dat_historicopayment = new Base_Dat_HistoricoPayment();
    $Correo = new Correo();
    $logger = new logger('/var/log/nxt/ial/crn/rawData/');
    $logger->_saveLog("Iniciando proceso|_doRawData-----------------------------");

    $logger->_saveLog("OP|Obteniendo registros");
    $data = $dat_historicopayment->getHistoricoCobro();
    if ($data[1] != '') {
        $logger->_saveLog("OP|" . sizeof($data[0]) . " registros|ID=" . $data[1]);
        $logger->_saveLog("BD|Actualizando estatus de registros a atendido");
        if ($dat_historicopayment->updateRegsProcessed($data[1])) {
            $logger->_saveLog("BD|Registros actualizado a atendido correctamente");

            /*             * *********** Generar rawdata *********** */
            $logger->_saveLog("OP|Generando rawdata file");
            $fileName = "rawData_" . date("Ymd") . ".txt";
            $fileName = str_replace(' ', '', $fileName);
            $txtfile = "/var/www/dev/ial/srv/parlayx/v1.0.1/modelo/SERVICIOS/rawData/Files/" . $fileName;
            $fp = fopen($txtfile, "w+");
            if (!is_resource($fp))
                $logger->_saveLog("ERR|OP|Generando rawdata file");
            foreach ($data[0] as $key => $value) {
                fwrite($fp, $value['PTN'] . "|iAlarm|MO|" . $value['CONTRATO'] . "|". $value['MONTO'] . "|". $value['DATE'] . "|" . $value['TIME'] . "|"  . $value['ESTATUS']. "|" . $value['TRANSACTION']  . "\n");
            }
            fclose($fp);
            $logger->_saveLog("OP|Rawdata file generado correctamente|fileName=".$fileName);
            /*             * *************************************** */

            /*             * *********** Enviar archivo por correo *********** */
            $logger->_saveLog("OP|Enviando rawdata file por correo");            
            require_once("config.php");            
            $infoCorreo = array();
            $infoCorreo['subject'] = $_rwConfig['subject'] . $fileName;
            $infoCorreo['remit'] = $_rwConfig['remit'];
            $infoCorreo['body'] = $_rwConfig['body'];
            $infoCorreo['alt_body'] = $_rwConfig['alt_body'];
            $infoCorreo['nombre'] = $_rwConfig['nombre'];
            $infoCorreo['correo'] = 'luis.montes@webmaps.com.mx';
            $infoCorreo['contacto'] = 'luis.montes@webmaps.com.mx';
            
            $adresses = explode("|",$_rwConfig['target']);
            foreach ($adresses as $key=>$value){
                $Correo->AddAddress($value,$value);
            }
            
            //if($Correo->sendMailReports($infoCorreo))
            if ($Correo->sendMailAttachfile($infoCorreo, $txtfile)) {
                $logger->_saveLog("OP|Rawdata file enviado correctamente|target=".$_rwConfig['target']);
            } else {
                $logger->_saveLog("ERR|OP|Enviando rawdata file por correo");
            }

            /*             * *************************************** */
        } else {
            $logger->_saveLog("ERR|BD|Actualizando estatus de registros a atendido|Query=" . $dat_historicopayment->_query);
        }
    } else {
        $logger->_saveLog("OP|No se encontraron registros de cobro en BD");
    }
    $logger->_saveLog("Proceso terminado|_doRawData-----------------------------");
    $logger->_saveLog(" ");
}
