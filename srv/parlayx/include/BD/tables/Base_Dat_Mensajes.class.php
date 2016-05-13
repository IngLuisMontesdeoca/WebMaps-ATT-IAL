<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              	*
 *   @version:       1.0                                     		*
 *   @created:       27/03/2014                              		*
 *   @copiright:     Copyright (c) 2014, WebMaps              		*
 *   @description    Metodos de acceso a la tabla dat_mensaje                 *
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_Mensajes extends Base {//<<--------------------------------------------------------- Class Base_Dat_Mensajes

    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 27/03/2014
     * * */

    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_mensajes', $id);
    }

//<<-------------------------------------------------------- End construct()

    /*     * *
     *   @description:  M�todo para obtener lso contactos asociados a un handset
     *   @param:        idHandset .- (string) Id del handset
     *   @return:       arrContactos .- (Array) Arreglo con la informacion de los contactos
     *   @updater:      LM
     *   @updated_date: 27/03/2014
     * * */

    public function getMensajesToSend() {//<<--------------- getMensajesToSend()
        $arrMensajes = array();
        $ids = '';
        $this->_querySQL = "SELECT * FROM {$this->_baseTbl} WHERE c_mensaje_atendido IS NULL OR c_mensaje_atendido IN ('','0') ORDER by n_mensaje_id desc";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $arrMensajes[$i]['idMensaje'] = $this->baseRs->fields['n_mensaje_id'];
                $arrMensajes[$i]['idHandset'] = $this->baseRs->fields['n_handset_id'];
                $arrMensajes[$i]['mensaje']   = $this->baseRs->fields['c_mensaje_mensaje'];
                $arrMensajes[$i]['tipo']      = $this->baseRs->fields['c_mensaje_tipo'];
                $ids .= $this->baseRs->fields['n_mensaje_id'].',';
                $this->next();
            }
            $ids = substr($ids , 0 , strlen($ids ) - 1 );
        }
        return array($arrMensajes,$ids);
    }//<<-------------------------------------------------------- End getMensajesToSend()
    
    
    public function getMensajesToSendByIds($ids) {//<<--------------- getMensajesToSendByIds()
        $arrMensajes = array();
        $this->_querySQL = "SELECT * FROM {$this->_baseTbl} WHERE n_mensaje_id IN ({$ids})";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $arrMensajes[$i]['idMensaje'] = $this->baseRs->fields['n_mensaje_id'];
                $arrMensajes[$i]['idHandset'] = $this->baseRs->fields['n_handset_id'];
                $arrMensajes[$i]['mensaje']   = $this->baseRs->fields['c_mensaje_mensaje'];
                $arrMensajes[$i]['tipo']      = $this->baseRs->fields['c_mensaje_tipo'];
                $this->next();
            }
        }
        return $arrMensajes;
    }//<<-------------------------------------------------------- End getMensajesToSendByIds()

    public function updateStatusMensajes($ids) {//<<------------------------------------------------------------ updateStatusMensajes()
        $this->_querySQL = "UPDATE  {$this->_baseTbl} SET c_mensaje_atendido = '1' WHERE n_mensaje_id IN ({$ids})";
        if ($this->_execute($this->_querySQL))
            return true;
        else
            return false;
    }//<<-------------------------------------------------------- End updateStatusMensajes()
}

//<<----------------------------------------------------- End Class Base_Dat_Mensajes
?>