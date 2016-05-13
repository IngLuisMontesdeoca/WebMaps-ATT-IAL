<?php

require_once 'lib/nusoap/nusoap.php';
require_once 'Parlayx/logger.class.php';
require_once 'Parlayx/parlayX.class.php';

class test {

    public $parlayX;
    public $config;
    public $logger;
    public $transactionId;

    function test() {
        $_path = '/var/www/pro/ial/call/';
        $this->parlayX = new parlayX($_path);
        require_once '/var/www/pro/ial/call/Parlayx/configWS.php';
        $this->config = $_WS;
        $this->logger = new logger('/var/log/nxt/ial/ws/Pruebas20160510/');
    }

    function getHandsetInfo($handset, $SC) {
        $this->transactionId = 1;
        $this->transactionId++;
        $this->transactionIdHash = md5($handset . $this->transactionId);
        if ($this->parlayX->getHandsetInfo($handset, $SC)) {
            $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
            $this->logger->_saveLog("PTN={$handset}|CD01-Informacion [getHandsetInfo] obtenida correctamente|getHandsetInfo|status={$retrieveSubscriberResponse->status},paymentMethods={$retrieveSubscriberResponse->paymentMethods}|getHandsetInfo");
            echo "PTN={$handset}|CD01-Informacion [getHandsetInfo] obtenida correctamente|getHandsetInfo|status={$retrieveSubscriberResponse->status},paymentMethods={$retrieveSubscriberResponse->paymentMethods}|getHandsetInfo";
            $this->writeXmlDetail($handset, 'retrieveSubscriber');
            return $retrieveSubscriberResponse;
        } else {
            $this->writeXmlDetail($handset, 'retrieveSubscriber');
            //$this->fault = $this->parlayX->_getParlayXFault();
            //$this->faulInfo = $this->parlayX->_getFaultInfo();
            $response = 0;
            $this->logger->_saveLog("ERR|PTN={$handset}|CD01-Obteniendo informacion [getHandsetInfo]|getHandsetInfo");
            return false;
        }
    }

    function retrieveSubscriber($handset, $SC) {
        if ($this->parlayX->retrieveSubscriber($handset, $SC)) {
            $retrieveSubscriberResponse = $this->parlayX->_getParlayXResponse();
            $this->logger->_saveLog("PTN={$handset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|retrieveSubscriber");
            echo "PTN={$handset}|CD01-Informacion [retrieveSubscriber] obtenida correctamente|paymentMethod={$retrieveSubscriberResponse->paymentMethodList},submarket={$retrieveSubscriberResponse->subMarketId}|retrieveSubscriber";
            $this->writeXmlDetail($handset, 'retrieveSubscriber');
            return $retrieveSubscriberResponse;
        } else {
            $this->writeXmlDetail($handset, 'retrieveSubscriber');
            //$this->fault = $this->parlayX->_getParlayXFault();
            //$this->faulInfo = $this->parlayX->_getFaultInfo();
            $response = 0;
            $this->logger->_saveLog("ERR|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|retrieveSubscriber");
            echo "ERR|PTN={$handset}|CD01-Obteniendo informacion [retrieveSubscriber]|retrieveSubscriber";
            return false;
        }
    }

//$paymentMethodBD 1-factura,2-saldo
    function amountCharging($handset, $paymentMethodBD, $submarket, $SC) {
        if ($this->parlayX->amountCharging($handset, $paymentMethodBD, $this->config['payment']['mount'], $this->config['payment']['itemId'], $this->config['payment']['itemName'], $this->config['payment']['idContentProvider'], $this->config['payment']['itemType'], $this->transactionIdHash, $submarket, $SC)) {
            $this->writeXmlDetail($handset, 'amountCharging');
            $response = 5;
            $this->logger->_saveLog("PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|amountCharging");
            echo "PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] efectuada correctamente|amountCharging";
            return $response;
        } else {
            $this->writeXmlDetail($handset, 'amountCharging');
            echo "ERR1|PTN={$handset}|CD01-Aplicando cargo [retrieveSubscriber]|amountCharging";
            $this->logger->_saveLog("ERR!!!|PTN={$handset}|3GLTE-CD01-Operacion [amountCharging] no exitosa|FaultCode:".$this->parlayX->faultInfo['faultcode']."|".$this->parlayX->faultInfo['faultstring']."|amountCharging");
            return false;
        }
    }

    function writeXmlDetail($idHandset, $op) {
        $this->logger->_saveLogDetail("H={$idHandset}|OP=[{$op}]---------------------------------------------------------");
        $this->logger->_saveLogDetail("Target|" . $this->parlayX->__getTarget());
        $this->logger->_saveLogDetail("XMLSent|" . $this->parlayX->__getLastRequest());
        $this->logger->_saveLogDetail("XMLReceived|" . $this->parlayX->__getLastResponse());
        $this->logger->_saveLogDetail("XMLFault|" . $this->parlayX->_getParlayXFault());
    }

}

$_t = new test();
//CD01,SC01,CD05
//echo $_t->getHandsetInfo("525563022778","CD01");
//Flujo 

//$_ptn = '525563022778';
//$_ptn = '525510907924';
//$_ptn = '525569667954';
$_ptn = '525569663699';

$_t->logger->_saveLog("--------------------------------------------");
$getHandsetInfoReponse = $_t->getHandsetInfo($_ptn, "CD01");
if ($getHandsetInfoReponse) {
    if ($getHandsetInfoReponse->status == 'A') {
        $retrieveSubscriberResponse = $_t->retrieveSubscriber($_ptn, "CD01");
        $SC = 'CD05';
        if ($retrieveSubscriberResponse->subMarketId == 'iDEN') 
            $SC = 'CD01';
        $retrieveSubscriberResponse = $_t->retrieveSubscriber($_ptn, $SC);
        if ($retrieveSubscriberResponse) {
            //$retrieveSubscriberResponse->paymentMethodList|1-Factura, 2-Saldo
            //Control devu
            $_t->amountCharging($_ptn, $retrieveSubscriberResponse->paymentMethodList, $retrieveSubscriberResponse->subMarketId, "CD01");
        }
    }
}
 