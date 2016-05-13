<?php

class alarmasProcess {

    private $dat_reintentocobro;
    private $dat_alarma;
    private $dat_estatuspayment;
    private $dat_contacto;
    private $dat_envioalarma;
    private $dat_contadortransacciones;
    private $dat_handset;
    private $dat_mensajes;
    private $parlayx;
    private $correo;
    private $config;
    public $arrHandsets;
    public $arrAlarmas;
    private $logger;
    private $fault;
    private $faultInfo;
    private $logDetail;
    private $url;
    private $paymentMethod;
    private $submarket;
    private $status;
    private $serviceCodes;
    private $msgAnt = '';
    private $db_Fail = true;
    private $verifyConnect;
    private $transactionId;
    private $transactionIdHash;
    private $dat_historicopayment;

    function alarmasProcess($config = array()) {
        try {
            $this->config = $config;
            $this->correo = new Correo();
            $this->parlayx = new parlayX();
            $this->logger = new logger($this->config['alarmas']['logPath']);
            $this->logDetail = $this->config['alarmas']['logDetail'];
            $this->verifyConnect = new verifyConnect(ROOT_ADODB . '/ini/BD.ini');
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            echo "Terminado";
        }
    }

    function verifyConnect() {
        if ($this->verifyConnect->_verifyConnectBDMysql()) {
            if ($this->db_Fail) {
                $this->dat_reintentocobro = new Base_Dat_ReintentoCobro();
                $this->dat_alarma = new Base_Dat_Alarma();
                $this->dat_estatuspayment = new Base_Dat_EstatusPayment();
                $this->dat_contacto = new Base_Dat_Contacto();
                $this->dat_envioalarma = new Base_Dat_EnvioAlarma();
                $this->dat_contadortransacciones = new Base_Dat_ContadorTransacciones();
                $this->dat_handset = new Base_Dat_Handset();
                $this->dat_mensajes = new Base_Dat_Mensajes();
                $this->dat_historicopayment = new Base_Dat_HistoricoPayment();
                $this->db_Fail = false;
            }
            return true;
        } else {
            $this->db_Fail = true;
            return false;
        }
    }

    public function alarmaStartProcess() {
        echo "Servicio iniciado";
        while (TRUE) {
            if ($this->verifyConnect()) {
                if ($this->msgAnt != '!!No se encontraron alarmas que atender|alarmaStartProcess')
                    if ($this->logDetail)
                        $this->logger->_saveLog("Obteniendo alarmas a atender|alarmaStartProcess");

                $hnd = $this->dat_envioalarma->getAlarmsToSend();
                if ($hnd[0] != '') {
                    $this->logger->_saveLog("-----------------------------------------------------------");
                    $this->logger->_saveLog("Alarmas a atender obtenidas|Equipos={$hnd[0]}|alarmaStartProcess");
                    if ($this->logDetail)
                        $this->logger->_saveLog("Obteniendo informacion de equipos|alarmaStartProcess");
                    $arrHandsets = $this->dat_handset->getHandsetInfoById($hnd[0]);
                    $this->arrHandsets = $arrHandsets;
                    $this->arrAlarmas = $hnd[1];
                    $this->logger->_saveLog("Iniciando procesamiento de alarmas|alarmaStartProcess");
                    $this->sendProcesaAlarma();
                    $this->msgAnt = '';
                    $this->logger->_saveLog("-----------------------------------------------------------");
                } else {
                    if ($this->msgAnt != '!!No se encontraron alarmas que atender|alarmaStartProcess') {
                        $this->msgAnt = '!!No se encontraron alarmas que atender|alarmaStartProcess';
                        $this->logger->_saveLog($this->msgAnt);
                    }
                }
            } else {
                if ($this->msgAnt != 'Sin conexion a BD!!|Reintentando cada 5 segundos..') {
                    $this->msgAnt = 'Sin conexion a BD!!|Reintentando cada 5 segundos..';
                    $this->logger->_saveLogError($this->msgAnt);
                }
                Sleep(5);
            }
        }
    }

    public function sendProcesaAlarma() {
        try {
            $flagPendiente = false;
            foreach ($this->arrHandsets as $key => $value) {
                $flagValServices = true;

                /*                 * ********SI ES LA PRIMERA VEZ QUE SE PROCESA LA ALARMA, VALIDAR SERVICIOS */
                if ($this->arrAlarmas[$value['handsetId']]['enviados'] == 0) {
                    if ($this->validateService($value['handsetId'], $value['ptn']) != 4) {
                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|EQUIPO SUSPENDIDO|sendProcesaAlarma");
                        $this->cancelService($value);
                        $flagValServices = false;
                        return;
                    }
                }

                if ($flagValServices) {

                    $alarmaRec = $this->dat_alarma->getAlarmRecibidaByHandset($value['handsetId']);
                    if ($alarmaRec != "") {
                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Modificando estatus de alarma {$alarmaRec} a seguimiento|sendProcesaAlarma");
                        if ($this->dat_alarma->updateStatusAlarm($alarmaRec, 1))
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Estatus de Alarma {$alarmaRec} modificado a seguimiento|sendProcesaAlarma");
                        else
                            $this->logger->_saveLogError("H={$value['handsetId']}|PTN={$value['ptn']}|Modificando Estatus de Alarma {$alarmaRec} a seguimiento|" . $this->dat_handset->__getQuerySQL() . "|sendProcesaAlarma");
                    }

                    //Procesar cobro para contratos por evento
                    $this->sendGetEvtId($value['handsetId']);
                    if ($value['contrato'] == '2') {
                        //CONTRATO POR EVENTO
                        //if( $value['tipoUsuario'] == '2'){
                        //Prepago
                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Procesando contrato por evento|sendProcesaAlarma");

                        $pagosPend = $this->dat_reintentocobro->getPagosPendientes($value['handsetId']);
                        //if ($pagosPend < 3 || $this->arrAlarmas[$value['handsetId']]['enviados'] > 0) {
                        if ($this->arrAlarmas[$value['handsetId']]['enviados'] == 0) {

                            //----------------Ejecutar cobro
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|---------------Iniciando proceso de cobro|sendProcesaAlarma");

                            $responsePayment = $this->sendChargeMount($value['ptn'], $value['handsetId'], $value['tipoUsuario']);
                            switch ($responsePayment) {
                                case -1:
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|ERROR AL OBTENER INFORMACION|Parametros no disponibles!!|retryPaymentStartProcess");
                                    $this->logger->_saveLog("H={$keyHandset}|P={$keyCobro}|PTN={$value['ptn']}|Actualizando informacion del reintento de cobro|retryPaymentStartProcess");
                                    $this->updateLastRetry($keyCobro, 3, 11, $keyHandset);
                                    break;
                                case 0:
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|NO SE LOGRO OBTENER INFORMACION|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|sendProcesaAlarma");
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Actualizando estatus de cobro NO EXITOSO|sendProcesaAlarma");
                                    $this->updateEstatusPayment($value['handsetId'], $value['ptn'], 11);
                                    break;
                                case 4:
                                    //Registrar cobro pendiente de evento
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|COBRO NO EXITOSO|sendProcesaAlarma");
                                    $this->updateEstatusPayment($value['handsetId'], $value['ptn'], 11);
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|No se logro ejecuar el cobro|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|sendProcesaAlarma");
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Insertando cobro pendiente|sendProcesaAlarma");
                                    $this->sendInsertaCobroPendiente($value['handsetId'], $value['ptn']);
                                    $this->sendProcesaMensajes($value);
                                    $flagPendiente = true;
                                    $this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 4);
                                    //if ($value['tipoServicio'] == '1') {
                                    //Para iAlarm 2.0 guardar cobro en historico
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|retryPaymentStartProcess");
                                    if ($this->saveHistoricoPayment($value['handsetId'], 11)) {
                                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro guardado en historico correctamente|retryPaymentStartProcess");
                                    } else {
                                        $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|retryPaymentStartProcess");
                                    }
                                    //}
                                    break;
                                case 5:
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|COBRO EXITOSO|sendProcesaAlarma");
                                    $this->updateEstatusPayment($value['handsetId'], $value['ptn'], 10);
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro [POR EVENTO] efectuado|sendProcesaAlarma");
                                    $this->sendProcesaMensajes($value);
                                    //if ($value['tipoServicio'] == '1') {
                                    //Para iAlarm 2.0 guardar cobro en historico
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|retryPaymentStartProcess");
                                    if ($this->saveHistoricoPayment($value['handsetId'], 10)) {
                                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro guardado en historico correctamente|retryPaymentStartProcess");
                                    } else {
                                        $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|retryPaymentStartProcess");
                                    }
                                    //}
                                    break;
                                default:
                                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|!!!!Falla en tiempo de ejecucion");
                            }

                            /* Verificar si el equipo es 2G y tiene plan PREPAGO
                              if ($responsePayment == '3') {
                              $this->cancelServiceHandsetDisabled($value);
                              }
                             */

                              //Verificar si se tienen 3 pagos pendientes y enviar notificacion suspension de servicio
                              if ($pagosPend == 2 && $flagPendiente) {
                                    $this->logger->_saveLog("H={$value['handsetId']}|3 pagos pendientes|sendProcesaAlarma");
                                    $this->sendSuspenService($value);
                              }
                             

                            /* Verificar si se tiene 1 pago pendiente y enviar advertencia de suspension del servicio
                              if ($pagosPend == 1 && $flagPendiente) {
                              $this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 1);
                              }
                             */

                            //----------------Ejecutar cobro
                        } else {
                            $this->sendProcesaMensajes($value);
                        }
                        /* } else {
                          $this->logger->_saveLog("H={$value['handsetId']}|Intento de uso fallido|cuenta con 3 pagos pendientes|sendProcesaAlarma");
                          $this->cancelServiceHandsetDisabled($value);
                          } */
                        //}
                    } else {
                        //MENSUAL
                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Procesando contrato mensual|sendProcesaAlarma");
                        $this->sendProcesaMensajes($value);
                    }
                }
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
    }

    private function validateService($idHandset, $ptn) {
        $response = 4;
        $SC = 'CD01';
        /*
          if ($this->arrHandsets[$idHandset]['userNetwork'] == '1')
          $SC = 'CD05';
          $this->logger->_saveLog("H={$idHandset}|Validando STATUS [getHandsetInfo]|validateService");
          if ($this->parlayx->retrieveSubscriber($this->arrHandsets[$idHandset]['ptn'], $SC)) {
          $retrieveSubscriberResponse = $this->parlayx->_getParlayXResponse();
          $this->paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
          $this->submarket = $retrieveSubscriberResponse->subMarketId;
          $this->logger->_saveLog("H={$idHandset}|Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$this->paymentMethod},submarket={$this->submarket}|sendChargeMount");
          } else {
          $this->fault = $this->parlayx->_getParlayXFault();
          $this->faulInfo = $this->parlayx->_getFaultInfo();
          $response = 0;
          $this->logger->_saveLog("ERR|H={$idHandset}|Obteniendo informacion [retrieveSubscriber]|sendChargeMount");
          }

          //***************Registrar Detalle de operacion
          $this->writeXmlDetail($idHandset, 'retrieveSubscriberResponse');
         */
        $this->logger->_saveLog("H={$idHandset}|PTN={$ptn}|Obteniendo informacion [getHandsetInfo]|sendChargeMount");
        //Obtener status y servicecodes
        if ($this->parlayx->getHandsetInfo($ptn, $SC)) {
            $getHandsetInfoResponse = $this->parlayx->_getParlayXResponse();
            $status = $getHandsetInfoResponse->status;
            $serviceCodes = $getHandsetInfoResponse->serviceCodes;
            $this->logger->_saveLog("H={$idHandset}|PTN={$ptn}|Informacion [getHandsetInfo] obtenida correctamente|status={$status},serviceCodes={$serviceCodes}|sendChargeMount");
        } else {
            $this->fault = $this->parlayx->_getParlayXFault();
            $this->faulInfo = $this->parlayx->_getFaultInfo();
            $response = 0;
            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$ptn}|Obteniendo informacion [getHandsetInfo]||MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|sendChargeMount");
            return $response;
        }
        /*         * ** Registrar detalle de la operacion** */
        $this->writeXmlDetail($idHandset, 'getHandsetInfo');

        //Validar status y service codes
        if ($status != 'A') {
            $response = 1;
            $this->logger->_saveLog("H={$idHandset}|PTN={$ptn}|EN ESTATUS SUSPENDIDO|status={$status}|sendChargeMount");
            return $response;
        }

        /*
          if ($this->serviceCodes != NUll) {
          if ($this->serviceCodes != $SC) {
          $response = 2;
          $this->logger->_saveLog("H={$idHandset}|NO CUENTA CON EL SERVICIO  {$SC},UserNetwork=" . $this->arrHandsets[$idHandset]['userNetwork'] . "|serviceCodes={$this->serviceCodes}|sendChargeMount");
          }
          } else {
          $response = 2;
          $this->logger->_saveLog("H={$idHandset}|NO CUENTA CON EL SERVICIO  {$SC},UserNetwork=" . $this->arrHandsets[$idHandset]['userNetwork'] . "|serviceCodes={$this->serviceCodes}|sendChargeMount");
          }
         */
        return $response;
    }

    private function cancelService($value) {
        /**         * * Cencelando servicio */
        $this->cancelServiceHandsetDisabled($value);
        /*         * *** Cancelando cobro */
        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cencelando cobro de servicio|cancelService");
        if ($this->dat_estatuspayment->updateStatusPayment($value['handsetId'], '4')) {
            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro se servicio cancelado correctamente|cancelService");
        } else {
            $this->logger->_saveLog("ERR|H={$value['handsetId']}|PTN={$value['ptn']}|Cencelando cobro de servicio|cancelService");
        }
    }

    private function cancelServiceHandsetDisabled($value) {
        $this->sendSuspenServiceServicioDeshabilitado($value);
        /*         * ** Cencelando envio de alertas */
        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cencelando envio|sendProcesaAlarma");
        if ($this->dat_envioalarma->cancelEnvioByHandset($value['handsetId']))
            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|BD|Envio cancelado|sendProcesaAlarma");
        else
            $this->logger->_saveLog("ERR|H={$value['handsetId']}|PTN={$value['ptn']}|BD|Envio cancelado|sendProcesaAlarma");

        /*         * ** Cencelando alarma */
        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cencelando alarma|sendProcesaAlarma");
        if ($this->dat_alarma->dissableAlarm($value['handsetId']))
            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|BD|Alarma cancelada|sendProcesaAlarma");
        else
            $this->logger->_saveLog("ERR|H={$value['handsetId']}|PTN={$value['ptn']}|BD|Cancelando alarma|sendProcesaAlarma");
    }

    private function sendSuspenServiceServicioDeshabilitado($value) {
        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|SUSPENDIENDO el servicio..");
        $this->dat_handset->setPk($value['handsetId']);
        $this->dat_handset->n_estatus_id = 6;
        if ($this->dat_handset->save())
            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Estatus modificado a SUSPENDIDO");
        else
            $this->logger->_saveLogError("H={$value['handsetId']}|PTN={$value['ptn']}|Modificando estatus a SUSPENDIDO|" . $this->dat_handset->__getQuerySQL());
        //Notificar servicio suspendido
        //$this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 3);
    }

    private function sendGetEvtId($idHandset) {
        $pos = $this->dat_alarma->getLastPosByHandset($idHandset);
        $this->url = $this->config['alarmas']['urlPage'] . $pos;
        /*
          $idAlarma = $this->dat_alarma->getAlarmByHandset($idHandset);
          $idAlarma = sha1(md5($idAlarma . $this->config['alarmas']['seed']));
          $this->url = $this->config['alarmas']['urlPage'] . $idAlarma;
         * */
    }

    private function sendSuspenService($value) {
        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|SUSPENDIENDO el servicio..");
        $this->dat_handset->setPk($value['handsetId']);
        $this->dat_handset->n_estatus_id = 6;
        if ($this->dat_handset->save())
            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Estatus modificado a SUSPENDIDO");
        else
            $this->logger->_saveLogError("H={$value['handsetId']}|PTN={$value['ptn']}|Modificando estatus a SUSPENDIDO|" . $this->dat_handset->__getQuerySQL());
        //Notificar servicio suspendido
        $this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 2);
    }

    //tipo 1->Mensaje de advertencia de suspension
    //     2->Mensaje de aviso       de suspension
    private function sendMensajeNotificacion($idHandset, $numero, $tipo) {
        $ret = true;
        switch ($tipo) {
            case 1:
                $this->dat_mensajes->setPk(0);
                $this->dat_mensajes->n_handset_id = $idHandset;
                $this->dat_mensajes->c_mensaje_tipo = '3';
                break;
            case 2:
                $this->dat_mensajes->setPk(0);
                $this->dat_mensajes->n_handset_id = $idHandset;
                $this->dat_mensajes->c_mensaje_tipo = '4';
                break;
            case 3:
                $this->dat_mensajes->setPk(0);
                $this->dat_mensajes->n_handset_id = $idHandset;
                $this->dat_mensajes->c_mensaje_tipo = '2';
                $this->dat_mensajes->c_mensaje_mensaje = $this->config['alarmas']['msg'];
                break;
            case 4:
                $this->dat_mensajes->setPk(0);
                $this->dat_mensajes->n_handset_id = $idHandset;
                $this->dat_mensajes->c_mensaje_tipo = '8';
                break;
        }
        if (!$this->dat_mensajes->save())
            $ret = false;
        return $ret;
    }

    private function updateEstatusPayment($idHandset, $ptn, $estatus) {
        try {
            if ($this->dat_estatuspayment->updateStatusPaymentByHandset($idHandset, $estatus))
                $this->logger->_saveLog("H={$idHandset}|PTN={$ptn}|BD|Estatus de cobro actualizado|updateEstatusPayment");
            else
                $this->logger->_saveLogError("H={$idHandset}|PTN={$ptn}|BD|Actualizando estatus de cobro|{$this->dat_estatuspayment->__getQuerySQL()}|updateEstatusPayment");
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
    }

    private function sendInsertaCobroPendiente($idHandset, $ptn) {
        try {
            date_default_timezone_set("Mexico/General");
            $fechalimite = new DateTime();
            $fechalimite->add(new DateInterval('P30D'));
            $this->dat_reintentocobro->setPk(0);
            $this->dat_reintentocobro->d_reintentocobro_fechauso = date('Y-m-d 00:00:00');
            $this->dat_reintentocobro->d_reintentocobro_fechalimite = $fechalimite->format('Y-m-d 00:00:00');
            $this->dat_reintentocobro->d_reintentocobro_fechaultimoreintento = NULL;
            $this->dat_reintentocobro->n_estatus_id = 3;
            $this->dat_reintentocobro->n_reintentocobro_ultimoestatus = 11;
            $this->dat_reintentocobro->n_handset_id = $idHandset;
            $this->dat_reintentocobro->n_tipocontrato_id = 2;
            if ($this->dat_reintentocobro->save())
                $this->logger->_saveLog("H={$idHandset}|PTN={$ptn}|BD|Cobro pendiente insertado|sendInsertaCobroPendiente");
            else
                $this->logger->_saveLogError("H={$idHandset}|PTN={$ptn}|BD|Insertando cobro pendiente|{$this->dat_reintentocobro->__getQuerySQL()}|sendInsertaCobroPendiente");
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
    }

    private function sendProcesaMensajes($infoHandset) {
        $this->logger->_saveLog("H={$infoHandset['handsetId']}|PTN={$infoHandset['ptn']}|Formando el mensaje en cola para ser enviado|sendProcesaMensajes");
        $this->dat_mensajes->setPk(0);
        $this->dat_mensajes->n_handset_id = $infoHandset['handsetId'];
        $this->dat_mensajes->c_mensaje_tipo = '1';
        $this->dat_mensajes->c_mensaje_mensaje = '';
        $this->dat_mensajes->d_mensaje_fecha = date('Y-m-d H:i:s');
        $this->dat_mensajes->c_mensaje_atendido = '0';
        if ($this->dat_mensajes->save()) {
            $this->logger->_saveLog("H={$infoHandset['handsetId']}|PTN={$infoHandset['ptn']}|Mensaje formado en cola|sendProcesaMensajes");
        } else {
            $this->logger->_saveLogError("BD|H={$infoHandset['handsetId']}|PTN={$infoHandset['ptn']}|Formando mensaje en cola|sendProcesaMensajes");
        }

        //Actualizar mensajes enviados y fecha de proximo envio
        if ($this->dat_envioalarma->updateEnvioByHandset($infoHandset['handsetId'], $this->arrAlarmas[$infoHandset['handsetId']]['enviados'])) {
            $this->logger->_saveLog("H={$infoHandset['handsetId']}|PTN={$infoHandset['ptn']}|BD|Proximo envio actualizado |Mensajes enviados=" . ($this->arrAlarmas[$infoHandset['handsetId']]['enviados'] + 1) . "|sendProcesaMensajes");
        } else {
            $this->logger->_saveLogError("H={$infoHandset['handsetId']}|PTN={$infoHandset['ptn']}|BD|Actualizando proximo envio|Mensajes enviados={$this->arrAlarmas[$infoHandset['handsetId']]['enviados']}|" . $this->dat_envioalarma->__getQuerySQL() . "|sendProcesaMensajes");
        }
        $this->logger->_saveLog("H={$infoHandset['handsetId']}|PTN={$infoHandset['ptn']}|---------------Terminado proceso de envio de mensajes|sendProcesaMensajes");
    }

    /*     * *
     *   @description:  M�todo para hacer el cobro a un equipo
     *   @param:        handset (int) Id del equipo al que se aplicara el cargo
     *   @return:       response .- (int) Respuesta de la operacion
     *                          0 - Error, no se puede obtener la informacion del equipo
     *                          1 - Error, El equipo no se encuentra activo
     *                          2 - Error, El equipo no cuenta con el servicio CD01
     *                          3 - Error, El equipo es 2G con plan PREPAGO, no puedes hacer uso del servicio
     *                          4 - Error, No se pudo aplicar el cargo al equipo
     *                          5 - OK, Cargo aplicado correctamente
     *                          6 - Error de ejecucion
     *   @updater:      LM
     *   @updated_date: 13/03/2014
     * ** */

    private function sendChargeMount($handset, $idHandset, $userType) {
        try {
            $paymentMethod = NULL;
            $submarket = NULL;
            $status = NULL;
            $response = -2;
            $SC = 'CD01';

            //Obtener numero de transaccion consecutiva de la BD
            $this->transactionId = $this->dat_contadortransacciones->getTransacciones();
            $this->transactionId++;
            $this->transactionIdHash = md5($idHandset.$this->transactionId);

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|sendChargeMount");
            //Obtener paymentMethod y submarket
            if ($this->parlayx->retrieveSubscriber($handset, $SC)) {
                $retrieveSubscriberResponse = $this->parlayx->_getParlayXResponse();
                $this->logger->_saveLog("H={$idHandset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|sendChargeMount");
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');

                if (property_exists($retrieveSubscriberResponse, 'subMarketId')) {
                    $submarket = $retrieveSubscriberResponse->subMarketId;
                    if ($submarket == 'IDEN') {
                        ////////////////////////////////Flujo IDEN
                        $response = $this->IDENFlow($retrieveSubscriberResponse, $handset, $idHandset, $userType);
                        ////////////////////////////////Flujo IDEN
                    } else {
                        ////////////////////////////////Flujo 3G-LTE
                        $response = $this->_3GLTEFlow($retrieveSubscriberResponse, $handset, $idHandset, $userType);
                        ////////////////////////////////Flujo 3G-LTE
                    }
                } else {
                    $response = -1;
                    $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|SubmarketId no devuelto-CD01|sendChargeMount");
                    return $response;
                }
            } else {
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $response = 0;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|sendChargeMount");
                return $response;
            }

            //Actualizar contador de transacciones ejecutadas
            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Actualizando contador de transacciones|transactionId={$this->transactionId}|sendChargeMount");
            if ($this->dat_contadortransacciones->updateTransacciones($this->transactionId)) {
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|BD|Contador de transacciones actualizado correctamente|transactionId={$this->transactionId}|sendChargeMount");
            } else {
                $this->logger->_saveLogError("H={$idHandset}PTN={$handset}||BD|Actualizando contador de transacciones|transactionId={$this->transactionId}|" . $this->dat_contadortransacciones->__getQuerySQL() . "|sendChargeMount");
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            $response = 6;
        }
        return $response;
    }

    private function IDENFlow($retrieveSubscriberResponse, $handset, $idHandset, $userType) {
        $response = NULL;
        $paymentMethod = NULL;
        $paymentMethodBD = NULL;
        try {
            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
                switch ($userType) {
                    case 2:
                    case 3:
                        $paymentMethodBD = 2;
                        break;
                    case 1:
                        $paymentMethodBD = 1;
                        break;
                }
                $submarket = $retrieveSubscriberResponse->subMarketId;
                if ($paymentMethod == '' || $paymentMethod != '1') {
                    $SC = 'SC01';
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$paymentMethod}|paymentMethodBD={$paymentMethodBD}|IDEN-SC01-Ejecutando operacion [retrieveSubscriber] con SC01|IDENFlow");
                    if ($this->parlayx->retrieveSubscriber($handset, $SC)) {
                        $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                        if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                            if ($paymentMethod != '') {
                                //EJECUTAR COBRO CON SC01
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging] con SC01|IDENFlow");
                                if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                    $response = 5;
                                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                                } else {
                                    //REINTENTAR COBRO EQUIPOS CONTROL
                                    $_charge = false;
                                    if ($userType == 3) {
                                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|IDENFlow");
                                        if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                            $_charge = true;
                                            $response = 5;
                                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                                        }
                                    }
                                    if (!$_charge) {
                                        $this->fault = $this->parlayx->_getParlayXFault();
                                        $this->faulInfo = $this->parlayx->_getFaultInfo();
                                        $response = 4;
                                        $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging]|IDENFlow");
                                    }
                                }
                                /*                                 * ** Registrar detalle de la operacion** */
                                $this->writeXmlDetail($idHandset, 'amountCharging');
                                //EJECUTAR COBRO CON SC01
                            } else {
                                $response = -1;
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-paymentMethodListInBlank|IDENFlow");
                                return $response;
                            }
                        } else {
                            $response = -1;
                            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-SC01-Obteniendo informacion [retrieveSubscriber]|paymentMethodList no devuelto-SC01|IDENFlow");
                            return $response;
                        }
                    } else {
                        $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                        $this->fault = $this->parlayx->_getParlayXFault();
                        $this->faulInfo = $this->parlayx->_getFaultInfo();
                        $response = 0;
                        $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|Obteniendo informacion [retrieveSubscriber]|retrieveSubscriber con SC01|IDENFlow");
                        return $response;
                    }
                } else {
                    //EJECUTAR COBRO CON CD01
                    $SC = 'CD01';
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$paymentMethod}|paymentMethodBD={$paymentMethodBD}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                    if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                        $response = 5;
                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-CD01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                    } else {
                         //REINTENTAR COBRO EQUIPOS CONTROL
                        $_charge = false;
                        if ($userType == 3) {
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|IDENFlow");
                            if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                $_charge = true;
                                $response = 5;
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-CD01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                            }
                        }
                        if (!$_charge) {
                            $this->fault = $this->parlayx->_getParlayXFault();
                            $this->faulInfo = $this->parlayx->_getFaultInfo();
                            $response = 4;
                            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                        }
                    }
                    /*                     * ** Registrar detalle de la operacion** */
                    $this->writeXmlDetail($idHandset, 'amountCharging');
                    //EJECUTAR COBRO CON CD01
                }
            } else {
                $response = -1;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-CD01-Obteniendo informacion [retrieveSubscriber]|paymentMethodList no devuelto|IDENFlow");
                return $response;
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            $response = 6;
        }
        return $response;
    }

    private function _3GLTEFlow($retrieveSubscriberResponse, $handset, $idHandset,$userType) {
        $response = NULL;
        $paymentMethod = NULL;
        $paymentMethodBD = NULL;
        try {
            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
                $submarket = $retrieveSubscriberResponse->subMarketId;
                if ($paymentMethod == '') {
                    $response = -1;
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-paymentMethodListInBlank|_3GLTEFlow");
                    return $response;
                } else {
                    switch ($userType) {
                        case 2:
                        case 3:
                            $paymentMethodBD = 2;
                            break;
                        case 1:
                            $paymentMethodBD = 1;
                            break;
                    }
                    //EJECUTAR COBRO CON CD01
                    $SC = 'CD01';
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$paymentMethod}|paymentMethodBD={$paymentMethodBD}|3GLTE-CD01-Ejecutando operacion [amountCharging] con CD01|_3GLTEFlow");
                    if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                        $this->writeXmlDetail($idHandset, 'amountCharging');
                        $response = 5;
                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                        return $response;
                    } else {
                        $this->writeXmlDetail($idHandset, 'amountCharging');
                        
                        //REINTENTAR COBRO EQUIPOS CONTROL
                        if ($userType == 3) {
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|_3GLTEFlow");
                            if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                $response = 5;
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                return $response;
                            }
                        }
                        
                        $this->fault = $this->parlayx->_getParlayXFault();
                        $this->faulInfo = $this->parlayx->_getFaultInfo();
                        if ($this->faultInfo['variables'] == '4241') {
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] no exitosa-Codigo=4241|_3GLTEFlow");
                            $SC = 'CD05';
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [retrieveSubscriber] ejecutando retrieveSubscriber con CD05|_3GLTEFlow");
                            if ($this->parlayx->retrieveSubscriber($handset, $SC)) {
                                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                                if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                                    $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
                                    if ($paymentMethod != '') {
                                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Ejecutando operacion [amountCharging] con CD05|_3GLTEFlow");
                                        //EJECUTAR COBRO CON CD05
                                        if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                            $response = 5;
                                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                            return $response;
                                        } else {
                                            
                                            //REINTENTAR COBRO EQUIPOS CONTROL
                                            if ($userType == 3) {
                                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05 |Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|_3GLTEFlow");
                                                if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                                    $response = 5;
                                                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                                    return $response;
                                                }
                                            }
                                            
                                            $response = 4;
                                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-CobroFallido|_3GLTEFlow");
                                            return $response;
                                        }
                                        //EJECUTAR COBRO CON CD05
                                    } else {
                                        $response = -1;
                                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-paymentMethodListInBlank|_3GLTEFlow");
                                        return $response;
                                    }
                                } else {
                                    $response = -1;
                                    $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|3GLTE-CD05-Obteniendo informacion [retrieveSubscriber]|paymentMethodList no devuelto|_3GLTEFlow");
                                    return $response;
                                }
                            } else {
                                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                                $this->fault = $this->parlayx->_getParlayXFault();
                                $this->faulInfo = $this->parlayx->_getFaultInfo();
                                $response = 0;
                                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|3GLTE-CD05-Obteniendo informacion [retrieveSubscriber]|retrieveSubscriber|_3GLTEFlow");
                                return $response;
                            }
                        } else {
                            $response = 4;
                            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|3GLTE-CD05-Ejecutando operacion [amountCharging]|_3GLTEFlow");
                            return $response;
                        }
                    }
                    //EJECUTAR COBRO CON CD01
                }
            } else {
                $response = -1;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|3GLTE-CD01-Obteniendo informacion [retrieveSubscriber]|paymentMethodList no devuelto-CD01|_3GLTEFlow");
                return $response;
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            $response = 6;
        }
        return $response;
    }

    private function writeXmlDetail($idHandset, $op) {
        if ($this->logDetail) {
            $this->logger->_saveLogDetail("H={$idHandset}|OP=[{$op}]---------------------------------------------------------");
            $this->logger->_saveLogDetail("Target|" . $this->parlayx->__getTarget());
            $this->logger->_saveLogDetail("SoapSent|" . $this->parlayx->__getLastRequest());
            $this->logger->_saveLogDetail("SoapReceived|" . $this->parlayx->__getLastResponse());
        }
    }

    private function saveHistoricoPayment($handset, $estatus) {
        return $this->dat_historicopayment->insertHistoricoPayment($handset, $estatus, $this->transactionIdHash);
    }

}

?>