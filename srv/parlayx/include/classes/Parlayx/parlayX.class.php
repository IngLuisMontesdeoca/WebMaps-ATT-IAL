<?php

/* * ******************************************************************************
 *                                                           					*
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>   					*
 *   @version:       1.0                                     					*
 *   @created:       06/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Clase para consumir los WSs de parlay-X						*
 * ****************************************************************************** */

class parlayX {

    public $parlayXClient;
    public $parlayXResponse;
    public $parlayXFault;
    public $faultInfo;

    function parlayX() {
        require_once("ws/determineSubmarket.class.php");
        require_once("ws/determinePayment.class.php");
        require_once("ws/amountCharging.class.php");
        require_once("ws/sendSms.class.php");
    }

    public function retrieveSubscriber($msisdn = '' , $serviceCode = '') {
        $this->parlayXClient = new determineSubmarket();
        $this->parlayXClient->_getClient();
        if ($this->parlayXClient->_retrieveSubscriber($msisdn,$serviceCode)) {
            $this->parlayXResponse = $this->parlayXClient->_getSoapResponse();
            return true;
        } else {
            $this->parlayXFault = $this->parlayXClient->_getSoapFault();
            $this->faultInfo=$this->parlayXClient->_getFaultInfo();
            return false;
        }
    }

    public function getHandsetInfo($msisdn = '' , $serviceCode = '') {
        $this->parlayXClient = new determineSubmarket();
        $this->parlayXClient->_getClient();
        if ($this->parlayXClient->_getHandsetInfo($msisdn , $serviceCode)) {
            $this->parlayXResponse = $this->parlayXClient->_getSoapResponse();
	    $this->parlayXResponse->serviceCodes = $this->getServiceCode($serviceCode);
            return true;
        } else {
            $this->parlayXFault = $this->parlayXClient->_getSoapFault();
            $this->faultInfo=$this->parlayXClient->_getFaultInfo();
            return false;
        }
    }

    function getServiceCode($serviceCode = '') {
        $sc = '';
        if (is_array($this->parlayXResponse->serviceCodes)) {
            foreach ($this->parlayXResponse->serviceCodes as $key => $val) {
                if ($val = $serviceCode)
                    $sc = $val;
            }
        }else {
            if ($this->parlayXResponse->serviceCodes == $serviceCode)
                $sc = $this->parlayXResponse->serviceCodes;
        }
        return $sc;
    }

    public function amountCharging($msisdn = '', $paymentMethod = '', $amount = '', $itemId = '', $itemName = '', $idContentProvider = '',$itemType = '', $transactionId = '', $submarket = '',$SC='') {
        $transID = $transactionId + 10000000000;
        $transID = $idContentProvider . $transID;
           
        $this->parlayXClient = new amountCharging();
        $this->parlayXClient->_getClient();
        $description = array(
            'PaymentMethod' => $paymentMethod,
            'idContentProvider' => $idContentProvider,
            'Receiver_MSISDN' => $msisdn,
            'Charged_MSISDN' => $msisdn,
            'totalAmount' => $amount,
            'datePurchase' => '20140305134001',
            'ItemId' => $itemId,
            'itemName' => $itemName,
            'itemType' => $itemType,
            'transactionId' => $transID,
            'currency' => 'MXN',
            'submarket' => $submarket);
        if ($this->parlayXClient->_chargeAmount($msisdn, $amount, $description,$SC)) {
            $this->parlayXResponse = $this->parlayXClient->_getSoapResponse();
            return true;
        } else {
            $this->parlayXFault = $this->parlayXClient->_getSoapFault();
            $this->faultInfo=$this->parlayXClient->_getFaultInfo();
            return false;
        }
    }

    public function sendSms($msisdn = '', $senderName = '', $mensaje = '' ) {
        $this->parlayXClient = new sendSms();
        $this->parlayXClient->_getClient();
        if ($this->parlayXClient->_sendSms($msisdn, $senderName, $mensaje)) {
            $this->parlayXResponse = $this->parlayXClient->_getSoapResponse();
            return true;
        } else {
            $this->parlayXFault = $this->parlayXClient->_getSoapFault();
            $this->faultInfo=$this->parlayXClient->_getFaultInfo();
            return false;
        }
    }

    public function _getParlayXResponse() {
        return $this->parlayXResponse;
    }

    public function _getParlayXFault() {
        return $this->parlayXFault;
    }

    function _getFaultInfo() {
        return $this->faultInfo;
    }

    function __getLastRequest() {
        return $this->parlayXClient->__getLastRequest();
    }
    
    function __getLastResponse() {
        return $this->parlayXClient->__getLastResponse();
    }
    
    function __getTarget(){
        return $this->parlayXClient->wsdl;
    }
}
