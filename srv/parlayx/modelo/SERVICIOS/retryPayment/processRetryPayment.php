<?php

class retryPaymentProcess {

    private $estatusPayment;
    private $datHandset;
    private $dat_contadortransacciones;
    private $dat_reintentocobro;
    private $dat_alarma;
    private $dat_envioalarma;
    private $dat_mensajes;
    private $parlayx;
    private $logger;
    private $config;
    private $fault;
    private $faultInfo;
    private $logDetail;
    private $arrCobros;
    private $arrEquipos;
    private $transactionId;
    private $transactionIdHash;
    private $dat_historicopayment;

    public function retryPaymentProcess($config = array()) {
        try {
            $this->estatusPayment = new Base_Dat_EstatusPayment();
            $this->datHandset = new Base_Dat_Handset();
            $this->dat_reintentocobro = new Base_Dat_ReintentoCobro();
            $this->dat_contadortransacciones = new Base_Dat_ContadorTransacciones();
            $this->dat_alarma = new Base_Dat_Alarma();
            $this->dat_envioalarma = new Base_Dat_EnvioAlarma();
            $this->dat_mensajes = new Base_Dat_Mensajes();
            $this->dat_historicopayment = new Base_Dat_HistoricoPayment();
            $this->parlayx = new parlayX();
            $this->config = $config;
            $this->logger = new logger($this->config['retryPayment']['logPath']);
            $this->logDetail = $this->config['retryPayment']['logDetail'];
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            echo "Terminado";
        }
    }

    public function retryPaymentStartProcess() {
        try {
            $this->logger->_saveLog("Obteniendo cobros pendientes|retryPaymentStartProcess");

            $this->arrCobros = $this->dat_reintentocobro->getCobrosPendientes();

            if (sizeof($this->arrCobros[1]) > 0) {
                $this->logger->_saveLog($this->arrCobros[2] . " Cobros pendientes encontrados|Equipos->{$this->arrCobros[0]}|retryPaymentStartProcess");

                $this->logger->_saveLog("Obteniendo informacion de equipos|retryPaymentStartProcess");
                $this->arrEquipos = $this->datHandset->getHandsetInfoById($this->arrCobros[0]);

                $this->logger->_saveLog("Iniciando procesamiento de cobros|retryPaymentStartProcess");

                foreach ($this->arrCobros[1] as $keyHandset => $valueHandset) {

                    foreach ($valueHandset as $keyCobro => $valueCobro) {

                        $this->logger->_saveLog("H={$keyHandset}|P={$keyCobro}|Procesando pago|retryPaymentStartProcess");
                        $responsePayment = $this->retryPaymentChargeMount($this->arrEquipos[$keyHandset]['ptn'], $keyHandset, $this->arrEquipos[$keyHandset]['tipoUsuario']);

                        switch ($responsePayment) {
                            case -1:
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|ERROR AL OBTENER INFORMACION|Parametros no disponibles!!|retryPaymentStartProcess");
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Actualizando informacion del reintento de cobro|retryPaymentStartProcess");
                                $this->updateLastRetry($keyCobro, 3, 11, $keyHandset);
                                break;
                            case 0:
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|NO SE LOGRO OBTENER INFORMACION|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|retryPaymentStartProcess");
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Actualizando informacion del reintento de cobro|retryPaymentStartProcess");
                                $this->updateLastRetry($keyCobro, 3, 11, $keyHandset);
                                break;
                            case 1:
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|EQUIPO SUSPENDIDO|retryPaymentStartProcess");
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Actualizando informacion del reintento de cobro|retryPaymentStartProcess");
                                $this->updateLastRetry($keyCobro, 3, 11, $keyHandset);
                                $this->cancelService($value['handsetId']);
                                break;
                            case 4:
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|COBRO NO EXITOSO|retryPaymentStartProcess");
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Actualizando informacion del reintento de cobro|retryPaymentStartProcess");
                                $this->updateLastRetry($keyCobro, 3, 11, $keyHandset);
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|No se logro ejecuar el cobro|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|retryPaymentStartProcess");
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Verificando si se sobrepaso la fecha limite del reintento|retryPaymentStartProcess");
                                //if($this->arrEquipos[$keyHandset]['tipoServicio'] == '1'){
                                //Para iLarma 2.0 - Verificar  si el servicio debe ser puesto en SUSPENDIDO o LISTA NEGRA
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Verificando si el equipo requiere suspension|retryPaymentStartProcess");
                                $this->retryVerificarSuspension($this->arrEquipos[$keyHandset], $keyCobro);
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Guardando cobro en historico|retryPaymentStartProcess");
                                if ($this->saveHistoricoPayment($keyHandset, 11)) {
                                    $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Cobro guardado en historico correctamente|retryPaymentStartProcess");
                                } else {
                                    $this->logger->_saveLog("ERR|BD|H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Guardando cobro en historico|retryPaymentStartProcess");
                                }
                                //}
                                break;
                            case 5:
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|COBRO EXITOSO|retryPaymentStartProcess");
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Actualizando estatus de cobro a EXITOSO|retryPaymentStartProcess");
                                $this->estatusPayment->updateStatusPaymentByHandset($keyHandset, 10);
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Actualizando informacion del reintento de cobro|retryPaymentStartProcess");
                                $this->updateLastRetry($keyCobro, 4, 10, $keyHandset);
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Cobro efectuado|retryPaymentStartProcess");
                                //if($this->arrEquipos[$keyHandset]['tipoServicio'] == '1'){
                                //Para iLarma 2.0 - Verificar si el servicio debe ser ACTIVADO
                                //$this->logger->_saveLog("H={$keyHandset}|P={$keyCobro}|Verificando si el equipo requiere activacion|retryPaymentStartProcess");
                                //$this->retryVerificarActivacion($this->arrEquipos[$keyHandset]);                                    
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Guardando cobro en historico|retryPaymentStartProcess");
                                if ($this->saveHistoricoPayment($keyHandset, 10)) {
                                    $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Cobro guardado en historico correctamente|retryPaymentStartProcess");
                                } else {
                                    $this->logger->_saveLog("ERR|BD|H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|Guardando cobro en historico|retryPaymentStartProcess");
                                }
                                //}
                                break;
                            default:
                                $this->logger->_saveLog("H={$keyHandset}|PTN={$this->arrEquipos[$keyHandset]['ptn']}|P={$keyCobro}|!!!!Falla en tiempo de ejecucion|retryPaymentStartProcess");
                        }
                    }
                }
            } else {
                $this->logger->_saveLog("No existen cobros pendientes|retryPaymentStartProcess");
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
    }

    private function retryVerificarSuspension($value, $idReintento) {
        //Verificar fechaLimite
        $fechalimite = $this->arrCobros[1][$value['handsetId']][$idReintento]['fechalimite'];
        $fecha = new DateTime();
        $fecActual = strtotime(date("Y-m-d H:i:00", time()));
        $fecLimite = strtotime($fechalimite);
        $statusHandset = $value['estatus'];

        if ($fecActual >= $fecLimite) {
            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|El reintento de cobro {$idReintento} llego a su fecha limite {$fechalimite}|retryVerificarSuspension");

            //Pasar el equipo a lista negra
            if (!in_array($statusHandset, array('5', '12'))) {
                /*
                $this->logger->_saveLog("H={$value['handsetId']}|Pasando el equipo a lista negra|retryVerificarSuspension");
                $this->sendListaNegra($value);
                */
                
                $this->logger->_saveLog("H={$value['handsetId']}|Suspendiendo al servicio|retryVerificarSuspension");
                $this->sendSuspendService($value);
                
                //Deshabilitar envio de alarmas
                if ($this->dat_envioalarma->cancelEnvioByHandset($value['handsetId'])) {
                    $this->logger->_saveLog("H={$value['handsetId']}|Envio de mensajes deshabilitado|retryVerificarSuspension");
                } else {
                    $this->logger->_saveLogError("H={$value['handsetId']}|Deshabilitando envio de mensajes|retryVerificarSuspension|" . $this->dat_alarma->__getQuerySQL());
                }

                //Deshabilitar alarmas
                if ($this->dat_alarma->dissableAlarm($value['handsetId'])) {
                    $this->logger->_saveLog("H={$value['handsetId']}|Alarma activa deshabilitada|retryVerificarSuspension");
                } else {
                    $this->logger->_saveLogError("H={$value['handsetId']}|Deshabilitando alarma activa|retryVerificarSuspension|" . $this->dat_alarma->__getQuerySQL());
                }
            }

            //Deshabilitar reintento de cobro
            $this->updateLastRetry($idReintento, 4, 11, $value['handsetId']);
        } /* else {
          if (($value['contrato'] == '1') && !in_array($statusHandset, array('5', '6', '12'))) {
          $diasRetraso = $this->getDiasReintento($value, $idReintento);
          if ($diasRetraso == '7') {
          //Enviar mensaje de advertencia de suspension
          $this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 1);
          } else if ($diasRetraso == '8') {
          //Suspender el servicio
          $this->sendSuspendService($value);
          }
          }
          //Actualizando reintento de cobro
          $this->updateLastRetry($idReintento, 3, 11, $value['handsetId']);
          } */
    }

    private function cancelService($handsetId) {
        /*         * *** Cancelando cobro */
        $this->logger->_saveLog("H={$handsetId}|Cencelando cobro de servicio|cancelService");
        if ($this->dat_estatuspayment->updateStatusPayment($handsetId, '4')) {
            $this->logger->_saveLog("H={$handsetId}|Cobro se servicio cancelado correctamente|cancelService");
        } else {
            $this->logger->_saveLog("ERR|H={$handsetId}|Cencelando cobro de servicio|cancelService");
        }
        /*         * *** Cancelando servicio */
        $this->logger->_saveLog("H={$handsetId}|SUSPENDIENDO el servicio..");
        $this->dat_handset->setPk($handsetId);
        $this->dat_handset->n_estatus_id = 6;
        if ($this->dat_handset->save())
            $this->logger->_saveLog("H={$handsetId}|Estatus modificado a SUSPENDIDO");
        else
            $this->logger->_saveLogError("H={$handsetId}|Modificando estatus a SUSPENDIDO|" . $this->dat_handset->__getQuerySQL());
        //Notificar servicio suspendido
        //$this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 3);
    }

    private function retryVerificarActivacion($value) {
        $cobrosPend = $this->dat_reintentocobro->getPagosPendientes($value['handsetId']);
        $statusHandset = $value['estatus'];
        if ($value['contrato'] == '2') {
            //Verificar si no tiene 3 pagos pendientes y no esta en lista negra, de ser asi habilitar servicio
            if ($cobrosPend < 3 && !in_array($statusHandset, array('5', '12'))) {
                $this->activateService($value, $cobrosPend);
            } else {
                $this->logger->_saveLog("H={$value['handsetId']}|Pagos pendientes = {$cobrosPend}|Status = {$statusHandset}|El servicio no puede ser activado|retryVerificarActivacion");
            }
        } else {
            //Verificar si el servicio esta suspendido y habilitarlo
            if (!in_array($statusHandset, array('5', '12'))) {
                $this->activateService($value, $cobrosPend);
            } else {
                $this->logger->_saveLog("H={$value['handsetId']}|Pagos pendientes = {$cobrosPend}|Status = {$statusHandset}|El servicio no puede ser activado|retryVerificarActivacion");
            }
        }
    }

    private function activateService($value, $cobrosPend) {
        $this->logger->_saveLog("H={$value['handsetId']}|Pagos pendientes = {$cobrosPend}|Activando el servicio|activateService");
        $this->datHandset->setPk($value['handsetId']);
        $this->datHandset->n_estatus_id = 3;
        if ($this->datHandset->save()) {
            $this->logger->_saveLog("H={$value['handsetId']}|Servicio activado|activateService");
        } else {
            $this->logger->_saveLogError("ERR|H={$value['handsetId']}|Activando el servicio|activateService");
        }
        $this->logger->_saveLog("H={$value['handsetId']}|Activando el envio de mensajes|activateService");
        if ($this->dat_envioalarma->activateEnvioByHandset($value['handsetId'])) {
            $this->logger->_saveLog("H={$value['handsetId']}|Envio de mensajes activado|activateService");
        } else {
            $this->logger->_saveLogError("ERR|H={$value['handsetId']}|Activando el envio de mensajes|activateService");
        }
    }

    private function updateLastRetry($idReintento, $status, $statusCobro, $keyHandset) {
        //Deshabilitar reintento de cobro
        $this->dat_reintentocobro->setPk($idReintento);
        $this->dat_reintentocobro->d_reintentocobro_fechaultimoreintento = date('Y-m-d H:i:s');
        $this->dat_reintentocobro->n_estatus_id = $status;
        $this->dat_reintentocobro->n_reintentocobro_ultimoestatus = $statusCobro;
        //Deshabilitar envio de alarmas
        if ($this->dat_reintentocobro->save()) {
            $this->logger->_saveLog("H={$keyHandset}|Estatus de ultimo reintento de cobro actualizado|updateLastRetry");
        } else {
            $this->logger->_saveLogError("H={$keyHandset}|Actualizando estatus de ultimo reintento de cobro|updateLastRetry|" . $this->dat_alarma->__getQuerySQL());
        }
    }

    private function getDiasReintento($value, $idReintento) {
        $fechaUso = $this->arrCobros[1][$value['handsetId']][$idReintento]['fechuso'];
        $fechaUso = new DateTime($fechaUso);
        $fechaActual = new DateTime();
        $diference = $fechaUso->diff($fechaActual);
        $diference = $diference->format('%a');
        return $diference;
    }

    private function sendListaNegra($value) {
        $this->logger->_saveLog("H={$value['handsetId']}|PASANDO A LISTA NEGRA el equipo..|sendListaNegra");
        $this->datHandset->setPk($value['handsetId']);
        $this->datHandset->d_handset_fechasuspension = date('Y-m-d H:i:s');
        $this->datHandset->n_estatus_id = 12;
        if ($this->datHandset->save())
            $this->logger->_saveLog("H={$value['handsetId']}|Estatus modificado a LISTA NEGRA|sendListaNegra");
        else
            $this->logger->_saveLogError("H={$value['handsetId']}|Modificando estatus a LISTA NEGRA|sendListaNegra|" . $this->dat_handset->__getQuerySQL());
        //Notificar equipo en lista negra
        $this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 3);
    }

    private function sendSuspendServiceBy2GPreapid($value) {
        $this->logger->_saveLog("H={$value['handsetId']}|SUSPENDIENDO el servicio..");
        $this->dat_handset->setPk($value['handsetId']);
        $this->dat_handset->n_estatus_id = 6;
        if ($this->dat_handset->save())
            $this->logger->_saveLog("H={$value['handsetId']}|Estatus modificado a SUSPENDIDO");
        else
            $this->logger->_saveLogError("H={$value['handsetId']}|Modificando estatus a SUSPENDIDO|" . $this->dat_handset->__getQuerySQL());
        //Notificar servicio suspendido        
        $this->dat_mensajes->setPk(0);
        $this->dat_mensajes->n_handset_id = $value['handsetId'];
        $this->dat_mensajes->c_mensaje_tipo = '2';
        $this->dat_mensajes->c_mensaje_mensaje = 'Tu equipo no tiene el servicio iAlarm habilitado, por favor comunicate con Atencion a clientes, gracias.';
        $this->dat_mensajes->d_mensaje_fecha = date('Y-m-d H:i:s');
        $this->dat_mensajes->save();
    }

    private function sendSuspendService($value) {
        $this->logger->_saveLog("H={$value['handsetId']}|SUSPENDIENDO el equipo..|sendSuspendService");
        $this->datHandset->setPk($value['handsetId']);
        $this->datHandset->d_handset_fechasuspension = date('Y-m-d H:i:s');
        $this->datHandset->n_estatus_id = 6;
        if ($this->datHandset->save())
            $this->logger->_saveLog("H={$value['handsetId']}|Estatus modificado a SUSPENDIDO|sendSuspendService");
        else
            $this->logger->_saveLogError("H={$value['handsetId']}|Modificando estatus a SUSPENDIDO|sendSuspendService|" . $this->dat_handset->__getQuerySQL());
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
                $this->dat_mensajes->c_mensaje_tipo = '5';
                $this->dat_mensajes->d_mensaje_fecha = date('Y-m-d H:i:s');
                break;
            case 2:
                $this->dat_mensajes->setPk(0);
                $this->dat_mensajes->n_handset_id = $idHandset;
                $this->dat_mensajes->c_mensaje_tipo = '9';
                $this->dat_mensajes->d_mensaje_fecha = date('Y-m-d H:i:s');
                break;
            case 3:
                $this->dat_mensajes->setPk(0);
                $this->dat_mensajes->n_handset_id = $idHandset;
                $this->dat_mensajes->c_mensaje_tipo = '7';
                $this->dat_mensajes->d_mensaje_fecha = date('Y-m-d H:i:s');
                break;
        }
        if ($this->dat_mensajes->save()) {
            $this->logger->_saveLog("H={$idHandset}|Mensaje formado en cola|sendMensajeNotificacion");
        } else {
            $this->logger->_saveLogError("BD|H={$idHandset}|Formando mensaje en cola|sendMensajeNotificacion");
            $ret = false;
        }
        return $ret;
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

    private function retryPaymentChargeMount($handset, $idHandset, $userType) {
        try {
            $paymentMethod = NULL;
            $submarket = NULL;
            $status = NULL;
            $serviceCodes = NULL;
            $response = -2;
            $SC = 'CD01';

            //Obtener numero de transaccion consecutiva de la BD
            $this->transactionId = $this->dat_contadortransacciones->getTransacciones();
            $this->transactionId++;
            $this->transactionIdHash = md5($idHandset.$this->transactionId);

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [getHandsetInfo]|retryPaymentChargeMount");
            //Obtener status y servicecodes
            if ($this->parlayx->getHandsetInfo($handset, $SC)) {
                $getHandsetInfoResponse = $this->parlayx->_getParlayXResponse();
                $status = $getHandsetInfoResponse->status;
                $serviceCodes = $getHandsetInfoResponse->serviceCodes;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Informacion [getHandsetInfo] obtenida correctamente|status={$status},serviceCodes={$serviceCodes}|retryPaymentChargeMount");
            } else {
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $response = 0;
                $this->logger->_saveLog("ERR|PTN={$handset}|H={$idHandset}|CD01-Obteniendo informacion [getHandsetInfo]|retryPaymentChargeMount");
                return $response;
            }
            /*             * ** Registrar detalle de la operacion** */
            $this->writeXmlDetail($idHandset, 'getHandsetInfo');

            //Validar status y service codes
            if ($status != 'A') {
                $response = 1;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|EN ESTATUS SUSPENDIDO|status={$status}|retryPaymentChargeMount");
                return $response;
            }

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|retryPaymentChargeMount");
            //Obtener paymentMethod y submarket
            if ($this->parlayx->retrieveSubscriber($handset, $SC)) {
                $retrieveSubscriberResponse = $this->parlayx->_getParlayXResponse();
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|retryPaymentChargeMount");
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
                    $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|SubmarketId no devuelto-CD01|retryPaymentChargeMount");
                    return $response;
                }
            } else {
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $response = 0;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|retryPaymentChargeMount");
                return $response;
            }

            //Actualizar contador de transacciones ejecutadas
            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Actualizando contador de transacciones|transactionId={$this->transactionId}|retryPaymentChargeMount");
            if ($this->dat_contadortransacciones->updateTransacciones($this->transactionId)) {
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|BD|Contador de transacciones actualizado correctamente|transactionId={$this->transactionId}|retryPaymentChargeMount");
            } else {
                $this->logger->_saveLogError("H={$idHandset}|PTN={$handset}|BD|Actualizando contador de transacciones|transactionId={$this->transactionId}|" . $this->dat_contadortransacciones->__getQuerySQL() . "|retryPaymentChargeMount");
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
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging] con SC01 y paymentMethod={$paymentMethodBD}|IDENFlow");
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

    private function _3GLTEFlow($retrieveSubscriberResponse, $handset, $idHandset, $userType) {
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
