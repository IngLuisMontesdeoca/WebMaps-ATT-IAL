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

class Base_Dat_Handset extends Base {//<<--------------------------------------------------------- Class Base_Dat_Handset
    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 13/03/2014
     * * */

    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_handset', $id);
    }

//<<-------------------------------------------------------- End construct()

    /*     * *
     *   @description:  M�todo para obtener la informacion de un handset
     *   @param:        $idsHandsets .- (string) Ids de los handsets
     *   @return:       $arrHandsets .- (Array) Arreglo con la informacion de los handsets
     *   @updater:      LM
     *   @updated_date: 13/03/2014
     * * */

    public function getHandsetInfoById($idsHandsets = '') {//<<------------------------------------------------------------ getHandsetInfoById()
        $arrHandsets = array();
        $this->_querySQL = "SELECT n_handset_id,"
                . "c_handset_ptn,"
                . "c_handset_asunto,"
                . "c_handset_mensaje,"
                . "n_tipocontrato_id,"
                . "n_estatus_id,"
                . "n_usertype_id,"
                . "n_usernetwork_id,"
                . "day(d_handset_fecharegistro) as diaCorte,"
                . "n_handset_intervalo as intervalo "
                . "FROM {$this->_baseTbl} WHERE n_handset_id IN ({$idsHandsets})";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['handsetId'] = $this->baseRs->fields['n_handset_id'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['ptn'] = $this->baseRs->fields['c_handset_ptn'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['asunto'] = $this->baseRs->fields['c_handset_asunto'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['mensaje'] = $this->baseRs->fields['c_handset_mensaje'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['contrato'] = $this->baseRs->fields['n_tipocontrato_id'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['estatus'] = $this->baseRs->fields['n_estatus_id'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['tipoUsuario'] = $this->baseRs->fields['n_usertype_id'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['userNetwork'] = $this->baseRs->fields['n_usernetwork_id'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['diaCorte'] = $this->baseRs->fields['diaCorte'];
                $arrHandsets[$this->baseRs->fields['n_handset_id']]['intervalo'] = $this->baseRs->fields['intervalo'];
                $this->next();
            }
        }
        return $arrHandsets;
    }

//<<-------------------------------------------------------- End getHandsetInfoById()

    public function getInfoByHandsetId($idsHandset = '') {//<<------------------------------------------------------------ getInfoByHandsetId()
        $info = array();
        $this->_querySQL = "SELECT n_handset_id,"
                . "c_handset_ptn,"
                . "c_handset_asunto,"
                . "c_handset_mensaje,"
                . "n_tipocontrato_id,"
                . "n_estatus_id,"
                . "n_usertype_id,"
                . "n_usernetwork_id "
                . "FROM {$this->_baseTbl} WHERE n_handset_id = {$idsHandset}";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $info['handsetId'] = $this->baseRs->fields['n_handset_id'];
                $info['ptn'] = $this->baseRs->fields['c_handset_ptn'];
                $info['asunto'] = $this->baseRs->fields['c_handset_asunto'];
                $info['mensaje'] = $this->baseRs->fields['c_handset_mensaje'];
                $info['contrato'] = $this->baseRs->fields['n_tipocontrato_id'];
                $info['estatus'] = $this->baseRs->fields['n_estatus_id'];
                $info['tipoUsuario'] = $this->baseRs->fields['n_usertype_id'];
                $info['userNetwork'] = $this->baseRs->fields['n_usernetwork_id'];
                $this->next();
            }
        }
        return $info;
    }

//<<-------------------------------------------------------- End getInfoByHandsetId()

    /*     * *
     *   @description:  M�todo para obtener el estatus de un handset
     *   @param:        $idHandset .- (string) Ids de los handsets
     *   @return:       $arrHandsets .- (Array) Arreglo con la informacion de los handsets
     *   @updater:      LM
     *   @updated_date: 25/03/2014
     * * */

    public function getHandsetStatusById($idHandset = '') {//<<------------------------------------------------------------ getHandsetStatusById()
        $status = NULL;
        $this->_querySQL = "SELECT n_estatus_id FROM {$this->_baseTbl} WHERE n_handset_id = 0{$idHandset}";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $status = $this->baseRs->fields['n_estatus_id'];
                $this->next();
            }
        }
        return $status;
    }

//<<-------------------------------------------------------- End getHandsetStatusById()


    /*     * *
     *   @description:  Metodo para obtener el estatus de un handset
     *   @param:        $ptn .- (string) numero de handset
     *   @return:       .- (Array) Id y estatus del equipo en BD
     *   @updater:      LM
     *   @updated_date: 21/11/2014
     * * */

    public function getHandsetStatusByPTN($ptn = '') {//<<------------------------------------------------------------ getHandsetStatusByPTN()
        $idHandset = 0;
        $status = 0;
        $this->_querySQL = "SELECT n_handset_id,n_estatus_id FROM {$this->_baseTbl} WHERE c_handset_ptn = '{$ptn}'";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $idHandset = $this->baseRs->fields['n_handset_id'];
                $status = $this->baseRs->fields['n_estatus_id'];
                $this->next();
            }
        }
        return array($idHandset, $status);
    }

//<<-------------------------------------------------------- End getHandsetStatusByPTN()


    /*     * *
     *   @description:  Metodo para obtener el contador MT de un handset
     *   @param:        $handsetID .- (int) id del handset
     *   @return:       $MT.- (int) Contador MT
     *   @updater:      LM
     *   @updated_date: 27/11/2014
     * * */

    public function getHandsetMTByHandsetID($handsetID = '') {//<<------------------------------------------------------------ getHandsetMTByHandsetID()
        $MT = 0;
        $this->_querySQL = "SELECT n_handset_MT FROM {$this->_baseTbl} WHERE n_handset_id = {$handsetID}";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $MT = $this->baseRs->fields['n_handset_MT'];
                $this->next();
            }
        }
        return $MT;
    }

//<<-------------------------------------------------------- End getHandsetMTByHandsetID()
}

//<<----------------------------------------------------- End Class Base_Dat_Handset
?>