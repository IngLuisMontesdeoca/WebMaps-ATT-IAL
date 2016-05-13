<?php

/* * ******************************************************************************
 *                                                           					*
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>   					*
 *   @version:       1.0                                     					*
 *   @created:       14/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Clase para conectar al WS 									*
 * 					https://129.192.129.106:7001/ParlayXSmsAccess/services/SendSms		*
 * ****************************************************************************** */

class sendSms {

    public $client;
    private $soapFault;
    private $soapResponse;
    public $faultInfo;
    public $wsdl;

    function sendSms() {
        
    }

    function _getClient() {
        require("config.php");
        $this->wsdl = $_WS['sendSms']['target'] . "?wsdl";
        $clientOptions = array("soap_version" => SOAP_1_1, "encoding" => "UTF-8",
            "compression" => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            "location" => $_WS['sendSms']['target'],
            'trace' => 1);        
        $this->client = new SoapClient( $this->wsdl, $clientOptions);
        $header_part = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                                                <wsse:UsernameToken>
								<wsse:Username>' . $_WS['sendSms']['Username'] . '</wsse:Username>
								<wsse:Password>' . $_WS['sendSms']['Password'] . '</wsse:Password>
                                                                </wsse:UsernameToken>
                                                        </wsse:Security>';
        $soap_var_header = new SoapVar($header_part, XSD_ANYXML, null, null, null);
        $soap_header = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'wsse', $soap_var_header);
        $this->client->__setSoapHeaders($soap_header);
    }

    function _sendSms($msisdn = '', $senderName = '', $message = '') {
        $sendSmsResponse = NULL;
        try {
            $body_part = '<loc:sendSms  xmlns:loc="http://www.csapi.org/schema/parlayx/sms/send/v2_1/local">
                                                                                 <loc:addresses>tel:'.$msisdn.'</loc:addresses>
                                                                                 <loc:senderName>'.$senderName.'</loc:senderName>
                                                                                 <loc:message>'.$message.'</loc:message>
                                                                          </loc:sendSms>';
            $soapstruct = new SoapVar($body_part, XSD_ANYXML, null, null, null);
            $sendSmsResponse = $this->client->sendSms($soapstruct);
            $this->soapResponse = $sendSmsResponse->result;
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
        $this->faultInfo['faultcode'] = (property_exists($this->soapFault, 'faultcode'))? $this->soapFault->faultcode : "Uknnown"; 
	$this->faultInfo['faultstring'] = (property_exists($this->soapFault, 'faultstring'))? $this->soapFault->faultstring : "Uknnown"; 
	$this->faultInfo['errorCode'] = '';
        $this->faultInfo['message'] = '';
        $this->faultInfo['variables'] = '';
        
	if (property_exists($soapFault, 'detail')) {
		$exc = $this->soapFault->detail;
		$this->faultInfo['exceptionName'] = (property_exists($exc, 'exceptionName'))? $exc->exceptionName : "Uknnown"; 
		if (property_exists($exc, 'PolicyException')) {
			$this->faultInfo['errorCode'] = $exc->PolicyException->messageId;
			$this->faultInfo['message'] = $exc->PolicyException->text;
			$this->faultInfo['variables'] = (property_exists($exc->PolicyException, 'variables'))? _getFaultVariablesCode($exc->PolicyException->variables) : "Empty";
		}else if (property_exists($exc, 'ServiceException')) {
			$this->faultInfo['errorCode'] = $exc->ServiceException->messageId;
			$this->faultInfo['message'] = $exc->ServiceException->text;
			$this->faultInfo['variables'] = (property_exists($exc->ServiceException, 'variables'))? _getFaultVariablesCode($exc->ServiceException->variables) : "Empty";
		}else if (property_exists($exc, 'MCDSException')) {
			$this->faultInfo['errorCode'] = $exc->MCDSException->messageId;
			$this->faultInfo['message'] = $exc->MCDSException->text;
			$this->faultInfo['variables'] = (property_exists($exc->MCDSException, 'variables'))? _getFaultVariablesCode($exc->MCDSException->variables) : "Empty";
		}else{
			$this->faultInfo['errorCode'] = "Unknown";
			$this->faultInfo['message'] = "Unknown";
			$this->faultInfo['variables'] = "Unknown";
		}
	}else{
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

class SOAPStruct {

    function SOAPStruct($s, $i, $f) {
        $this->addresses = $s;
        $this->senderName = $i;
        $this->message = $f;
    }

}

?>
