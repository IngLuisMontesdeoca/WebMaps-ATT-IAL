<?php

class logger {

    public $_logPath = '';

    public function logger($_logPath = '') {
        $this->_logPath = $_logPath;
    }

    public function _saveLog($message = '') {
        $fecha = date("Ymd");
        $this->searchFolder($fecha);
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}/Activity.log", "a");
        $grabar = fwrite($fichero, "{$message}\n");
        $cerrar = fclose($fichero);
    }

    public function _saveLogDetail($message = '') {
        $fecha = date("Ymd");
        $this->searchFolder($fecha);
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}/XML_WILAEN.log", "a");
        $grabar = fwrite($fichero, "{$message}\n");
        $cerrar = fclose($fichero);
    }
    
    public function _saveLogDetailRequest($message = '') {
        $fecha = date("Ymd");
        $this->searchFolder($fecha);
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}/XML_SC.log", "a");
        $grabar = fwrite($fichero, "{$message}\n");
        $cerrar = fclose($fichero);
    }
    
    public function _saveLogError($message = '') {
        $fecha = date("Ymd");
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}_Error.log", "a");
        $grabar = fwrite($fichero, "{$message}\n");
        $cerrar = fclose($fichero);
    }

    public function _saveLogErrorApp($e) {
        $fecha = date("Ymd");
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}_ErrorDetail.log", "a");
        $grabar = fwrite($fichero, "Message=" . $e->getMessage() . "|StackTrace=" . $e->getTraceAsString() . "\n");
        $cerrar = fclose($fichero);
    }

    //-----------       LOGS LOCATOR   -----------------/

    public function _saveLogLocator($message = '') {
        $fecha = date("Ymd");
        $this->searchFolder($fecha);
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}/Locator.log", "a");
        $grabar = fwrite($fichero, "{$message}\n");
        $cerrar = fclose($fichero);
    }

    public function _saveLogLocatorError($message = '') {
        $fecha = date("Ymd");
        $this->searchFolder($fecha);
        $message = date('H:i:s') . ' - ' . $message;
        $fichero = fopen("{$this->_logPath}{$fecha}/Locator_Error.log", "a");
        $grabar = fwrite($fichero, "{$message}\n");
        $cerrar = fclose($fichero);
    }
    
    private function searchFolder($name) {
        if (!file_exists("{$this->_logPath}{$name}")) {
            mkdir("{$this->_logPath}{$name}");
        }
    }

}
