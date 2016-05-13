<?php

class parlayXProcess {

    private $fault = NULL;
    private $faultInfo = NULL;
    private $parlayX = NULL;
    private $logger = NULL;
    private $Base = NULL;
    private $config = NULL;
    private $transactionId;
    private $transactionIdHash;

    function parlayXProcess($_WS,$_path) {
        $this->parlayX = new parlayX($_path);
        $this->config = $_WS;
        $this->logger = new logger('/var/log/nxt/ial/dev/ws/SCReception/');
    }

    public function aplyCharge($ptn, $idHandset, $userType) {
        $this->Base = new Base();
        $this->logger->_saveLog("OP|H={$idHandset}|PTN={$ptn}|Aplicando cargo|aplyCharge");
        if ($this->_charge($ptn, $idHandset, $userType)) {
            $this->logger->_saveLog("OP|OK|H={$idHandset}|PTN={$ptn}|Cargo aplicado correctamente|aplyCharge");
            return $this->transactionIdHash;
        } else {
            $this->logger->_saveLog("ERR|OP|H={$idHandset}|PTN={$ptn}|Error al aplicar cargo|aplyCharge");
            $this->logger->_saveLog("BD|H={$idHandset}|PTN={$ptn}|Almacenando reintento de cobro|aplyCharge");
            //Guadar reintento de cobro en BD
            $resReintento = $this->saveReintentoCobroSC($idHandset);
            if ($resReintento[1]) {
                $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Reintento de cobro almacenando|idReintento={$resReintento[0]}|aplyCharge");
            } else {
                $this->logger->_saveLog("ERR|BD|H={$idHandset}|PTN={$ptn}|Almacenando reintento de cobro|Query=" . $this->Base->__getQuerySQL() . "|aplyCharge");
            }
            return "501";
        }
    }

    function _charge($ptn, $idPtn = 0, $userType) {
        $response = false;
        try {
            $responsePayment = $this->sendChargeMount($ptn, $idPtn, $userType);
            switch ($responsePayment[0]) {
                case -1:
                    $this->logger->_saveLog("H={$idPtn}|P={$keyCobro}|PTN={$ptn}|ERROR AL OBTENER INFORMACION|Parametros no disponibles!!|_charge");
                    $this->logger->_saveLog("H={$idPtn}|P={$keyCobro}|PTN={$ptn}|Actualizando informacion del reintento de cobro|_charge");
                    break;
                case 0:
                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$ptn}|NO SE LOGRO OBTENER INFORMACION|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|sendProcesaAlarma");
                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$ptn}|Actualizando estatus de cobro NO EXITOSO|sendProcesaAlarma");
                    break;
                case 5:
                    $this->logger->_saveLog("BD|H={$idPtn}|PTN={$ptn}|Actualizando estatus de cobro EXITOSO|_charge|SubmarketType={$responsePayment[1]},PaymentMethod={$responsePayment[2]}");
                    if ($this->Base->updateStatusPaymentByHandset($idPtn, 10)) {
                        $this->logger->_saveLog("BD|H={$idPtn}|PTN={$ptn}|Estatus de cobro actualizado correctamente|_charge");
                    } else {
                        $this->logger->_saveLog("ERR|BD|H={$idPtn}|PTN={$ptn}|Actualizando estatus de cobro|Query=" . $this->Base->__getQuerySQL() . "|_charge");
                    }
                    $this->logger->_saveLog("WS|H={$idPtn}|PTN={$ptn}|Cobro [POR EVENTO] efectuado|_charge");

                    $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|Guardando cobro en historico|_charge");
                    if ($this->Base->saveHistoricoPayment($idPtn, 10, $this->transactionIdHash)) {
                        $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|Cobro guardado en historico correctamente|_charge");
                    } else {
                        $this->logger->_saveLog("ERR|BD|H={$idPtn}|PTN={$ptn}|Guardando cobro en historico|_charge");
                    }

                    $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|Actualizando SubmarketType y PaymentMethod|_charge");
                    if ($this->Base->updateSubMarketAndPayment($idPtn, $responsePayment[1], $responsePayment[2])) {
                        $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|SubmarketType y PaymentMethod actualizados correctamente|_charge");
                    } else {
                        $this->logger->_saveLog("ERR|BD|H={$idPtn}|PTN={$ptn}|Actualizando SubmarketType y PaymentMethod|_charge|".$this->Base->_querySQL);
                    }


                    $response = true;
                    break;
                case 4:
//Registrar cobro pendiente de evento
                    $this->logger->_saveLog("BD|H={$idPtn}|PTN={$ptn}|Actualizando estatus de cobro NO EXITOSO|_charge");
                    if ($this->Base->updateStatusPaymentByHandset($idPtn, 11)) {
                        $this->logger->_saveLog("BD|H={$idPtn}|PTN={$ptn}|Estatus de cobro actualizado correctamente|_charge");
                    } else {
                        $this->logger->_saveLog("ERR|BD|H={$idPtn}|PTN={$ptn}|Actualizando estatus de cobro|Query=" . $this->Base->__getQuerySQL() . "|_charge");
                    }
                    $this->logger->_saveLog("ERR|WS|H={$idPtn}|PTN={$ptn}|No se logro ejecuar el cobro|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|_charge");
                    $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|Guardando cobro en historico|_charge");
                    if ($this->Base->saveHistoricoPayment($idPtn, 11, $this->transactionIdHash)) {
                        $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|Cobro guardado en historico correctamente|_charge");
                    } else {
                        $this->logger->_saveLog("ERR|BD|H={$idPtn}|PTN={$ptn}|Guardando cobro en historico|_charge");
                    }
                    break;
                case 3:
                    $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|!!!!Falla en tiempo de ejecucion|_charge");
                    break;
            }
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
        }
        return $response;
    }

    function sendChargeMount($handset, $idHandset, $userType) {
        try {
            $paymentMethod = NULL;
            $submarket = NULL;
            $status = NULL;
            $response = -2;
            $SC = 'CD01';

            //Obtener numero de transaccion consecutiva de la BD
            $this->transactionId = $this->Base->getTransacciones();
            $this->transactionId++;
            $this->transactionIdHash = md5($idHandset.$this->transactionId);

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|sendChargeMount");
            //Obtener paymentMethod y submarket
            if ($this->parlayX->retrieveSubscriber($handset, $SC)) {
                $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|sendChargeMount");
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
                $this->fault = $this->parlayX->_getParlayXFault();
                $this->faulInfo = $this->parlayX->_getFaultInfo();
                $response = 0;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|sendChargeMount");
                return $response;
            }

            //Actualizar contador de transacciones ejecutadas
            $this->logger->_saveLog("H={$idHandset}|Actualizando contador de transacciones|transactionId={$this->transactionId}|sendChargeMount");
            if ($this->Base->updateTransacciones($this->transactionId)) {
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|BD|Contador de transacciones actualizado correctamente|transactionId={$this->transactionId}|sendChargeMount");
            } else {
                $this->logger->_saveLogError("H={$idHandset}|PTN={$handset}|BD|Actualizando contador de transacciones|transactionId={$this->transactionId}|" . $this->Base->__getQuerySQL() . "|sendChargeMount");
            }
            
            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
            }
            
        } catch (Exception $e) {
            $logger->_saveLogErrorApp($e);
            $response = 3;
        }
        return array($response, $submarket, $paymentMethod);
    }

    private function IDENFlow($retrieveSubscriberResponse, $handset, $idHandset, $userType) {
        $response = NULL;
        $paymentMethod = '';
        $paymentMethodBD = NULL;
        try {
            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
            }
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
                    if ($this->parlayX->retrieveSubscriber($handset, $SC)) {
                        $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                        $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
                        if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')){
                            $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
                            if ($paymentMethod != '') {
                                //EJECUTAR COBRO CON SC01
                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging] con SC01|IDENFlow");
                                if ($this->parlayX->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
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
                                        $this->fault = $this->parlayX->_getParlayXFault();
                                        $this->faulInfo = $this->parlayX->_getFaultInfo();
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
                        $this->fault = $this->parlayX->_getParlayXFault();
                        $this->faulInfo = $this->parlayX->_getFaultInfo();
                        $response = 0;
                        $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|Obteniendo informacion [retrieveSubscriber]|retrieveSubscriber con SC01|IDENFlow");
                        return $response;
                    }
                } else {
                    //EJECUTAR COBRO CON CD01
                    $SC = 'CD01';
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$paymentMethod}|paymentMethodBD={$paymentMethodBD}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                    if ($this->parlayX->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
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
                            $this->fault = $this->parlayX->_getParlayXFault();
                            $this->faulInfo = $this->parlayX->_getFaultInfo();
                            $response = 4;
                            $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                        }
                    }
                    /*                     * ** Registrar detalle de la operacion** */
                    $this->writeXmlDetail($idHandset, 'amountCharging');
                    //EJECUTAR COBRO CON CD01
                }
            /*}else {
                $response = -1;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-CD01-Obteniendo informacion [retrieveSubscriber]|paymentMethodList no devuelto|IDENFlow");
                return $response;
            }*/
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            $response = 6;
        }
        return $response;
    }

    private function _3GLTEFlow($retrieveSubscriberResponse, $handset, $idHandset, $userType) {
        $response = NULL;
        $paymentMethod = '';
        $paymentMethodBD = NULL;
        try {
            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
            }
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
                    if ($this->parlayX->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
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
                        
                        $this->fault = $this->parlayX->_getParlayXFault();
                        $this->faulInfo = $this->parlayX->_getFaultInfo();
                        if ($this->faultInfo['variables'] == '4241') {
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] no exitosa-Codigo=4241|_3GLTEFlow");
                            $SC = 'CD05';
                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [retrieveSubscriber] ejecutando retrieveSubscriber con CD05|_3GLTEFlow");
                            if ($this->parlayX->retrieveSubscriber($handset, $SC)) {
                                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                                if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                                    $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
                                    $paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
                                    if ($paymentMethod != '') {
                                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Ejecutando operacion [amountCharging] con CD05|_3GLTEFlow");
                                        //EJECUTAR COBRO CON CD05
                                        if ($this->parlayX->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
                                            $response = 5;
                                            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                                            return $response;
                                        } else {
                                            
                                            //REINTENTAR COBRO EQUIPOS CONTROL
                                            if ($userType == 3) {
                                                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05|Equipo CONTROL, cargo a SALDO FALLIFO, intentando cargar a FACTURA|_3GLTEFlow");
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
                                $this->fault = $this->parlayX->_getParlayXFault();
                                $this->faulInfo = $this->parlayX->_getFaultInfo();
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
            /*} else {
                $response = -1;
                $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|3GLTE-CD01-Obteniendo informacion [retrieveSubscriber]|paymentMethodList no devuelto-CD01|_3GLTEFlow");
                return $response;
            }*/
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            $response = 6;
        }
        return $response;
    }

    function writeXmlDetail($idHandset, $op) {
        $this->logger->_saveLogDetail("H={$idHandset}|OP=[{$op}]---------------------------------------------------------");
        $this->logger->_saveLogDetail("Target|" . $this->parlayX->__getTarget());
        $this->logger->_saveLogDetail("XMLSent|" . $this->parlayX->__getLastRequest());
        $this->logger->_saveLogDetail("XMLReceived|" . $this->parlayX->__getLastResponse());
    }

    function provisionate( $ptn , $plan ){
        
    }
    
    
    function saveHandsetSC($ptn,$plan) {
        $idHandset = 0;
        $result = false;
        $this->Base = new Base();
        //Guardar en dat_handset
        $this->logger->_saveLog("BD|OK|PTN={$ptn}|Guardando equipo en base de datos(plan = {$plan})|saveHandsetSC");
        if ($this->Base->saveHandset($ptn,$plan)) {
            $idHandset = $this->Base->conn->insert_id;
            $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Equipo guardado correctamente(plan = {$plan})|saveHandsetSC");
            //Guardar en dat_estatuspayment
            $idPayment = $this->Base->getEstatusPayment($idHandset);
            if ($idPayment == 0) {
                $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Guardando en dat_estatuspayment(plan = {$plan})|saveHandsetSC");
                if ($this->Base->saveEstatusPayment($idHandset)) {
                    $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Guardado en dat_estatuspayment correctamente(plan = {$plan})|saveHandsetSC");
                    $result = true;
                } else {
                    $this->logger->_saveLog("BD|ERR|H={$idHandset}|PTN={$ptn}|Guardando en dat_estatuspayment(plan = {$plan})|Query=" . $this->Base->__getQuerySQL() . "|saveHandsetSC");
                }
            }
        } else {
            $this->logger->_saveLog("BD|ERR|H={$idHandset}|PTN={$ptn}|Guardando en dat_handset(plan = {$plan})|Query=" . $this->Base->__getQuerySQL() . "|saveHandsetSC");
        }
        return array($idHandset, $result);
    }

    function saveReintentoCobroSC($idHandset) {
        $idReintentoCobro = 0;
        $result = false;
//Guardar Reintento de cobro
        date_default_timezone_set("Mexico/General");
        $fechalimite = new DateTime();
        $fechalimite->add(new DateInterval('P30D'));
        if ($this->Base->saveReintentoCobro($idHandset, $fechalimite->format('Y-m-d 00:00:00'))) {
            $result = true;
        }
        return array($this->Base->conn->insert_id, $result);
    }

    function updateMO($idHandset, $logger) {
        $MO = $this->Base->getHandsetMOByHandsetID($idHandset);
        $MO++;
        $logger->_saveLog("BD|H={$idHandset}|Actualizando MO|updateMO");
        if ($this->Base->updateMO($idHandset, $MO)) {
            $logger->_saveLog("BD|H={$idHandset}|MO Actualizado|MO=" . $MO . "|updateMO");
        } else {
            $logger->_saveLog("ERR|BD|H={$idHandset}|Actualizando MO|ERR={$Base->__getQuerySQL()}|updateMO");
        }
    }

}
