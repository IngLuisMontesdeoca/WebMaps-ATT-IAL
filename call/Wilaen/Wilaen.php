<?php

function aplyCharge($ptn, $idHandset, $Base, $logger) {
    $logger->_saveLog("OP|H={$idHandset}|Aplicando cargo|aplyCharge");
    if (_charge($ptn, $idHandset, $Base, $logger)) {
        $logger->_saveLog("OP|OK|H={$idHandset}|Cargo aplicado correctamente|aplyCharge");
        return "0";
    } else {
        $logger->_saveLog("ERR|OP|H={$idHandset}|Error al aplicar cargo|aplyCharge");
        $logger->_saveLog("BD|H={$idHandset}|Almacenando reintento de cobro|aplyCharge");
        //Guadar reintento de cobro en BD
        $resReintento = saveReintentoCobroSC($idHandset, $Base);
        if ($resReintento[1]) {
            $logger->_saveLog("BD|OK|H={$idHandset}|Reintento de cobro almacenando|idReintento={$resReintento[0]}|aplyCharge");
        } else {
            $logger->_saveLog("ERR|BD|H={$idHandset}|Almacenando reintento de cobro|Query=" . $Base->__getQuerySQL() . "|aplyCharge");
        }
        return "501";
    }
}

function _charge($ptn, $idPtn = 0, $Base, $logger) {
    $response = false;
    try {
        $responsePayment = sendChargeMount($ptn, $idPtn, $logger, $Base);
        switch ($responsePayment[0]) {
            case 1:
                $logger->_saveLog("BD|H={$idPtn}|Actualizando estatus de cobro EXITOSO|_charge|SubmarketType={$responsePayment[3]},PaymentMethod={$responsePayment[4]}");
                if ($Base->updateStatusPaymentByHandset($idPtn, 10)) {
                    $logger->_saveLog("BD|H={$idPtn}|Estatus de cobro actualizado correctamente|_charge");
                } else {
                    $logger->_saveLog("ERR|BD|H={$idPtn}|Actualizando estatus de cobro|Query=" . $Base->__getQuerySQL() . "|_charge");
                }
                $logger->_saveLog("WS|H={$idPtn}|Cobro [POR EVENTO] efectuado|_charge");
                
                $logger->_saveLog("H={$idPtn}|Guardando cobro en historico|_charge");
                if ($Base->saveHistoricoPayment($idPtn, 10,$responsePayment[2])) {
                    $logger->_saveLog("H={$idPtn}|Cobro guardado en historico correctamente|_charge");
                } else {
                    $logger->_saveLog("ERR|BD|H={$idPtn}|Guardando cobro en historico|_charge");
                }
                
                $logger->_saveLog("H={$idPtn}|Actualizando SubmarketType y PaymentMethod|_charge");
                if ($Base->updateSubMarketAndPayment($idPtn, $responsePayment[3], $responsePayment[4])) {
                    $logger->_saveLog("H={$idPtn}|SubmarketType y PaymentMethod actualizados correctamente|_charge");
                } else {
                    $logger->_saveLog("ERR|BD|H={$idPtn}|Actualizando SubmarketType y PaymentMethod|_charge");                    
                }
                
                
                $response = true;
                break;
            case 2:
                //Registrar cobro pendiente de evento
                $logger->_saveLog("BD|H={$idPtn}|Actualizando estatus de cobro NO EXITOSO|_charge");
                if ($Base->updateStatusPaymentByHandset($idPtn, 11)) {
                    $logger->_saveLog("BD|H={$idPtn}|Estatus de cobro actualizado correctamente|_charge");
                } else {
                    $logger->_saveLog("ERR|BD|H={$idPtn}|Actualizando estatus de cobro|Query=" . $Base->__getQuerySQL() . "|_charge");
                }
                $logger->_saveLog("ERR|WS|H={$idPtn}|No se logro ejecuar el cobro|Code = {$responsePayment[1]['variables']}, Desc = {$responsePayment[1]['messageId']},Response = {$responsePayment[1]['text']}|_charge");
                $logger->_saveLog("H={$idPtn}|Guardando cobro en historico|_charge");
                if ($Base->saveHistoricoPayment($idPtn, 11,$responsePayment[2])) {
                    $logger->_saveLog("H={$idPtn}|Cobro guardado en historico correctamente|_charge");
                } else {
                    $logger->_saveLog("ERR|BD|H={$idPtn}|Guardando cobro en historico|_charge");
                }
                break;
            case 3:
                $logger->_saveLog("H={$idPtn}|!!!!Falla en tiempo de ejecucion|_charge");
                break;
        }
    } catch (Exception $e) {
        $logger->_saveLogErrorApp($e);
    }
    return $response;
}

function sendChargeMount($handset, $idHandset, $logger, $Base) {
    $faultInfo = array();
    $transaction = '';
    $subMarket = '';
    $paymentMethod = '';
    try {
        //Obtener numero de transaccion consecutiva de la BD
        $transactionId = $Base->getTransacciones();
        $transactionId++;

        $wilaen = new wilaen();
        $response = 0;
        $logger->_saveLog("H={$idHandset}|Ejecutando operacion [Charge]|sendChargeMount");
        //if ($this->parlayx->amountCharging($handset, $this->paymentMethod, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $transactionId, $this->submarket)) {
        if ($wilaen->_charge(array('PhoneNumber' => $handset, 'ReferenceCode' => '1'))) {
            $transaction = $wilaen->response['Transaction'];
            $subMarket = $wilaen->response['SubmarketType'];
            $paymentMethod = $wilaen->response['PaymentMethod'];
            $response = 1;
            $logger->_saveLog("H={$idHandset}|Operacion [Charge] efectuada correctamente|sendChargeMount");
        } else {
            $transaction = $wilaen->response['Transaction'];
            $faultInfo['variables'] = $wilaen->errCode;
            $faultInfo['messageId'] = $wilaen->err;
            $faultInfo['text'] = $wilaen->strackTrace;
            $response = 2;
            $logger->_saveLog("ERR|H={$idHandset}|Ejecutando operacion [Charge]|sendChargeMount");
        }
        //***************Registrar Detalle de operacion
        writeXMLDetailWilaen($idHandset, 'Charge', $wilaen, $logger);

        //Actualizar contador de transacciones ejecutadas
        $logger->_saveLog("H={$idHandset}|Actualizando contador de transacciones|transactionId={$transactionId}|sendChargeMount");
        if ($Base->updateTransacciones($transactionId)) {
            $logger->_saveLog("H={$idHandset}|BD|Contador de transacciones actualizado correctamente|transactionId={$transactionId}|sendChargeMount");
        } else {
            $logger->_saveLogError("H={$idHandset}|BD|Actualizando contador de transacciones|transactionId={$transactionId}|Query=" . $Base->__getQuerySQL() . "|sendChargeMount");
        }
    } catch (Exception $e) {
        $logger->_saveLogErrorApp($e);
        $response = 3;
    }
    return array($response, $faultInfo,$transaction,$subMarket,$paymentMethod);
}

function writeXMLDetailWilaen($idHandset, $op, $wilaen, $logger) {
    $logger->_saveLogDetail("H={$idHandset}|OP=[{$op}]---------------------------------------------------------");
    $logger->_saveLogDetail("Target|" . $wilaen->__getTarget());
    $logger->_saveLogDetail("XMLSent|" . $wilaen->__getLastRequest());
    $logger->_saveLogDetail("XMLReceived|" . $wilaen->__getLastResponse());
}

function saveHandsetSC($ptn, $Base, $logger) {
    $idHandset = 0;
    $result = false;
    //Guardar en dat_handset
    $logger->_saveLog("BD|OK|H={$ptn}|Guardando equipo en base de datos|saveHandsetSC");
    if ($Base->saveHandset($ptn)) {
        $idHandset = $Base->conn->insert_id;
        $logger->_saveLog("BD|OK|H={$idHandset}|Equipo guardado correctamente|saveHandsetSC");
        //Guardar en dat_estatuspayment
        $idPayment = $Base->getEstatusPayment($idHandset);
        if ($idPayment == 0) {
            $logger->_saveLog("BD|OK|H={$idHandset}|Guardando en dat_estatuspayment|saveHandsetSC");
            if ($Base->saveEstatusPayment($idHandset)) {
                $logger->_saveLog("BD|OK|H={$idHandset}|Guardado en dat_estatuspayment correctamente|saveHandsetSC");
                $result = true;
            } else {
                $logger->_saveLog("BD|ERR|H={$ptn}|Guardando en dat_estatuspayment|Query=" . $Base->__getQuerySQL() . "|saveHandsetSC");
            }
        }
    } else {
        $logger->_saveLog("BD|ERR|H={$ptn}|Guardando en dat_handset|Query=" . $Base->__getQuerySQL() . "|saveHandsetSC");
    }
    return array($idHandset, $result);
}

function saveReintentoCobroSC($idHandset, $Base) {
    $idReintentoCobro = 0;
    $result = false;
    //Guardar Reintento de cobro
    date_default_timezone_set("Mexico/General");
    $fechalimite = new DateTime();
    $fechalimite->add(new DateInterval('P30D'));
    if ($Base->saveReintentoCobro($idHandset, $fechalimite->format('Y-m-d 00:00:00'))) {
        $result = true;
    }
    return array($Base->conn->insert_id, $result);
}

function updateMO($idHandset, $Base , $logger){
    $MO = $Base->getHandsetMOByHandsetID($idHandset);
    $MO++;
    $logger->_saveLog("BD|H={$idHandset}|Actualizando MO|updateMO");
    if($Base->updateMO($idHandset,$MO)){
        $logger->_saveLog("BD|H={$idHandset}|MO Actualizado|MO=".$MO."|updateMO");
    }else{
        $logger->_saveLog("ERR|BD|H={$idHandset}|Actualizando MO|ERR={$Base->__getQuerySQL()}|updateMO");
    }
}
