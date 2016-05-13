<?php

class Base {

    public $conn;
    public $errorMessage;
    public $connEstatus = FALSE;
    public $_querySQL = "";

    function __construct() {
        $arrDB = parse_ini_file("BD.ini");

        $this->conn = new mysqli($arrDB["HOST"], $arrDB["USER"], $arrDB["PASSWORD"], $arrDB["DATABASE"]);
        if ($this->conn->connect_errno) {
            $this->errorMessage = $this->conn->connect_error;
            $this->conn = FALSE;
            $this->connEstatus = FALSE;
        }
        $this->connEstatus = TRUE;
    }

    function decodeHandset($PTN = '520000000000') {
        $arrConf = parse_ini_file("Config.ini");
        $query = "SELECT n_handset_id FROM dat_handset WHERE c_handset_ptn='" . $PTN . "' AND n_estatus_id = 3;";
        $idHandset = 0;
        if ($result = $this->conn->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $idHandset = $row['n_handset_id'];
            }
            $result->free();
        }
        return $idHandset;
    }

    function cancelAlarm($idHandset = 0) {
        $query = "UPDATE dat_alarma SET n_estatus_id = 5 WHERE n_handset_id = " . $idHandset . ";";
        return mysqli_query($this->conn, $query);
    }

    function cancelarMensajes($idHandset = 0) {
        $query = "UPDATE dat_envioalarma SET n_envioalarma_mensajesenviados = 0, n_estatus_id = 4 WHERE n_handset_id = " . $idHandset . ";";
        $this->errorMessage = $query;
        return mysqli_query($this->conn, $query);
    }

    function existeAlarma($idHandset = 0) {
        $query = "SELECT n_alarma_id FROM dat_alarma WHERE n_estatus_id IN (1, 9) AND n_handset_id = " . $idHandset . " LIMIT 1;";
        $idAlarma = 0;
        if ($result = $this->conn->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $idAlarma = $row['n_alarma_id'];
            }
            $result->free();
        }
        return $idAlarma;
    }

    function radio($tipoLocalizacion = 0) {
        $query = "SELECT n_tipolocalizacion_radio FROM cat_tipolocalizacion WHERE n_tipolocalizacion_id = " . $tipoLocalizacion . ";";
        $radio = 0;
        if ($result = $this->conn->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $radio = $row['n_tipolocalizacion_radio'];
            }
            $result->free();
        }
        return $radio;
    }

    function updateCodes($idHandset = 0, $serverCode = "", $clientCode = "") {
        $query = "UPDATE dat_handset SET c_handset_clientcode = '" . $clientCode . "', c_handset_servercode = '" . $serverCode . "', b_handset_flagcode = 0 WHERE n_handset_id = " . $idHandset . ";";
        $this->errorMessage = $query;
        return mysqli_query($this->conn, $query);
    }

    function updateSubMarketAndPayment($idHandset = 0, $SubmarketType, $PaymentMethod) {
        try {
            $SubmarketType = "3";
            if ($SubmarketType == 'IDEN')
                $SubmarketType = "1";
            else if ($SubmarketType == '3G')
                $SubmarketType = "2";
            
            $_paymentMethod = ($PaymentMethod != '') ? ', n_usertype_id = {$PaymentMethod}' : '';
            $this->_querySQL = "UPDATE dat_handset SET n_usernetwork_id = {$SubmarketType} {$_paymentMethod} WHERE n_handset_id = " . $idHandset . ";";
            $this->errorMessage = $this->_querySQL;
            mysqli_query($this->conn, $this->_querySQL);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function save($idHandset = 0, $lon = 0, $lat = 0, $radio = 0, $fecha = '0000-00-00 00:00:00') {
        $query = "INSERT INTO dat_alarma(d_alarma_fechainicio, 
		                          d_alarma_fechafin, 
					  d_alarma_fechaultimoreporte, 
					  d_alarma_longitude, 
					  d_alarma_latitude, 
					  n_alarma_radio, 
					  n_handset_id, 
					  n_estatus_id) 
					  VALUES(NOW(), '0000-00-00 00:00:00', '" . $fecha . "', " . $lon . ", " . $lat . ", " . $radio . ", " . $idHandset . ",9);";

        return mysqli_query($this->conn, $query);
    }

    function update($idAlarma = 0, $lon = 0, $lat = 0, $radio = 0, $fecha = '0000-00-00 00:00:00') {
        $query = "UPDATE dat_alarma SET d_alarma_fechaultimoreporte='" . $fecha . "', 
		                         d_alarma_longitude=" . $lon . ",
					 d_alarma_latitude=" . $lat . ",
					 n_alarma_radio = " . $radio . " WHERE n_alarma_id = " . $idAlarma . ";";

        return mysqli_query($this->conn, $query);
    }

    function message($idHandset = 0, $message = "", $type="2") {
        $query = "INSERT INTO dat_mensajes(n_handset_id, c_mensaje_tipo, c_mensaje_mensaje) VALUES(" . $idHandset . ", '{$type}', '" . $message . "');";
        return mysqli_query($this->conn, $query);
    }

    function close() {
        $this->conn->close();
    }

    /*     * **************************************************************** */
    /*     * *********** FUNCIONES SC-CallBack - iAlarm 1.0 ***************** */
    /*     * **************************************************************** */

    function saveHandset($ptn) {
        $this->_querySQL = "INSERT INTO dat_handset(c_handset_ptn, 
		                          d_handset_fecharegistro, 
					  c_handset_asunto, 
					  c_handset_mensaje, 
					  n_usernetwork_id, 
					  n_usertype_id, 
					  n_cuenta_id, 
					  n_tipocontrato_id,
                                          n_estatus_id,
                                          n_tiposervicio_id,
                                          n_canalsuscripcion_id) 
					  VALUES('{$ptn}',NOW(), '', '', 1, 2, 1, 2, 3, 2, 3);";
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function saveHistoricoPayment($idHandset, $estatus, $transactionID) {
        $this->_querySQL = "INSERT INTO dat_historicopayment(
		                          d_historicopayment_fecha, 
					  n_handset_id, 
					  n_estatus_id, 
					  n_tipocobro_id,
                                          c_historicopayment_transactionID) 
					  VALUES(NOW(),{$idHandset},{$estatus},1,'{$transactionID}');";
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function saveEstatusPayment($idHandset) {
        $this->_querySQL = "INSERT INTO dat_estatuspayment(d_estatuspayment_fechaproximo, 
		                          d_estatuspayment_fechaultimo, 
					  n_estatuspayment_estatusultimo, 
					  n_handset_id,
                                          n_estatus_id) 
					  VALUES(NOW(), NOW(), 11, {$idHandset}, 3);";
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function saveReintentoCobro($idHandset, $fechaLimite) {
        $this->_querySQL = "INSERT INTO dat_reintentocobro(d_reintentocobro_fechauso, 
		                          d_reintentocobro_fechalimite, 
					  d_reintentocobro_fechaultimoreintento, 
					  n_reintentocobro_ultimoestatus,
                                          n_tipocontrato_id,
                                          n_estatus_id,
                                          n_handset_id) 
					  VALUES(NOW(), '{$fechaLimite}', NULL, 11, 2,3,{$idHandset});";
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function getTipoContratoByHandsetId($idHandset) {
        $this->_querySQL = "SELECT n_tipocontrato_id FROM dat_handset WHERE n_handset_id=" . $idHandset . " and n_tiposervicio_id = 2;";
        $idTipoContrato = 0;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $idTipoContrato = $row['n_tipocontrato_id'];
            }
            $result->free();
        }
        return $idTipoContrato;
    }

    function updateStatusPaymentByHandset($idHandset, $idEstatus) {
        $fechaUltimo = ($idEstatus == 10) ? "d_estatuspayment_fechaultimo = NOW()," : "";
        $this->_querySQL = "UPDATE 	dat_estatuspayment "
                . "SET	 	{$fechaUltimo}n_estatuspayment_estatusultimo                    = {$idEstatus}"
                . " WHERE  	n_handset_id                    = {$idHandset}";
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function getEstatusPayment($idHandset = 0) {
        $this->_querySQL = "SELECT n_estatuspayment_id FROM dat_estatuspayment WHERE n_handset_id = {$idHandset};";
        $id = 0;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['n_estatuspayment_id'];
            }
            $result->free();
        }
        return $id;
    }

    function getReintentoCobro($idHandset = 0) {
        $this->_querySQL = "SELECT n_reintentocobro_id FROM dat_reintentocobro WHERE n_handset_id = {$idHandset};";
        $id = 0;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['n_reintentocobro_id'];
            }
            $result->free();
        }
        return $id;
    }

    function getTransacciones() {
        $this->_querySQL = "SELECT n_contadortransacciones_numero FROM dat_contadortransacciones;";
        $numeroTransacciones = 0;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $numeroTransacciones = $row['n_contadortransacciones_numero'];
            }
            $result->free();
        }
        return $numeroTransacciones;
    }

    function updateTransacciones($transactionId) {
        $this->_querySQL = "UPDATE dat_contadortransacciones SET n_contadortransacciones_numero = {$transactionId};";
        $this->errorMessage = $this->_querySQL;
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function getHandsetByPtn($PTN = '520000000000') {
        $this->_querySQL = "SELECT n_handset_id FROM dat_handset WHERE c_handset_ptn='" . $PTN . "' AND n_estatus_id = 3 AND n_tiposervicio_id = 2;";
        $idHandset = 0;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $idHandset = $row['n_handset_id'];
            }
            $result->free();
        }
        return $idHandset;
    }

    function getUserTypeByPtn($PTN = '520000000000') {
        $this->_querySQL = "SELECT n_usertype_id FROM dat_handset WHERE c_handset_ptn='" . $PTN . "' AND n_estatus_id = 3 AND n_tiposervicio_id = 2;";
        $_userType = 1;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $_userType = $row['n_usertype_id'];
            }
            $result->free();
        }
        return $_userType;
    }

    function getHandsetMOByHandsetID($idHandset) {
        $this->_querySQL = "SELECT n_handset_MO FROM dat_handset WHERE n_handset_id = " . $idHandset . ";";
        $MO = 0;
        if ($result = $this->conn->query($this->_querySQL)) {
            while ($row = $result->fetch_assoc()) {
                $MO = $row['n_handset_MO'];
            }
            $result->free();
        }
        return $MO;
    }

    function updateMO($idHandset = 0, $MO) {
        $this->_querySQL = "UPDATE dat_handset SET n_handset_MO = {$MO} WHERE n_handset_id = " . $idHandset . ";";
        $this->errorMessage = $this->_querySQL;
        return mysqli_query($this->conn, $this->_querySQL);
    }

    function __getQuerySQL() {
        return $this->_querySQL;
    }

}

?>