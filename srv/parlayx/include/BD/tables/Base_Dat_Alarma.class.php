<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              	*
 *   @version:       1.0                                     		*
 *   @created:       14/03/2014                              		*
 *   @copiright:     Copyright (c) 2014, WebMaps              		*
 *   @description    Metodos de acceso a la tabla dat_alarma                 *
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_Alarma extends Base {//<<--------------------------------------------------------- Class Base_Dat_Alarma

    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 13/03/2014
     * * */

    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_alarma', $id);
    }

//<<-------------------------------------------------------- End construct()

    /*     * *
     *   @description:  M�todo para obtener el id de la alarma activa de un equipo
     *   @param:        idHandset .- (string) Id del handset
     *   @return:       idAlarm .- (string) Id de la alarma
     *   @updater:      LM
     *   @updated_date: 21/03/2014
     * * */

    public function getAlarmByHandset($idHandset = '') {//<<------------------------------------------------------------ getAlarmByHandset()
        $idAlarm = '';
        $this->_querySQL = "SELECT n_alarma_id from {$this->_baseTbl} WHERE n_handset_id = {$idHandset} AND n_estatus_id NOT IN (2,4)";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $idAlarm = $this->baseRs->fields['n_alarma_id'];
                $this->next();
            }
        }
        return $idAlarm;
    }

    /*     * *
     *   @description:  M�todo para obtener el id de la alarma activa de un equipo
     *   @param:        idHandset .- (string) Id del handset
     *   @return:       idAlarm .- (string) Id de la alarma
     *   @updater:      LM
     *   @updated_date: 21/03/2014
     * * */

    public function getLastPosByHandset($idHandset = '') {//<<------------------------------------------------------------ getAlarmByHandset()
        $pos = '';
        $this->_querySQL = "SELECT d_alarma_latitude,d_alarma_longitude from {$this->_baseTbl} WHERE n_handset_id = ".$idHandset." AND n_estatus_id NOT IN (2,4)";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $pos = $this->baseRs->fields['d_alarma_latitude'].','.$this->baseRs->fields['d_alarma_longitude'];
                $this->next();
            }
        }
        return $pos;
    }
    
//<<-------------------------------------------------------- End getAlarmByHandset()

    public function getAlarmRecibidaByHandset($idHandset = '') {//<<------------------------------------------------------------ getAlarmRecibidaByHandset()
        $idAlarm = '';
        $this->_querySQL = "SELECT n_alarma_id from {$this->_baseTbl} WHERE n_handset_id = {$idHandset} AND n_estatus_id IN (9)";
        $this->_execute($this->_querySQL);
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $idAlarm = $this->baseRs->fields['n_alarma_id'];
                $this->next();
            }
        }
        return $idAlarm;
    }

//<<-------------------------------------------------------- End getAlarmRecibidaByHandset()

    public function updateStatusAlarm($idAlarm = '', $status = 0) {//<<------------------------------------------------------------ updateStatusAlarm()
        $this->_querySQL = "UPDATE  {$this->_baseTbl} SET n_estatus_id = {$status} WHERE n_alarma_id = {$idAlarm}";
        if ($this->_execute($this->_querySQL))
            return true;
        else
            return false;
    }

//<<-------------------------------------------------------- End updateStatusAlarm()

    /*     * *
     *   @description:  M�todo para actualizar el estatus de un handset
     *   @param:        $idHandset       .- (int)   id del handset
     *   @return:        .- (bool) Resultado de la actualizacion
     *   @updater:      LM
     *   @updated_date: 24/03/2014
     * * */

    public function dissableAlarm($idHandset) {//<<------------------------------------------------------------ updateStatusAlarma()
        $this->_querySQL = "UPDATE  {$this->_baseTbl} SET n_estatus_id = 4 WHERE n_estatus_id NOT IN (4) AND n_handset_id = {$idHandset}";
        if ($this->_execute($this->_querySQL))
            return true;
        else
            return false;
    }

//<<-------------------------------------------------------- End updateStatusAlarma()
}

//<<----------------------------------------------------- End Class Base_Dat_Alarma
?>