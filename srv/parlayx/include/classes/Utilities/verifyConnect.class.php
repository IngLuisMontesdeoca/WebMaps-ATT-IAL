<?php

class verifyConnect{
    private $db_h = '';
    private $db_u = '';
    private $db_p = '';
    private $db_b = '';
    
    public function verifyConnect( $cfigPath ){
        $ini = parse_ini_file($cfigPath);
        $this->db_h = $ini['WebMaps.serv'];
        $this->db_u = $ini['WebMaps.user'];
        $this->db_p = $ini['WebMaps.pass'];
        $this->db_b = $ini['WebMaps.basd'];
    }
    
    public function _verifyConnectBDMysql() {
        try {
            $conexion = mysql_connect($this->db_h, $this->db_u, $this->db_p);
            if ($conexion != FALSE) {
                if (mysql_select_db($this->db_b, $conexion))
                    return true;
                else
                    return false;
            } else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }
}

