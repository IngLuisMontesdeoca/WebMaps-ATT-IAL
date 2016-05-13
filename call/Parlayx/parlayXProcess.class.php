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
    public $userNetwork = '';
    public $paymentMethod = '';

    function parlayXProcess($_WS, $_path) {
        $this->parlayX = new parlayX($_path);
        $this->config = $_WS;
        $this->logger = new logger('/var/log/nxt/ial/ws/SCReception/');
    }

    public function aplyCharge($ptn, $idHandset) {
        $this->Base = new Base();
        $this->logger->_saveLog("OP|H={$idHandset}|PTN={$ptn}|Aplicando cargo|aplyCharge");
        if ($this->_charge($ptn, $idHandset)) {
            $this->logger->_saveLog("OP|OK|H={$idHandset}|PTN={$ptn}|Cargo aplicado correctamente|aplyCharge");
            return $this->transactionIdHash;
        } else {
            $this->logger->_saveLog("ERR|OP|H={$idHandset}|PTN={$ptn}|Error al aplicar cargo|aplyCharge");
            //EnviarSMS
            if( $this->Base->message($idHandset, "", 9))
                $this->logger->_saveLog("OP|H={$idHandset}|PTN={$ptn}|Envio de SMS almacenado en BD");
            else
                $this->logger->_saveLog("ERR|OP|H={$idHandset}|PTN={$ptn}|Almacenando envio de SMS en BD");
            return "501";
        }
    }

    function _charge($ptn, $idPtn = 0) {
        $response = false;
        try {
            $responsePayment = $this->sendChargeMount($ptn, $idPtn);
            switch ($responsePayment) {
                case -1:
                    $this->logger->_saveLog("H={$idPtn}|P={$keyCobro}|PTN={$ptn}|ERROR AL OBTENER INFORMACION|Parametros no disponibles!!|_charge");
                    $this->logger->_saveLog("H={$idPtn}|P={$keyCobro}|PTN={$ptn}|Actualizando informacion del reintento de cobro|_charge");
                    break;
                case 0:
                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$ptn}|NO SE LOGRO OBTENER INFORMACION|MessageID = {$this->faulInfo['errorCode']},Code = {$this->faulInfo['variables']}, Desc = {$this->faulInfo['faultstring']}|sendProcesaAlarma");
                    $this->logger->_saveLog("H={$value['handsetId']}|PTN={$ptn}|Actualizando estatus de cobro NO EXITOSO|sendProcesaAlarma");
                    break;
                case 5:
                    $this->logger->_saveLog("BD|H={$idPtn}|PTN={$ptn}|Actualizando estatus de cobro EXITOSO|_charge|SubmarketType={$this->userNetwork},PaymentMethod={$this->paymentMethod}");
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
                    if ($this->Base->updateSubMarketAndPayment($idPtn, $this->userNetwork, $this->paymentMethod)) {
                        $this->logger->_saveLog("H={$idPtn}|PTN={$ptn}|SubmarketType y PaymentMethod actualizados correctamente|_charge");
                    } else {
                        $this->logger->_saveLog("ERR|BD|H={$idPtn}|PTN={$ptn}|Actualizando SubmarketType y PaymentMethod|_charge|" . $this->Base->_querySQL);
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

    function sendChargeMount($handset, $idHandset) {
        try {
            $status = NULL;
            $response = -2;
            $SC = 'CD01';

            //Obtener numero de transaccion consecutiva de la BD
            $this->transactionId = $this->Base->getTransacciones();
            $this->transactionId++;
            $this->transactionIdHash = md5($idHandset . $this->transactionId);

            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|sendChargeMount");
            //Obtener paymentMethod y submarket
            if ($this->parlayX->retrieveSubscriber($handset, $SC)) {
                $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|sendChargeMount");
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');

                if (property_exists($retrieveSubscriberResponse, 'subMarketId')) {
                    $this->userNetwork = $retrieveSubscriberResponse->subMarketId;
                    if ($this->userNetwork == 'IDEN')
                        $response = $this->IDENFlow($retrieveSubscriberResponse, $handset, $idHandset);
                    else
                        $response = $this->_3GLTEFlow($retrieveSubscriberResponse, $handset, $idHandset);
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
            if ($this->Base->updateTransacciones($this->transactionId))
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|BD|Contador de transacciones actualizado correctamente|transactionId={$this->transactionId}|sendChargeMount");
            else
                $this->logger->_saveLogError("H={$idHandset}|PTN={$handset}|BD|Actualizando contador de transacciones|transactionId={$this->transactionId}|" . $this->Base->__getQuerySQL() . "|sendChargeMount");
        } catch (Exception $e) {
            $logger->_saveLogErrorApp($e);
            $response = 3;
        }
        return $response;
    }

    private function IDENFlow($retrieveSubscriberResponse, $handset, $idHandset) {
        $response = NULL;
        try {
            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList'))
                $this->paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
            else
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01 No se logro obtener paymentMthodList con CD01|IDENFlow");

            if ($this->paymentMethod == '1') {
                $SC = 'CD01';
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$this->paymentMethod}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                if ($this->parlayX->amountCharging($handset, $this->paymentMethod, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $this->userNetwork, $SC)) {
                    $response = 5;
                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-CD01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                } else {
                    $this->fault = $this->parlayX->_getParlayXFault();
                    $this->faulInfo = $this->parlayX->_getFaultInfo();
                    $response = 4;
                    $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-CD01-Ejecutando operacion [amountCharging]|IDENFlow");
                }
                $this->writeXmlDetail($idHandset, 'amountCharging');
                return $response;
            }

            $SC = 'SC01';
            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$this->paymentMethod}|IDEN-SC01-Ejecutando operacion [retrieveSubscriber] con SC01|IDENFlow");
            if ($this->parlayX->retrieveSubscriber($handset, $SC)) {
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
                if (property_exists($retrieveSubscriberResponse, 'paymentMethodList')) {
                    $this->paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
                    if ($this->paymentMethod == '') {
                        $response = -1;
                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-paymentMethodListInBlank|IDENFlow");
                        return $response;
                    }

                    $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging] con SC01|IDENFlow");
                    if ($this->parlayX->amountCharging($handset, $this->paymentMethod, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $this->userNetwork, $SC)) {
                        $response = 5;
                        $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|IDEN-SC01-Operacion [amountCharging] efectuada correctamente|IDENFlow");
                    } else {
                        $this->fault = $this->parlayX->_getParlayXFault();
                        $this->faulInfo = $this->parlayX->_getFaultInfo();
                        $response = 4;
                        $this->logger->_saveLog("ERR|H={$idHandset}|PTN={$handset}|IDEN-SC01-Ejecutando operacion [amountCharging]|IDENFlow");
                    }
                    $this->writeXmlDetail($idHandset, 'amountCharging');
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
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            $response = 6;
        }
        return $response;
    }

    private function _3GLTEFlow($retrieveSubscriberResponse, $handset, $idHandset) {
        $response = NULL;
        try {
            $SC = 'CD05';
            if (!$this->parlayX->retrieveSubscriber($handset, $SC)) {
                $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-Operacion [retrieveSubscriber]No se logro obtener informacion|_3GLTEFlow");
                return 0;
            }

            $this->writeXmlDetail($idHandset, 'retrieveSubscriber');
            $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();

            if (property_exists($retrieveSubscriberResponse, 'paymentMethodList'))
                $this->paymentMethod = $retrieveSubscriberResponse->paymentMethodList;
            if ($this->paymentMethod == '') {
                $response = -1;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD05-paymentMethodListInBlank|_3GLTEFlow");
                return $response;
            }

            $SC = 'CD01';
            $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|paymentMethod={$this->paymentMethod}|3GLTE-CD01-Ejecutando operacion [amountCharging] con CD01|_3GLTEFlow");
            if ($this->parlayX->amountCharging($handset, $this->paymentMethod, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $this->userNetwork, $SC)) {
                $this->writeXmlDetail($idHandset, 'amountCharging');
                $response = 5;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|_3GLTEFlow");
                return $response;
            } else {
                $this->writeXmlDetail($idHandset, 'amountCharging');
                $response = 4;
                $this->logger->_saveLog("H={$idHandset}|PTN={$handset}|3GLTE-CD01-CobroFallido|_3GLTEFlow");
                return $response;
            }
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
    
    function saveHandsetSC($ptn) {
        $idHandset = 0;
        $result = false;
        $this->Base = new Base();
        //Guardar en dat_handset
        $this->logger->_saveLog("BD|OK|PTN={$ptn}|Guardando equipo en base de datos|saveHandsetSC");
        if ($this->Base->saveHandset($ptn)) {
            $idHandset = $this->Base->conn->insert_id;
            $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Equipo guardado correctamente|saveHandsetSC");
            //Guardar en dat_estatuspayment
            $idPayment = $this->Base->getEstatusPayment($idHandset);
            if ($idPayment == 0) {
                $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Guardando en dat_estatuspayment|saveHandsetSC");
                if ($this->Base->saveEstatusPayment($idHandset)) {
                    $this->logger->_saveLog("BD|OK|H={$idHandset}|PTN={$ptn}|Guardado en dat_estatuspayment correctamente|saveHandsetSC");
                    $result = true;
                } else {
                    $this->logger->_saveLog("BD|ERR|H={$idHandset}|PTN={$ptn}|Guardando en dat_estatuspayment|Query=" . $this->Base->__getQuerySQL() . "|saveHandsetSC");
                }
            }
        } else {
            $this->logger->_saveLog("BD|ERR|H={$idHandset}|PTN={$ptn}|Guardando en dat_handset|Query=" . $this->Base->__getQuerySQL() . "|saveHandsetSC");
        }
        return array($idHandset, $result);
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
