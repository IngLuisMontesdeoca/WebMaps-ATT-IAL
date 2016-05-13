<?php

class wilaen {

    public $target = '';
    public $requestXML = '';
    public $responseXML = '';
    public $response = array();
    public $err = '';
    public $errCode = '';
    public $strackTrace = '';

    function wilaen() {
        
    }

    /*     * *
     *   @description:  Metodo para hacer el cobro a un equipo
     *   @param:        info (array){'BillingCode'=>'CD01','PhoneNumber'=>'5563021906','ReferenceCode'=>'1'}
     *   @return:       
     *   @autor:        LM
     *   @created_date: 05/11/2014
     */

    public function _charge($info = array()) {
        try {
            require("config.php");
            require("ResponseCodes.php");
            $this->target = $_WS['Charge']['target'];
            $info['User'] = $_WS['cert']['user'];
            $info['Password'] = $_WS['cert']['password'];
            $info['BillingCode'] = $_WS['Charge']['BillingCode']['EVENT'];
            $info['ItemId'] = $_WS['Charge']['ItemId'];
            $info['ItemName'] = $_WS['Charge']['ItemName'];
            $info['ItemType'] = $_WS['Charge']['ItemType'];
            $this->_saveRequestXML($info);   
            
            //Llamar ws WILAEN via JAVA
            $_cmdPars  = $info['BillingCode'].' ';
            $_cmdPars .= $info['User'].' ';
            $_cmdPars .= $info['Password'].' ';
            $_cmdPars .= $info['PhoneNumber'].' ';
            $_cmdPars .= $info['ReferenceCode'].' ';
            $_cmdPars .= $info['ItemId'].' ';
            $_cmdPars .= $info['ItemName'].' ';
            $_cmdPars .= $info['ItemType'];            
            $response = exec("java -jar /var/www/nxt/ial/srv/parlayx/v1.0.2/include/classes/Wilaen/httpRequest.jar {$_cmdPars}");
            $responseArr = explode("///", $response);
            $this->responseXML = str_replace("???","",$responseArr[1]);
            $xml = simplexml_load_string($this->responseXML);
            $this->response['Code'] = $xml->Code->__toString();
            $this->response['Description'] = $xml->Description->__toString();
            $this->response['Transaction'] = $xml->Transaction->__toString();
            $json = explode(",",str_replace(array("\"","{","}"),"",$xml->AdditionalParams->__toString()));
            $this->response['SubmarketType'] = explode(":",$json[0])[1];
            $this->response['PaymentMethod'] = explode(":",$json[1])[1];
            $this->response['Step'] = $xml->Step->__toString();
            $this->response['Amount'] = $xml->Amount->__toString();
            if ($this->response['Code'] == "0") {
                return true;
            } else {
                $this->err = $_rCode[$this->response['Code']];
                $this->errCode = $this->response['Code'];
                $this->strackTrace = $this->response['Description'];
                return false;
            }
            return true;
        } catch (Exception $ex) {
            $this->err = $ex->getMessage();
            $this->errCode = $ex->getCode();
            $this->strackTrace = $ex->getTraceAsString();
            return false;
        }
    }

    /*     * *
     *   @description:  Metodo para hacer el cobro a un equipo
     *   @param:        info (array){'shortCode'=>'425272','msisdn'=>'5563021906','message'=>'Message'}
     *   @return:       
     *   @autor:        LM
     *   @created_date: 05/11/2014
     */

    function SendMessageService($info = array()) {
        try {
            require("config.php");
            $this->target = $_WS['Charge']['target'];
            $info['user'] = $_WS['cert']['user'];
            $info['password'] = $_WS['cert']['password'];
            $info['partnerId'] = $_WS['cert']['partnerId'];
            $info['messageType'] = 'SMS';
            $info['operatorId'] = '1';
            $data = json_encode($info);
            $this->_saveRequestXML($data);
            $options = array("http" => array(
                    "method" => "POST",
                    "header" => array("Content-Type: application/json"),
                    "content" => $data
            ));
            $context = stream_context_create($options);
            $this->responseXML = file_get_contents($this->target, false, $context);
            $parse = explode("CDATA[", $this->responseXML);
            $this->response = $parse[1];
            $parse = explode("]]", $xmlSms);
            $this->response = $parse[0];
            if ($this->response == "Success") {
                return true;
            } else {
                $this->err = $this->response;
                return false;
            }
        } catch (Exception $ex) {
            $this->err = $ex->getMessage();
            $this->errCode = $ex->getCode();
            $this->strackTrace = $ex->getTraceAsString();
            return false;
        }
    }

    private function _saveRequestXML($data) {
        $this->requestXML = '<?xml version="1.0" encoding="UTF-8"?><BillingGateway xmlns="http://wilaen.com/BillingGateway/services">';
        foreach ($data as $key => $value)
            $this->requestXML .= '<'.$key.'>'.$value.'</'.$key.'>';
        $this->requestXML .= '</BillingGateway>';
    }

    public function __getLastRequest() {
        return $this->requestXML;
    }

    public function __getLastResponse() {
        return $this->responseXML;
    }

    public function __getTarget() {
        return $this->target;
    }

}
