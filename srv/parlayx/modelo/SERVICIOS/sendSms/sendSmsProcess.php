<?php

class sendProcess {

    public $arrMensajes;
    public $ids;
    private $dat_contacto;
    private $dat_handset;
    private $dat_mensajes;
    private $dat_alarma;
    private $parlayx;
    private $correo;
    private $logger;
    private $config;
    private $fault;
    private $logDetail;
    private $urlPage;
    private $msgAnt = '';
    private $db_Fail = true;
    private $verifyConnect;

    function sendProcess($config = array()) {
        try {
            $this->config = $config;
            $this->correo = new Correo();
            $this->parlayx = new parlayX();
            $this->logger = new logger($this->config['sendSms']['logPath']);
            $this->logDetail = $this->config['sendSms']['logDetail'];
            $this->verifyConnect = new verifyConnect(ROOT_ADODB . '/ini/BD.ini');            
        } catch (Exception $e) {
            $this->logger->_saveLogError($e);
            echo "Terminado";
        }
    }

    function verifyConnect() {
        if ($this->verifyConnect->_verifyConnectBDMysql()) {
            if ($this->db_Fail) {
                $this->dat_contacto = new Base_Dat_Contacto();
                $this->dat_handset = new Base_Dat_Handset();
                $this->dat_alarma = new Base_Dat_Alarma();
                $this->dat_mensajes = new Base_Dat_Mensajes();
                $this->dat_historicomensajes = new Base_Dat_HistoricoMensajes();
                $this->db_Fail = false;
            }
            return true;
        } else {
            $this->db_Fail = true;
            return false;
        }
    }

    function run() {
        echo "Servicio iniciado";
        while (TRUE) {
            if ($this->verifyConnect()) {
                if ($this->msgAnt != '!!No se encontraron mensajes para enviar|run')
                    $this->logger->_saveLog("Buscando informacion de  mensajes a enviar|run");

                $mensajes = $this->dat_mensajes->getMensajesToSend();
                $this->arrMensajes = $mensajes[0];
                $this->ids = $mensajes[1];

                if (sizeof($this->arrMensajes)) {
                    $this->logger->_saveLog("Informacion de mensajes obtenida|{$this->ids}|Procesando mensajes|run");

                    if ($this->dat_mensajes->updateStatusMensajes($this->ids)) {
                        $this->logger->_saveLog("Estatus de mensajes actualizado a atendido|run");
                    } else {
                        $this->logger->_saveLogError("BD|Actualizando estatus de mensajes a atendido|" . $this->dat_mensajes->__getQuerySQL() . "|run");
                    }

                    foreach ($this->arrMensajes as $key => $value) {
                        $this->logger->_saveLog("M={$value['idMensaje']},H={$value['idHandset']}|---------------Iniciando proceso de envio de mensajes|processMessage");
                        $this->logger->_saveLog("Msj={$value['idMensaje']},H={$value['idHandset']},T={$value['tipo']}|Procesando mensaje|run");

                        $this->logger->_saveLog("Obteniendo informacion del equipo|run");
                        $infoHandset = $this->dat_handset->getInfoByHandsetId($value['idHandset']);

                        if (sizeof($infoHandset) > 0) {
                            $this->logger->_saveLog("BD|Informacion obtenida correctamente|run");
                        } else {
                            $this->logger->_saveLogError("BD|Obteniendo informacion del equipo|run");
                        }

                        switch ($value['tipo']) {
                            case '1':
                                $this->sendGetEvtId($value['idHandset']);
                                $this->processMessage($value, $infoHandset);
                                break;
                            case '2':
                                $this->procesaMensajeInformativo($value, $infoHandset);
                                break;
                            default:
                                $this->sendMensajeNotificacion($value['idHandset'], $infoHandset['ptn'], $value['tipo']);
                                break;
                        }
                        $this->logger->_saveLog("M={$value['idMensaje']},H={$value['idHandset']}|---------------Terminado proceso de envio de mensajes|processMessage");
                        /******** Actualizar contador MT ****/
                        $this->logger->_saveLog("M={$value['idMensaje']},H={$value['idHandset']}|Actualizando MT|run");
                        if($this->updateMTByHandset($value['idHandset'])){
                            $this->logger->_saveLog("M={$value['idMensaje']},H={$value['idHandset']}|MT actualizado correctamente|run");
                        }else{
                            $this->logger->_saveLog("ERR|BD|M={$value['idMensaje']},H={$value['idHandset']}|Actualizando MT|run");
                        }
                        /******************************************************/
                    }
                    $this->msgAnt = '';
                } else {
                    if ($this->msgAnt != '!!No se encontraron mensajes para enviar|run') {
                        $this->msgAnt = '!!No se encontraron mensajes para enviar|run';
                        $this->logger->_saveLog($this->msgAnt);
                    }
                }
            } else {
                if ($this->msgAnt != 'Sin conexion a BD!!|Reintentando cada 5 segundos..') {
                    $this->msgAnt = 'Sin conexion a BD!!|Reintentando cada 5 segundos..';
                    $this->logger->_saveLogError("Sin conexion a BD!!|Reintentando cada 5 segundos..");
                }
                Sleep(5);
            }
        }
    }

    private function sendGetEvtId($idHandset) {
        $idAlarma = $this->dat_alarma->getAlarmByHandset($idHandset);
        $idAlarma = sha1(md5($idAlarma . $this->config['sendSms']['seed']));
        $this->urlPage = $this->config['sendSms']['urlPage'] . $idAlarma;
    }

    private function sendGetUrlIalarm($idHandset) {
        $idAlarma = $this->dat_alarma->getAlarmByHandset($idHandset);
        $idAlarma = sha1(md5($idAlarma . $this->config['sendSms']['seed']));
        return $this->config['sendSms']['urlPage'] . $idAlarma;
    }
    
    private function sendGetUrlGoogle($idHandset) {
        $pos = $this->dat_alarma->getLastPosByHandset($idHandset);
        return $this->config['sendSms']['urlGoogle'] . $pos;
    }
    
    private function procesaMensajeInformativo($message, $infoHandset) {
        $this->logger->_saveLog("Enviando mensaje de validacion|procesaMensageValidacion");
        $this->logger->_saveLog("Obteniendo informacion del equipo|procesaMensageValidacion");

        if (sizeof($infoHandset) > 0) {
            $this->logger->_saveLog("BD|Informacion obtenida correctamente|processMessage");

            if ((strpos($message['mensaje'], 'http://') !== FALSE) || ( strpos($message['mensaje'], 'No se pudo determinar su') !== FALSE)) {
                $message['mensaje'] = 'Su servicio iAlarm funciona correctamente ' . $message['mensaje'];
            }

            if ($this->sendSmsNextel($infoHandset['ptn'], $message['mensaje'], $infoHandset['handsetId'], $this->config['sendSms']['senderName'])) {
                $this->logger->_saveLog("M=${message['idMensaje']},H=${message['idHandset']}|SMS de validacion enviado a {$infoHandset['ptn']}|procesaMensageValidacion");
            } else {
                $this->logger->_saveLog("ERR|M=${message['idMensaje']},H=${message['idHandset']}|Enviando SMS de validacion a {$infoHandset['ptn']}|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|procesaMensageValidacion");
            }
        } else {
            $this->logger->_saveLogError("BD|Obteniendo informacion del equipo|" . $this->dat_handset->__getQuerySQL() . "|procesaMensageValidacion");
        }
    }

    private function processMessage($message, $infoHandset) {
        if (sizeof($infoHandset) > 0) {

            try {
                $value = $infoHandset;
                $arrContactos = $this->dat_contacto->getContactosByHandset($value['handsetId']);

                if (sizeof($arrContactos) > 0) {
                    foreach ($arrContactos AS $keyConcato => $valueContacto) {
                        switch ($valueContacto['tipo']) {
                            case '1':
                                //Nextel
                                $urlPage = $this->sendGetUrlIalarm($value['handsetId']);
                                if ($this->sendSmsNextel($valueContacto['numerocorreo'], $value['mensaje'] . ' ' . $urlPage, $value['handsetId'], $this->config['sendSms']['senderName'])) {
                                    $this->logger->_saveLog("M=${message['idMensaje']},H=${message['idHandset']}|SMS enviado a {$valueContacto['numerocorreo']}|Message={$value['mensaje']} {$urlPage}|processMessage");
                                } else {
                                    $this->logger->_saveLog("ERR|M=${message['idMensaje']},H=${message['idHandset']}|Enviando SMS a {$valueContacto['numerocorreo']}|MessageID ={$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|processMessage");
                                }
                                break;
                            case '2':
                                //Correo;                                
                                $urlPage = $this->sendGetUrlGoogle($value['handsetId']);
                                $urlPageiAlarm = $this->sendGetUrlIalarm($value['handsetId']);
                                if ($this->sendCorreo($valueContacto['nombre'], $valueContacto['numerocorreo'], $value['asunto'], $value['mensaje'], $value['handsetId'], $value['ptn'],$urlPageiAlarm.'<br/><br/>'.$urlPage)) {
                                    $this->logger->_saveLog("M=${message['idMensaje']},H=${message['idHandset']}|E-mail enviado a {$valueContacto['numerocorreo']}||Message={$value['mensaje']} {$urlPage} {$urlPageiAlarm}|processMessage");
                                } else {
                                    $this->logger->_saveLog("ERR|M=${message['idMensaje']},H=${message['idHandset']}|Enviando E-mail a {$valueContacto['numerocorreo']}|processMessage");
                                }
                                break;
                            case '3':
                                //Otro
                                if ($this->sendSmsOtro($valueContacto['numerocorreo'], $value['asunto'], $value['mensaje'], $value['handsetId'], $value['ptn'])) {
                                    $this->logger->_saveLog("M=${message['idMensaje']},H=${message['idHandset']}|SMS [3ero] enviado a {$valueContacto['numerocorreo']}|processMessage");
                                } else {
                                    $this->logger->_saveLog("ERR|M=${message['idMensaje']},H=${message['idHandset']}|Enviando SMS [3ero] a {$valueContacto['numerocorreo']}|processMessage");
                                }
                                break;
                        }
                    }
                } else {
                    $this->logger->_saveLog("ERR|M=${message['idMensaje']},H=${message['idHandset']}|BD|Imposible procesar mensaje, SIN contactos asociados|" . $this->dat_contacto->__getQuerySQL() . "|processMessage");
                }
            } catch (Exception $e) {
                $this->logger->_saveLogError($e);
            }
        } else {
            $this->logger->_saveLogError("BD|Obteniendo informacion del equipo|" . $this->dat_handset->__getQuerySQL() . "|processMessage");
        }
    }

    private function sendMensajeNotificacion($idHandset, $numero, $tipo) {
        $asunto = '';

        switch ($tipo) {
            case '3':
                $message = $this->config['sendSms']['msgNotice'];
                $tipoMsg = 'Nofiticacion de ADVERTENCIA de SUSPENSION';
                break;
            case '4':
                $message = $this->config['sendSms']['msgSuspend'];
                $tipoMsg = 'NOTIFICACION de SUSPENSION';
                break;
            case '9':
                $message = $this->config['sendSms']['msgSuspend30Dias'];
                $tipoMsg = 'NOTIFICACION de SUSPENSION(Reintento-30 dias) - iAlarm 1.0';
                break;
            case '5':
                $message = $this->config['sendSms']['msgNoticeMensual'];
                $tipoMsg = 'Nofiticacion de ADVERTENCIA de SUSPENSION-Mensual';
                break;
            case '6':
                $message = $this->config['sendSms']['msgSuspendMensual'];
                $tipoMsg = 'NOTIFICACION de SUSPENSION-Mensual';
                break;
            case '7':
                $message = $this->config['sendSms']['msgListaNegra'];
                $tipoMsg = 'NOTIFICACION de LISTA NEGRA';
            case '8':
                $message = $this->config['sendSms']['msgNotifyNotAmount'];
                $tipoMsg = 'NOTIFICACION de SALDO INSUFICIENTE - iAlarm 1.0';
                break;
        }

        try {
            $this->logger->_saveLog("H={$idHandset}|Enviando SMS de {$tipoMsg} a {$numero}|sendMensajeNotificacion");
            if ($this->parlayx->sendSms($numero, $this->config['sendSms']['senderName'], $message)) {
                $this->logger->_saveLog("H={$idHandset}|SMS de {$tipoMsg} enviado a {$numero}|sendMensajeNotificacion");
                /*                 * ** Registrar detalle de la operacion** */
                $this->writeXmlDetail($idHandset, 'sendSms');
                $this->logger->_saveLog("H={$value['handsetId']}|Guardando mensaje en historico|sendSmsNextel");
                if ($this->saveHistoricoMensajes($idHandset,$numero, 10,$message)) {
                    $this->logger->_saveLog("H={$value['handsetId']}|Mensaje guardado en historico correctamente|sendSmsNextel");
                } else {
                    $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|Guardando mensaje en historico|query=".$this->dat_historicomensajes->_query."|sendSmsNextel");
                }
                return true;
            } else {
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $this->logger->_saveLog("ERR|H={$idHandset}|Enviando SMS de {$tipoMsg} a {$numero}|sendMensajeNotificacion");
                /*                 * ** Registrar detalle de la operacion** */
                $this->writeXmlDetail($idHandset, 'sendSms');
                 //Para iAlarm 2.0 guardar mensaje en historico
                $this->logger->_saveLog("H={$value['handsetId']}|Guardando mensaje en historico|sendSmsNextel");
                if ($this->saveHistoricoMensajes($idHandset,$numero, 11,$message)) {
                    $this->logger->_saveLog("H={$value['handsetId']}|Mensaje guardado en historico correctamente|sendSmsNextel");
                } else {
                    $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|Guardando mensaje en historico|query=".$this->dat_historicomensajes->_query."|sendSmsNextel");
                }
                return false;
            }
        } catch (Exception $e) {
            $this->logger->_saveLogError($e);
            return false;
        }
    }

    private function sendSmsNextel($numero, $mensaje, $idHandset, $senderName) {
        try {
            $this->logger->_saveLog("H={$idHandset}|Enviando SMS [NEXTEL] a {$numero}|sendSmsNextel");
            if ($this->parlayx->sendSms($numero, $senderName, $mensaje)) {
                $this->logger->_saveLog("H={$idHandset}|SMS enviado a {$numero}|sendSmsNextel");
                /*                 * ** Registrar detalle de la operacion** */
                $this->writeXmlDetail($idHandset, 'sendSms');
                //Para iAlarm 2.0 guardar mensaje en historico
                $this->logger->_saveLog("H={$value['handsetId']}|Guardando mensaje en historico|sendSmsNextel");
                if ($this->saveHistoricoMensajes($idHandset,$numero, 10,$mensaje)) {
                    $this->logger->_saveLog("H={$value['handsetId']}|Mensaje guardado en historico correctamente|sendSmsNextel");
                } else {
                    $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|Guardando mensaje en historico|query=".$this->dat_historicomensajes->_query."|sendSmsNextel");
                }
                return true;
            } else {
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $this->logger->_saveLog("ERR|H={$idHandset}|Enviando SMS [NEXTEL] a {$numero}|Code={$this->faulInfo['errorCode']}|Message={$this->faulInfo['faultstring']}|Variables={$this->faulInfo['variables']}|sendSmsNextel");
                /*                 * ** Registrar detalle de la operacion** */
                $this->writeXmlDetail($idHandset, 'sendSms');
                //Para iAlarm 2.0 guardar mensaje en historico
                $this->logger->_saveLog("H={$value['handsetId']}|Guardando mensaje en historico|sendSmsNextel");
                if ($this->saveHistoricoMensajes($idHandset,$numero, 11,$mensaje)) {
                    $this->logger->_saveLog("H={$value['handsetId']}|Mensaje guardado en historico correctamente|sendSmsNextel");
                } else {
                    $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|Guardando mensaje en historico|query=".$this->dat_historicomensajes->_query."|sendSmsNextel");
                }
                return false;
            }
        } catch (Exception $e) {
            $this->logger->_saveLogError($e);
            return false;
        }
    }

    private function sendCorreo($nombre, $correo, $asunto, $mensaje, $idHandset, $ptn,$urlPage) {
        $result = false;
        try {
            $this->correo = new Correo();
            $this->logger->_saveLog("H={$idHandset}|Enviando E-mail a {$correo}|sendCorreo");
            $this->correo->makeBodyFromFile("/var/www/dev/ial/srv/parlayx/v1.0.1/vistas/XHTML/Email/message.html", array('MESSAGE' => $mensaje, 'URL' => $urlPage, 'NUMERO' => $ptn));
            $arrVariables['subject'] = $asunto;
            $arrVariables['nombre'] = 'Nextel iAlarm';
            $arrVariables['remit'] = 'info@nextel.com.mx';
            $arrVariables['contacto'] = $nombre;
            $arrVariables['correo'] = $correo;
            $arrVariables['alt_body'] = 'El contenido del mensaje no se puede desplegar';

            $result = $this->correo->sendMailReports($arrVariables);
            if ($result)
                $this->logger->_saveLog("H={$idHandset}|E-mail enviado a {$correo}|sendCorreo");
            else
                $this->logger->_saveLogError("H={$idHandset}|Enviando E-mail a {$correo}|sendCorreo|Error=" . $this->correo->error);
        } catch (Exception $e) {
            $this->logger->_saveLogError($e);
        }
        return $result;
    }

    private function sendSmsOtro($numero, $asunto, $mensaje, $idHandset, $senderName) {
        try {
            $this->logger->_saveLog("H={$idHandset}|Enviando SMS [Tercero] a {$numero}|sendSmsOtro");
            if (TRUE)
                $this->logger->_saveLog("H={$idHandset}|SMS [Tercero] enviado a {$numero}|sendSmsOtro");
            else
                $this->logger->_saveLog("ERR|H={$idHandset}|ERR|Enviando SMS [Tercero] a{$numero}|sendSmsOtro");
        } catch (Exception $e) {
            $this->logger->_saveLogError($e);
        }
    }

    private function writeXmlDetail($idHandset, $op) {

        if ($this->logDetail) {
            $this->logger->_saveLogDetail("H={$idHandset}|OP=[{$op}]---------------------------------------------------------");
            $this->logger->_saveLogDetail("Target|" . $this->parlayx->__getTarget());
            $this->logger->_saveLogDetail("SoapSent|" . $this->parlayx->__getLastRequest());
            $this->logger->_saveLogDetail("SoapReceived|" . $this->parlayx->__getLastResponse());
        }
    }

    
    private function saveHistoricoMensajes($handset, $numero, $estatus, $mensaje) {
        return $this->dat_historicomensajes->insertHistoricoMensajes($handset,$estatus,$numero,$mensaje);
    }

    private function updateMTByHandset($handset){
        $MT = $this->dat_handset->getHandsetMTByHandsetID($handset);
        $MT++;
        $this->dat_handset->setPk($handset);
        $this->dat_handset->n_handset_MT = $MT;
        return $this->dat_handset->save();
    }
    
}
