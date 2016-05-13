<?php

/* * ******************************************************************************
 *                                                           					*
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>   					*
 *   @version:       1.0                                     					*
 *   @created:       05/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Clase para conectar al WS 									*
 * 					https://129.192.129.104:8080/sigservices/lookup				*
 * ****************************************************************************** */

class determineSubmarket {

    public $client;
    private $soapFault;
    private $soapResponse;
    public $faultInfo;
    public $wsdl;

    function determineSubmarket() {
        
    }

    function _getClient() {
        require("config.php");
        $this->wsdl = $_WS['determinePayment']['target'] . "?wsdl";
        $clientOptions = array("soap_version" => SOAP_1_1, "encoding" => "UTF-8",
            "compression" => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            "location" => $_WS['determinePayment']['target'],
            'trace' => 1);
        $this->client = new SoapClient($this->wsdl, $clientOptions);
        $header_part = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                            <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                                                   	<wsse:Username>' . $_WS['determinePayment']['Username'] . '</wsse:Username>
							<wsse:Password>' . $_WS['determinePayment']['Password'] . '</wsse:Password>
							<wsu:Created>2015-04-20T18:08:49.909Z</wsu:Created>
                                            </wsse:UsernameToken>
                                            </wsse:Security>';
        $soap_var_header = new SoapVar($header_part, XSD_ANYXML, null, null, null);
        $soap_header = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'wsse', $soap_var_header);
        $this->client->__setSoapHeaders($soap_header);
    }

    /*
      function _getSubscriberInfo($msisdn = '') {
      $getSubscriberInfo = array("msisdn" => $msisdn);
      try {
      $getSubscriberInfoResponse = $this->client->getSubscriberInfo($getSubscriberInfo);
      $this->soapResponse = $getSubscriberInfoResponse->return;
      return true;
      } catch (SoapFault $ex) {
      $this->soapFault = $ex;
      return false;
      }
      }
     */

    function _retrieveSubscriber($msisdn = '', $serviceCode = '') {
        $retrieveSubscriber = array("msisdn" => $msisdn, "serviceCode" => $serviceCode);
        try {
            $retrieveSubscriberResponse = $this->client->retrieveSubscriber($retrieveSubscriber);
            $this->soapResponse = $retrieveSubscriberResponse->return;
            return true;
        } catch (SoapFault $ex) {
            $this->soapFault = $ex;
            return false;
        }
    }

    function _getHandsetInfo($msisdn = '', $serviceCode = '') {
        $getHandsetInfo = array("identifierType" => "MSISDN",
            "identifierValue" => $msisdn,
            "contentType" => NULL,
            "contentSubs" => NULL,
            "giftFlag" => NULL,
            "receiverFlag" => NULL,
            "barredServicesType" => $serviceCode,
            "paymentMethod" => NULL);
        try {
            $getHandsetInfoResponse = $this->client->getHandsetInfo($getHandsetInfo);
            $this->soapResponse = $getHandsetInfoResponse->return;
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
        if (property_exists( $this->soapFault, 'detail')) {
            $exc = $this->soapFault->detail;
            $this->faultInfo['exceptionName'] = (property_exists($exc, 'exceptionName')) ? $exc->exceptionName : "Uknnown";
            if (property_exists($exc, 'PolicyException')) {
                $this->faultInfo['errorCode'] = $exc->PolicyException->errorCode;
                $this->faultInfo['message'] = $exc->PolicyException->message;
                $this->faultInfo['variables'] = (property_exists($exc->PolicyException, 'variables')) ? _getFaultVariablesCode($exc->PolicyException->variables) : "Empty";
            } else if (property_exists($exc, 'ServiceException')) {
                $this->faultInfo['errorCode'] = $exc->ServiceException->errorCode;
                $this->faultInfo['message'] = $exc->ServiceException->message;
                $this->faultInfo['variables'] = (property_exists($exc->ServiceException, 'variables')) ? _getFaultVariablesCode($exc->ServiceException->variables) : "Empty";
            } else if (property_exists($exc, 'MCDSException')) {
                $this->faultInfo['errorCode'] = $exc->MCDSException->errorCode;
                $this->faultInfo['message'] = $exc->MCDSException->message;
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