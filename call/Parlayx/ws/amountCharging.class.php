<?php

/* * ******************************************************************************
 *                                                           					*
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>   					*
 *   @version:       1.0                                     					*
 *   @created:       05/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Clase para conectar al WS 									*
 * 					https://129.192.129.104:8080/mcdsservices/subscription		*
 * ****************************************************************************** */

class amountCharging {

    public $client;
    private $soapFault;
    private $soapResponse;
    public $faultInfo;
    public $wsdl;

    function amountCharging() {
        
    }

    function _getClient($_WS) {
        $this->wsdl = $_WS['amountCharging']['target'] . "?wsdl";
        $localCert = $_WS['cert']['path'];
        $clientOptions = array("soap_version" => SOAP_1_1, "encoding" => "UTF-8",
            "compression" => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            "location" => $_WS['amountCharging']['target'],
            'trace' => 1);
        $this->client = new SoapClient($this->wsdl);
        $header_part = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                                                <wsse:UsernameToken>
								<wsse:Username>' . $_WS['amountCharging']['Username'] . '</wsse:Username>
								<wsse:Password>' . $_WS['amountCharging']['Password'] . '</wsse:Password>
                                                                </wsse:UsernameToken>
                                                        </wsse:Security>';
        $soap_var_header = new SoapVar($header_part, XSD_ANYXML, null, null, null);
        $soap_header = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'wsse', $soap_var_header);
        $this->client->__setSoapHeaders($soap_header);
    }

    function _chargeAmount($msisdn = '', $amount = '', $description = array(), $SC) {
        $chargeAmountResponse = NULL;
        $description = $description['PaymentMethod'] . '|' .
                $description['idContentProvider'] . '|' .
                $description['Receiver_MSISDN'] . '|' .
                $description['Charged_MSISDN'] . '|' .
                $description['totalAmount'] . '|' .
                $description['datePurchase'] . '|' .
                $description['ItemId'] . '|' .
                $description['itemName'] . '|' .
                $description['itemType'] . '|' .
                $description['transactionId'] . '|' .
                $description['currency'] . '|' .
                $description['submarket'];
        $ChargingInformation = array('description' => $description, 'currency' => 'MXN', 'amount' => $amount, 'code' => NULL);
        $chargeAmount = array('endUserIdentifier' => 'tel:' . $msisdn, 'charge' => $ChargingInformation, 'referenceCode' => $SC);
        try {
            $chargeAmountResponse = $this->client->chargeAmount($chargeAmount);
            $this->soapResponse = $chargeAmountResponse;
            //var_dump($this->soapResponse );
            return true;
        } catch (SoapFault $ex) {
            $this->soapFault = $ex;
            return false;
        }
    }

    function _getSoapResponse() {
        return $this->soapResponse;
    }

    function _getSoapFault() {
        $this->__formatFault();
        return $this->soapFault;
    }

    function _getFaultInfo() {
        return $this->faultInfo;
    }

    private function __formatFault() {
        $this->faultInfo['faultcode'] = (property_exists($this->soapFault, 'faultcode')) ? $this->soapFault->faultcode : "Uknnown";
        $this->faultInfo['faultstring'] = (property_exists($this->soapFault, 'faultstring')) ? $this->soapFault->faultstring : "Uknnown";
        $this->faultInfo['errorCode'] = '';
        $this->faultInfo['message'] = '';
        $this->faultInfo['variables'] = '';

	if (property_exists($this->soapFault, 'detail')) {
            $exc = $this->soapFault->detail;
            $this->faultInfo['exceptionName'] = (property_exists($exc, 'exceptionName')) ? $exc->exceptionName : "Uknnown";
            if (property_exists($exc, 'PolicyException')) {
                $this->faultInfo['errorCode'] = $exc->PolicyException->messageId;
                $this->faultInfo['message'] = $exc->PolicyException->text;
                $this->faultInfo['variables'] = (property_exists($exc->PolicyException, 'variables')) ? _getFaultVariablesCode($exc->PolicyException->variables) : "Empty";
            } else if (property_exists($exc, 'ServiceException')) {
                $this->faultInfo['errorCode'] = $exc->ServiceException->messageId;
                $this->faultInfo['message'] = $exc->ServiceException->text;
                $this->faultInfo['variables'] = (property_exists($exc->ServiceException, 'variables')) ? _getFaultVariablesCode($exc->ServiceException->variables) : "Empty";
            } else if (property_exists($exc, 'MCDSException')) {
                $this->faultInfo['errorCode'] = $exc->MCDSException->messageId;
                $this->faultInfo['message'] = $exc->MCDSException->text;
                $this->faultInfo['variables'] = (property_exists($exc->MCDSException, 'variables')) ? _getFaultVariablesCode($exc->MCDSException->variables) : "Empty";
            } else {
                $this->faultInfo['errorCode'] = "Unknown";
                $this->faultInfo['message'] = "Unknown";
                $this->faultInfo['variables'] = "Unknown";
            }
        } else {
            $this->faultInfo['errorCode'] = "Unknown";
            $this->faultInfo['message'] = "Unknown";
            $this->faultInfo['variables'] = "Unknown";
        }
        var_dump($this->faultInfo);
    }

    function _getFaultVariablesCode($variables) {
        if (is_array($variables)) {
            foreach ($variables as $k => $v)
                if ($v != '')
                    return $v;
        } else
            return $variables;
    }

    function __getFunctions() {
        return $this->client->__getFunctions();
    }

    function __getTypes() {
        return $this->client->__getTypes();
    }

    function __getLastRequest() {
        return $this->client->__getLastRequest();
    }

    function __getLastResponse() {
        return $this->client->__getLastResponse();
    }

}

?>