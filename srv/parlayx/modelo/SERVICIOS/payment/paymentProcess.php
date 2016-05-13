<?php

class paymentProcess {

    private $estatusPayment;
    private $datHandset;
    private $datmensajes;
    private $dat_envioalarma;
    private $dat_alarma;
    private $dat_contadortransacciones;
    private $dat_reintentocobro;
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

    public function paymentProcess($config = array()) {
        try {
            $this->estatusPayment = new Base_Dat_EstatusPayment();
            $this->datHandset = new Base_Dat_Handset();
            $this->datmensajes = new Base_Dat_Mensajes();
            $this->dat_alarma = new Base_Dat_Alarma();
            $this->dat_envioalarma = new Base_Dat_EnvioAlarma();
            $this->dat_reintentocobro = new Base_Dat_ReintentoCobro();
            $this->dat_contadortransacciones = new Base_Dat_ContadorTransacciones();
            $this->dat_historicopayment = new Base_Dat_HistoricoPayment();
            $this->parlayx = new parlayX();
            $this->config = $config;
            $this->logger = new logger($this->config['payment']['logPath']);
            $this->logDetail = $this->config['payment']['logDetail'];
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            echo "Terminado";
        }
    }

    public function paymentStartProcess() {
        try {
            $this->logger->_saveLog("Obteniendo cobros para el dia actual|paymentStartProcess");

            $this->arrCobros = $this->estatusPayment->getCobros();
            if (sizeof($this->arrCobros[1]) > 0) {
                $this->logger->_saveLog(sizeof($this->arrCobros[1]) . " Cobros encontrados para el dia de hoy|Equipos->{$this->arrCobros[0]}|paymentStartProcess");

                $this->logger->_saveLog("Obteniendo informacion de equipos|paymentStartProcess");
                $this->arrEquipos = $this->datHandset->getHandsetInfoById($this->arrCobros[0]);

                $this->logger->_saveLog("Iniciando procesamiento de cobros|paymentStartProcess");
                foreach ($this->arrEquipos as $key => $value) {

                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Procesando pago|paymentStartProcess");
                    $responsePayment = $this->paymentChargeMount($value['ptn'], $value['handsetId'], $value['tipoUsuario']);

                    $fechaCorte = $this->paymentGetNextCutDate($value['diaCorte']);

                    switch ($responsePayment) {
                        case -1:
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|ERROR AL OBTENER INFORMACION|Paremtros no disponibles!!|paymentStartProcess");
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Actualizando estatus de cobro NO EXITOSO|Proximo Corte={$fechaCorte}|paymentStartProcess");
                            $this->estatusPayment->updateStatusPaymentByHandsetMensual($value['handsetId'], 11, $fechaCorte);
                            break;
                        case 0:
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|NO SE LOGRO OBTENER INFORMACION|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|paymentStartProcess");
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Actualizando estatus de cobro NO EXITOSO|Proximo Corte={$fechaCorte}|paymentStartProcess");
                            $this->estatusPayment->updateStatusPaymentByHandsetMensual($value['handsetId'], 11, $fechaCorte);
                            break;
                        case 1:
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|EQUIPO SUSPENDIDO|paymentStartProcess");
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Actualizando estatus de cobro NO EXITOSO|Proximo Corte={$fechaCorte}|paymentStartProcess");
                            $this->estatusPayment->updateStatusPaymentByHandsetMensual($value['handsetId'], 11, $fechaCorte);
                            $this->cancelService($value['handsetId']);
                            break;
                        case 4:
                            //Registrar cobro pendiente de evento
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|COBRO NO EXITOSO|Proximo Corte={$fechaCorte}|paymentStartProcess");
                            $this->estatusPayment->updateStatusPaymentByHandsetMensual($value['handsetId'], 11, $fechaCorte);
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|No se logro ejecuar el cobro|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|paymentStartProcess");
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Insertando cobro pendiente|paymentStartProcess");
                            $this->paymentInsertaCobroPendiente($value['handsetId']);
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|retryPaymentStartProcess");
                            if ($this->saveHistoricoPayment($value['handsetId'], 11)) {
                                $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro guardado en historico correctamente|retryPaymentStartProcess");
                            } else {
                                $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|query=" . $this->dat_historicopayment->_query . "|retryPaymentStartProcess");
                            }
                            //$this->sendMensajeNotificacion($value['handsetId'], $value['ptn'], 4);
                            break;
                        case 5:
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|COBRO EXITOSO|Proximo Corte={$fechaCorte}|paymentStartProcess");
                            $this->estatusPayment->updateStatusPaymentByHandsetMensual($value['handsetId'], 10, $fechaCorte);
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro [MENSUAL] efectuado|paymentStartProcess");
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|retryPaymentStartProcess");
                            if ($this->saveHistoricoPayment($value['handsetId'], 10)) {
                                $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Cobro guardado en historico correctamente|retryPaymentStartProcess");
                            } else {
                                $this->logger->_saveLog("ERR|BD|H={$value['handsetId']}|PTN={$value['ptn']}|Guardando cobro en historico|query=" . $this->dat_historicopayment->_query . "|retryPaymentStartProcess");
                            }
                            break;
                        default:
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|!!!!Falla en tiempo de ejecucion|paymentStartProcess");
                    }

                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|Verificando si el equipo esta suspendido o eliminado|paymentStartProcess");
                    if ($value['estatus'] != '3') {
                        $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|El equipo esta suspendido..Deshabilitando proximo pago|paymentStartProcess");
                        if ($this->estatusPayment->updateStatusPayment($value['handsetId'], 4))
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|BD|Proximo pago deshabilitado|paymentStartProcess");
                        else
                            $this->logger->_saveLog("H={$value['handsetId']}|PTN={$value['ptn']}|BD|Deshabilitando proximo pago|paymentStartProcess");
                    }
                }
            } else {
                $this->logger->_saveLog("No existen cobros para el dia de hoy|paymentStartProcess");
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
    }

    private function cancelService($handsetId) {
        /*         * *** Cancelando cobro */
        $this->logger->_saveLog("H={$handsetId}|Cencelando cobro de servicio|cancelService");
        if ($this->estatusPayment->updateStatusPayment($handsetId, '4')) {
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

    //31 - Enero,Marzo, Mayo, Julio, Agosto, Octubre, Diciembre        
    //30 - Abril, Junio,Septiembre, Noviembre
    //28 - Febrero 
    function paymentGetNextCutDate($diaCorte) {
        $dia = $diaCorte;
        $mes = date('m');
        $ano = date('Y');
        $nextDia = NULL;
        $nextMes = $mes + 1;
        $nextAno = $ano;

        if ($mes == '12') {
            $nextMes = 1;
            $nextAno = $ano + 1;
        }

        if ($nextMes < 10)
            $nextMes = '0' . $nextMes;

        if (in_array($nextMes, array('04', '06', '09', '11'))) {
            if ($dia > 30)
                $nextDia = '30';
        }

        if ($nextMes == '02') {
            $diaFeb = (checkdate(02, 29, $ano)) ? 29 : 28;
            if ($dia > $diaFeb)
                $nextDia = $diaFeb;
        }

        if ($nextDia == NULL)
            $nextDia = $dia;

        return date($nextAno . '-' . $nextMes . '-' . $nextDia . ' 00:00:00');
    }

    private function paymentInsertaCobroPendiente($idHandset) {
        try {
            date_default_timezone_set("Mexico/General");
            $fechalimite = new DateTime();
            $fechalimite->add(new DateInterval('P30D'));
            $this->dat_reintentocobro->setPk(0);
            $this->dat_reintentocobro->d_reintentocobro_fechauso = date('Y-m-d  00:00:00');
            $this->dat_reintentocobro->d_reintentocobro_fechalimite = $fechalimite->format('Y-m-d  00:00:00');
            $this->dat_reintentocobro->d_reintentocobro_fechaultimoreintento = NULL;
            $this->dat_reintentocobro->n_estatus_id = 3;
            $this->dat_reintentocobro->n_reintentocobro_ultimoestatus = 11;
            $this->dat_reintentocobro->n_handset_id = $idHandset;
            $this->dat_reintentocobro->n_tipocontrato_id = 1;
            if ($this->dat_reintentocobro->save())
                $this->logger->_saveLog("H={$idHandset}|BD|Cobro pendiente insertado|paymentInsertaCobroPendiente");
            else
                $this->logger->_saveLogError("H={$idHandset}|BD|Insertando cobro pendiente|{$this->dat_reintentocobro->__getQuerySQL()}|paymentInsertaCobroPendiente");
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
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

    private function paymentChargeMount($handset, $idHandset, $userType) {
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

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Obteniendo informacion [getHandsetInfo]|paymentChargeMount");
            //Obtener status y servicecodes
            if ($this->parlayx->getHandsetInfo($handset, $SC)) {
                $getHandsetInfoResponse = $this->parlayx->_getParlayXResponse();
                $status = $getHandsetInfoResponse->status;
                $serviceCodes = $getHandsetInfoResponse->serviceCodes;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Informacion [getHandsetInfo] obtenida correctamente|status={$status},serviceCodes={$serviceCodes}|paymentChargeMount");
            } else {
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $response = 0;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|Obteniendo informacion [getHandsetInfo]|paymentChargeMount");
                return $response;
            }
            /*             * ** Registrar detalle de la operacion** */
            $this->writeXmlDetail($idHandset, 'getHandsetInfo');

            //Validar status y service codes
            if ($status != 'A') {
                $response = 1;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|EN ESTATUS SUSPENDIDO|status={$status}|paymentChargeMount");
                return $response;
            }

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Obteniendo informacion [retrieveSubscriber]|paymentChargeMount");
            //Obtener paymentMethod y submarket
            if ($this->parlayx->retrieveSubscriber($handset, $SC)) {
                $retrieveSubscriberResponse = $this->parlayx->_getParlayXResponse();
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|paymentChargeMount");

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
                    $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|Obteniendo informacion [retrieveSubscriber]|SubmarketId no devuelto-CD01|paymentChargeMount");
                    return $response;
                }
            } else {
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                $this->fault = $this->parlayx->_getParlayXFault();
                $this->faulInfo = $this->parlayx->_getFaultInfo();
                $response = 0;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|Obteniendo informacion [retrieveSubscriber]|paymentChargeMount");
                return $response;
            }

            //Actualizar contador de transacciones ejecutadas
            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Actualizando contador de transacciones|transactionId={$this->transactionId}|paymentChargeMount");
            if ($this->dat_contadortransacciones->updateTransacciones($this->transactionId)) {
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|BD|Contador de transacciones actualizado correctamente|transactionId={$this->transactionId}|paymentChargeMount");
            } else {
                $this->logger->_saveLogError("H={$idHandset}|PTN={$handset}|BD|Actualizando contador de transacciones|transactionId={$this->transactionId}|" . $this->dat_contadortransacciones->__getQuerySQL() . "|paymentChargeMount");
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
                //$paymentMethodBD = 1;
                
                switch ($userType) {
                    case 2:
                        $paymentMethodBD = 2;
                        break;
                    case 1:
                    case 3:
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
                                if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                    $response = 5;
                                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                                } else {
                                    //REINTENTAR COBRO EQUIPOS CONTROL
                                    /*
                                    $_charge = false;
                                    if ($userType == 3) {
                                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|IDENFlow");
                                        if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                            $_charge = true;
                                            $response = 5;
                                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                                        }
                                    }
                                    if (!$_charge) {*/
                                        $this->fault = $this->parlayx->_getParlayXFault();
                                        $this->faulInfo = $this->parlayx->_getFaultInfo();
                                        $response = 4;
                                        $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging]|IDENFlow");
                                    //}
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
                    if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                        $response = 5;
                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-CD01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                    } else {
                        //REINTENTAR COBRO EQUIPOS CONTROL
                        /*
                        $_charge = false;
                        if ($userType == 3) {
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|IDENFlow");
                            if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                $_charge = true;
                                $response = 5;
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-CD01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                            }
                        }
                        if (!$_charge) {
                         */
                            $this->fault = $this->parlayx->_getParlayXFault();
                            $this->faulInfo = $this->parlayx->_getFaultInfo();
                            $response = 4;
                            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                        //}
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
                    //$paymentMethodBD = 1;
                    switch ($userType) {
                        case 2:
                            $paymentMethodBD = 2;
                            break;
                        case 1:
                        case 3:
                            $paymentMethodBD = 1;
                            break;
                    }
                     
                    //EJECUTAR COBRO CON CD01
                    $SC = 'CD01';
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$paymentMethod}|paymentMethodBD={$paymentMethodBD}|3GLTE-CD01-Ejecutando operacion [amountCharging] con CD01|_3GLTEFlow");
                    if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                        $this->writeXmlDetail($idHandset, 'amountCharging');
                        $response = 5;
                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                        return $response;
                    } else {
                        $this->writeXmlDetail($idHandset, 'amountCharging');

                        //REINTENTAR COBRO EQUIPOS CONTROL
                        /*
                        if ($userType == 3) {
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|_3GLTEFlow");
                            if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                $response = 5;
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                return $response;
                            }
                        }
                        */
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
                                        if ($this->parlayx->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                            $response = 5;
                                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                            return $response;
                                        } else {
                                            
                                            //REINTENTAR COBRO EQUIPOS CONTROL
                                            /*
                                            if ($userType == 3) {
                                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05 |Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|_3GLTEFlow");
                                                if ($this->parlayx->amountCharging($handset, 1, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemNameIlmimitado'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                                    $response = 5;
                                                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                                    return $response;
                                                }
                                            }
                                            */
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
                            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|3GLTE-CD01-Ejecutando operacion [amountCharging]|_3GLTEFlow");
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
