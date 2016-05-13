<?php
/* * ******************************************************************************
 *   @autor:         (JC) Jose Chavez <jose.chavez@webmaps.com.mx>               *
 *   @updater:                                                                   *
 *   @version:       1.0                                                         *
 *   @created:       25/03/2014                                                  *
 *   @copiright:     Copyright (c) 2014, WebMaps                                 *
 *   @description:   Web Services iAlarm                                         *
 *   @notes:         SMS CallBack - Nextel                                       *
 * ****************************************************************************** */

require_once 'lib/nusoap/nusoap.php';
require_once 'lib/Base.class.php';
require_once 'lib/Utilities.class.php';
require_once 'Parlayx/logger.class.php';
require_once 'Parlayx/parlayX.class.php';
require_once 'Parlayx/parlayXProcess.class.php';
/*
  require_once 'Wilaen/Wilaen.class.php';
  require_once 'Wilaen/Wilaen.php';
 */

function notifySmsReception($correlator, $message) {
    //$file = fopen('SCReception.log', 'a+');
    $_path = '/var/www/pro/ial/call/';
    require_once $_path.'Parlayx/configWS.php';
    $logger = new logger('/var/log/nxt/ial/ws/SCReception/');
    $utilities = new Utilities();
    $parlayXProcess = new parlayXProcess($_WS,$_path);
    $Base = new Base();
    $count = 0;
    $response = "O";
    $ptn = "";
    $datos = "|Correlator: " . $correlator;
    foreach ($message as $msg => $valor) {
        $datos.= " | " . $msg . ": " . $valor;
        if ($count == 1)
            $ptn = $valor;
        $count++;
    }
    $idHandset = $Base->getHandsetByPtn($ptn);
    $logger->_saveLog("OP|H=|Client=" . $utilities->getRealIP() . "-------------------------------------------------------|notifySCReception");
    $logger->_saveLog(date("d/m/Y H:i:s", time()) . ": " . $datos);

    if ($idHandset == 0) {
        $logger->_saveLog("BD|H={$ptn}|El equipo no existe en BD|notifySmsReception");
        //Guardar PTN en BD
        $logger->_saveLog("BD|H={$ptn}|Guardando equipo en BD|notifySmsReception");
        $resSaveH = $parlayXProcess->saveHandsetSC($ptn);
        if( $resSaveH[1]){
            $logger->_saveLog("BD|H={$ptn}|ID={$resSaveH[0]}|Equipo guardado correctamente en BD|notifySmsReception");
            $idHandset = $resSaveH[0];
            //Ejecutar cobro por evento(PayAsYouGo)
            $response = $parlayXProcess->aplyCharge($ptn, $idHandset);
        }else{
            $logger->_saveLog("ERR!!!|BD|H={$ptn}|Guardando equipo en BD|notifySmsReception");
        }
    } else {
        $logger->_saveLog("BD|H={$ptn}|El equipo ya existe en BD|notifySmsReception");
        //Validar si es contrato por evento
        $tipoContrato = $Base->getTipoContratoByHandsetId($idHandset);
        if ($tipoContrato == 2) {
            $logger->_saveLog("BD|H={$ptn}|Contrato por evento|Procesando cobro|notifySmsReception");
            //Ejecutar cobro por evento(PayAsYouGo)
            $response = $parlayXProcess->aplyCharge($ptn, $idHandset);
        }else{
            $logger->_saveLog("BD|H={$ptn}|Contrato ilimitado|notifySmsReception");
        }
    }
    $Base->close();
    //return $response;
}

// Funcion para el procesamiento de mensajes
function SCReception($correlator, $message) {

    $file = fopen('notifySmsReception.log', 'a+');
    $datos = "Correlator: " . $correlator;
    foreach ($message as $msg => $valor) {
        $datos.= " | " . $msg . ": " . $valor;
    }
    $datos.="\n";
    fwrite($file, date("d/m/Y H:i:s", time()) . ": " . $datos);
    fclose($file);

    $arrMsg = array();
    foreach ($message as $msg => $valor) {
        $arrMsg[$msg] = $valor;
    }
    if (count($arrMsg) == 3) {
        $remitente = substr($arrMsg['senderAddress'], 4);
        $mensaje = $arrMsg['message'];

        $Base = new Base();

        if ($Base->connEstatus) {
            $idHandset = $Base->decodeHandset($remitente);

            if (strlen(trim($mensaje)) == 8) {//Activar
                $Utilities = new Utilities();

                $serverCode = $Utilities->generateCode();

                if ($Base->updateCodes($idHandset, $serverCode, trim($mensaje))) {
                    if ($Base->message($idHandset, $serverCode)) {
                        $Base->close();
                        return "MSG SEND";
                    } else {
                        $Base->close();
                        return "500";
                    }
                } else {
                    $Base->close();
                    return "500";
                }
            } else {//Request o Cancelar
                $arrArgs = explode("|", $mensaje);
                if (count($arrArgs) == 5) {//Request
                    $lon = $arrArgs[1];
                    $lat = $arrArgs[2];
                    $locationType = $arrArgs[3];
                    $Time = $arrArgs[4];

                    $idAlarma = $Base->existeAlarma($idHandset);
                    $radio = $Base->radio($locationType);
                    if ($idAlarma > 0) {//Alarma existente
                        if ($Base->update($idAlarma, $lon, $lat, $radio, $Time)) {
                            $Base->close();
                            return "REQUEST SEND AE";
                        } else {
                            $Base->close();
                            return "500";
                        }
                    } else {//Nueva alarma
                        if ($Base->save($idHandset, $lon, $lat, $radio, $Time)) {
                            $Base->close();
                            return "REQUEST SEND AN";
                        } else {
                            $Base->close();
                            return "500";
                        }
                    }
                } else {//Cancelar
                    if ($Base->cancelAlarm($idHandset)) {
                        if ($Base->cancelarMensajes($idHandset)) {
                            $Base->close();
                            return "CANCEL";
                        } else {
                            $Base->close();
                            return "500";
                        }
                    } else {
                        $Base->close();
                        return "500";
                    }
                }
            }
        } else {
            return "500";
        }
    } else {
        return "501";
    }
}

//Funcion para cobranza alternativa
function notifySCReception($msisdn) {
    $_path = '/var/www/pro/ial/call/';
    require_once $_path.'Parlayx/configWS.php';
    require_once $_path.'Parlayx/parlayX.class.php';
    require_once $_path.'Parlayx/parlayXProcess.class.php';
    $ptn = $msisdn;
    $Base = new Base();
    $parlayXProcess = new parlayXProcess($_WS,$_path);
    $logger = new logger('/var/log/nxt/ial/srv/paymentSC/');
    $utilities = new Utilities();
    $idHandset = $Base->getHandsetByPtn($ptn);
    $response = "0";
    $logger->_saveLog("OP|H={$ptn}|Client=" . $utilities->getRealIP() . "-------------------------------------------------------|notifySCReception");

    if ($idHandset == 0) {
        $logger->_saveLog("BD|H={$ptn}|El equipo no existe en BD|notifySCReception");
        //Guardar PTN en BD
        $resSaveH = $parlayXProcess->saveHandsetSC($ptn);
        $idHandset = $resSaveH[0];
        //Ejecutar cobro por evento(PayAsYouGo)
        $response = $parlayXProcess->aplyCharge($ptn, $idHandset,2);
    } else {
        //Validar si es contrato por evento
        $tipoContrato = $Base->getTipoContratoByHandsetId($idHandset);
        if ($tipoContrato == 2) {
            //Ejecutar cobro por evento(PayAsYouGo)
            $response = $parlayXProcess->aplyCharge($ptn, $idHandset,2);
        }
    }
    $Base->close();
    return $response;
}

// Instancia del Server
$server = new soap_server();

// Definir NS
$ns = "http://call.ialarm.webmaps.mx";
$server->configureWSDL('wmServices', $ns);
$server->wsdl->schematargetnamespace = $ns;

// Definicion de parametros de entrada		
$server->wsdl->addComplexType(
        'Msg', 'complexType', 'struct', 'all', '', array(
    'message' => array('name' => 'message', 'type' => 'xsd:string'),
    'senderAddress' => array('name' => 'senderAddress', 'type' => 'xsd:string'),
    'smsServiceActivationNumber' => array('name' => 'smsServiceActivationNumber', 'type' => 'xsd:string')
        )
);

$server->wsdl->addComplexType('arrayMsg', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType',
        'wsdl:arrayType' => 'tns:Msgs[]')
        ), 'tns:Msgs'
);

// Registro Metodo a WS
$server->register('SCReception', array('correlator' => 'xsd:string',
    'message' => 'tns:arrayMsg'), array('response' => 'xsd:string'), $ns);


// Registro Metodo a WS
$server->register('notifySmsReception', array('correlator' => 'xsd:string',
    'message' => 'tns:arrayMsg'), array(), $ns);

// Registro Metodo de cobranza alternativa a WS
$server->register('notifySCReception', array('msisdn' => 'xsd:string'), array('response' => 'xsd:string'), $ns);


//$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
//$server->service($HTTP_RAW_POST_DATA); 

if (isset($HTTP_RAW_POST_DATA)) {
    $input = $HTTP_RAW_POST_DATA;
} else {
    $input = implode("\r\n", file('php://input'));
}
$server->service($input);
exit;
?>

