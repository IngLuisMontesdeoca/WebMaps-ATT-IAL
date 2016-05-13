<?php

class processCheckDevice {

    private $arrContactos;
    private $datContacto;
    private $parlayx;
    private $logger;
    private $config;
    private $fault;
    private $faultInfo;
    private $logDetail;
    private $msgAnt = '';
    private $db_Fail = true;
    private $verifyConnect;

    public function processCheckDevice($config = array()) {
        try {
            $this->parlayx = new parlayX();
            $this->config = $config;
            $this->logger = new logger($this->config['checkDevice']['logPath']);
            $this->logDetail = $this->config['checkDevice']['logDetail'];
            $this->verifyConnect = new verifyConnect(ROOT_ADODB . '/ini/BD.ini');
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            echo "Terminado";
        }
    }

    function verifyConnect() {
        if ($this->verifyConnect->_verifyConnectBDMysql()) {
            if ($this->db_Fail) {
                $this->datContacto = new Base_Dat_Contacto();
                $this->db_Fail = false;
            }
            return true;
        } else {
            $this->db_Fail = true;
            return false;
        }
    }

    public function checkDeviceStartProcess() {
        try {
            echo "Servicio iniciado";

            while (TRUE) {
                if ($this->verifyConnect()) {
                    $this->arrContactos = $this->datContacto->getContactosForGetCompany();
                    if (sizeof($this->arrContactos) > 0) {
                        $this->logger->_saveLog("Contactos obtenidos|checkDeviceStartProcess");
                        foreach ($this->arrContactos as $key => $value) {
                            $this->logger->_saveLog("C={$value['idContacto']}|Procesando numero {$value['numerocorreo']}|checkDeviceStartProcess");
                            if ($this->parlayx->getHandsetInfo($value['numerocorreo'])) {
                                $this->logger->_saveLog("C={$value['idContacto']}|Numero {$value['numerocorreo']} ES NEXTEL|checkDeviceStartProcess");
                                $getHandsetInfoResponse = $this->parlayx->_getParlayXResponse();

                                $this->logger->_saveLog("C={$value['idContacto']}|Actualizando tipo de contacto a 1(NEXTEL)|checkDeviceStartProcess");
                                $this->datContacto->setPk($value['idContacto']);
                                $this->datContacto->c_contacto_tipocontacto = '1';
                                if ($this->datContacto->save()) {
                                    $this->logger->_saveLog("C={$value['idContacto']}|Tipo de contacto actualizado a 1(NEXTEL)|checkDeviceStartProcess");
                                } else {
                                    $this->logger->_saveLogError("C={$value['idContacto']}|Actualizando tipo de contacto a 1(NEXTEL)|checkDeviceStartProcess");
                                }
                            } else {
                                $this->fault = $this->parlayx->_getParlayXFault();
                                $this->faulInfo = $this->parlayx->_getFaultInfo();
                                switch ($this->faulInfo['errorCode']) {
                                    case '2':
                                        $this->logger->_saveLog("ERR|C={$value['idContacto']}|Code={$value['idContacto']}|Code={$this->faulInfo['errorCode']}|Message={$this->faulInfo['faultstring']}|Variables={$this->faulInfo['variables']}|checkDeviceStartProcess");
                                        break;
                                    case '3':
                                        $this->logger->_saveLog("C={$value['idContacto']}|El numero {$value['numerocorreo']} NO ES NEXTEL|Code={$value['idContacto']}|Code={$this->faulInfo['errorCode']}|Message={$this->faulInfo['faultstring']}|Variables={$this->faulInfo['variables']}|checkDeviceStartProcess");
                                        $this->datContacto->setPk($value['idContacto']);
                                        $this->datContacto->c_contacto_tipocontacto = '3';

                                        $this->logger->_saveLog("C={$value['idContacto']}|Actualizando tipo de contacto a 3(INTERCARRIER)|checkDeviceStartProcess");
                                        if ($this->datContacto->save()) {
                                            $this->logger->_saveLog("C={$value['idContacto']}|Tipo de contacto actualizado a 3(INTERCARRIER)|checkDeviceStartProcess");
                                        } else {
                                            $this->logger->_saveLogError("C={$value['idContacto']}|Actualizando tipo de contacto a 3(INTERCARRIER)|checkDeviceStartProcess");
                                        }
                                        break;
                                    default:
                                        $this->logger->_saveLog("ERR|C={$value['idContacto']}|Code={$this->faulInfo['errorCode']}|Message={$this->faulInfo['faultstring']}|Variables={$this->faulInfo['variables']}|checkDeviceStartProcess");
                                        break;
                                }
                                $response = 0;
                                $this->logger->_saveLog("ERR|C={$value['idContacto']}|Obteniendo informacion [getHandsetInfo]|checkDeviceStartProcess");
                            }
                            $this->writeXmlDetail($value['numerocorreo'], 'getHandsetInfo');
                        }
                        $this->msgAnt = '';
                    } else {
                        if ($this->msgAnt != '!!No se encontraron numeros para procesar|checkDeviceStartProcess') {
                            $this->msgAnt = '!!No se encontraron numeros para procesar|checkDeviceStartProcess';
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
        } catch (Exception $e) {
            $this->logger->_saveLogErrorApp($e);
            echo "Terminado";
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

}
