<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              		*
 *   @version:       1.0                                     					*
 *   @created:       19/11/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Metodos de acceso a la tabla dat_historicomensajes             *
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_HistoricoMensajes extends Base {//<<--------------------------------------------------------- Class Base_Dat_HistoricoMensajes

    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 19/11/2014
     * * */

    public $_query = '';
    
    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_historicomensajes', $id);
    }

    public function insertHistoricoMensajes($handset,$estatus,$numero,$mensaje) {
        $this->_query = "INSERT INTO dat_historicomensajes(d_historicomensajes_fecha,c_historicomensajes_numero,c_historicomensajes_mensaje,n_handset_id,n_estatus_id) "
                . " VALUES('".date('Y-m-d H:i:s')."','{$numero}','{$mensaje}',{$handset},{$estatus});";
        return $this->_execute($this->_query);
    }
    
//<<-------------------------------------------------------- End construct()
    
}

//<<----------------------------------------------------- End Class Base_Dat_HistoricoMensajes
?>