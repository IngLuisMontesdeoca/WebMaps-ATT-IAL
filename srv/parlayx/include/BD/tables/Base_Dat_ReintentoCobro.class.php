<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              		*
 *   @version:       1.0                                     					*
 *   @created:       13/03/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Metodos de acceso a la tabla dat_reintentocobro             *
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_ReintentoCobro extends Base {//<<--------------------------------------------------------- Class Base_Dat_ReintentoCobro

    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 13/03/2014
     * * */

    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_reintentocobro', $id);
    }

//<<-------------------------------------------------------- End construct()

    /*     * *
     *   @description:  Metodo para obtener las pagos pendientes por uso de servicio de un handset
     *   @param:        idHandset .- (int) Id del handset
     *   @return:       count .- (int) Numero de pagos pendientes
     *   @updater:      LM
     *   @updated_date: 14/03/2014
     * * */

    public function getPagosPendientes($idHandset) {//<<------------------------------------------------------------ getPagosPendientes()
        $count = 0;
        $this->_querySQL = "SELECT count(*) as count FROM {$this->_baseTbl} "
                . "WHERE n_handset_id = {$idHandset} AND n_tipocontrato_id = 2 "
                . "AND n_estatus_id = 3";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $count = $this->baseRs->fields['count'];
                $this->next();
            }
        }
        return $count;
    }

//<<-------------------------------------------------------- End getPagosPendientes()

    /*     * *
     *   @description:  M�todo para obtener los cobros pendientes
     *   @param:        void
     *   @return:       array($strHandset,$cobros) $strHandset (String) Cadena con los ids de los equipos
     *                                                $cobros (Array) Arreglo con los cobros pendientes
     *   @updater:      LM
     *   @updated_date: 21/03/2014
     * * */

    public function getCobrosPendientes() {//<<------------------------------------------------------------ getCobrosPendientes()
        $strHandset = '';
        $cobros = array();
        $numCobros = 0;
        $arrEquipos = array();
        $this->_querySQL = "SELECT n_reintentocobro_id,n_tipocontrato_id,n_handset_id,d_reintentocobro_fechalimite,d_reintentocobro_fechauso FROM {$this->_baseTbl} "
                . "WHERE n_estatus_id = 3";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $arrEquipos[$this->baseRs->fields['n_handset_id']] =  $this->baseRs->fields['n_handset_id'];
                $cobros[$this->baseRs->fields['n_handset_id']][$this->baseRs->fields['n_reintentocobro_id']]['id'] = $this->baseRs->fields['n_reintentocobro_id'];
                $cobros[$this->baseRs->fields['n_handset_id']][$this->baseRs->fields['n_reintentocobro_id']]['contrato'] = $this->baseRs->fields['n_tipocontrato_id'];
                $cobros[$this->baseRs->fields['n_handset_id']][$this->baseRs->fields['n_reintentocobro_id']]['fechalimite'] = $this->baseRs->fields['d_reintentocobro_fechalimite'];
                $cobros[$this->baseRs->fields['n_handset_id']][$this->baseRs->fields['n_reintentocobro_id']]['fechuso'] = $this->baseRs->fields['d_reintentocobro_fechauso'];
                $this->next();
                $numCobros++;
            }
            $strHandset = implode(',',$arrEquipos);
        }
        return array($strHandset, $cobros,$numCobros);
    }

//<<-------------------------------------------------------- End getCobrosPendientes()
}

//<<----------------------------------------------------- End Class Base_Dat_ReintentoCobro
?>