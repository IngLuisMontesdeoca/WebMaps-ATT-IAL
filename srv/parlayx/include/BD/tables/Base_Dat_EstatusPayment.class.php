<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              	*
 *   @version:       1.0                                     		*
 *   @created:       14/03/2014                              		*
 *   @copiright:     Copyright (c) 2014, WebMaps              		*
 *   @description    Metodos de acceso a la tabla dat_estatuspayment         *
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_EstatusPayment extends Base {//<<--------------------------------------------------------- Class Base_Dat_EstatusPayment

    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 14/03/2014
     * * */

    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_estatuspayment', $id);
    }

//<<-------------------------------------------------------- End construct()

    /*     * *
     *   @description:  M�todo para actualizar el estatus de payment de un equipo
     *   @param:        idHandset .- (int)   Id del equipo
     *                  idEstatus .- (int)   Id del estatus
     *   @return:                     (bool) Resultado de query
     *   @updater:      LM
     *   @updated_date: 20/03/2014
     * * */
    public function updateStatusPayment($idHandset, $idEstatus) {//<<------------------------------------------------------------ updateStatusPayment()
        $this->_querySQL = "UPDATE 	{$this->_baseTbl} "
                . "SET	 	n_estatus_id                    = {$idEstatus}"
                . " WHERE  	n_handset_id                    = {$idHandset}";
        if ($this->_execute($this->_querySQL))
            return true;
        else
            return false;
    }

//<<-------------------------------------------------------- End updateStatusPayment()

    /*     * *
     *   @description:  M�todo para actualizar el estatus de cobro para un equipo con contrato por evento
     *   @param:        idHandset .- (int)   Id del equipo
     *                  idEstatus .- (int)   Id del estatus
     *   @return:                     (bool) Resultado de query
     *   @updater:      LM
     *   @updated_date: 20/03/2014
     * * */
    public function updateStatusPaymentByHandset($idHandset, $idEstatus) {//<<------------------------------------------------------------ updateStatusPaymentByHandset()
        $fechaUltimo = ($idEstatus == 10) ? "d_estatuspayment_fechaultimo = NOW()," : "";
        $this->_querySQL = "UPDATE 	{$this->_baseTbl} "
                . "SET	 	{$fechaUltimo}n_estatuspayment_estatusultimo                    = {$idEstatus}"
                . " WHERE  	n_handset_id                    = {$idHandset}";
        if ($this->_execute($this->_querySQL))
            return true;
        else
            return false;
    }

//<<-------------------------------------------------------- End updateStatusPaymentByHandset()

    /*     * *
     *   @description:  M�todo para actualizar el estatus de cobro para un equipo con contrato mensual
     *   @param:        idHandset .- (int)   Id del equipo
     *                  idEstatus .- (int)   Id del estatus
     *                  fechaCorte .- (int)  Proxima fecha de corte
     *   @return:                     (bool) Resultado de query
     *   @updater:      LM
     *   @updated_date: 20/03/2014
     * * */
    public function updateStatusPaymentByHandsetMensual($idHandset, $idEstatus, $fechaCorte) {//<<------------------------------------------------------------ updateStatusPaymentByHandsetMensual()                
        $fechaUltimo = ($idEstatus == 10) ? "d_estatuspayment_fechaultimo = NOW()," : "";
        $this->_querySQL = "UPDATE 	{$this->_baseTbl} "
                . "SET	 	{$fechaUltimo}n_estatuspayment_estatusultimo      = {$idEstatus},"
                . "              d_estatuspayment_fechaproximo   = '{$fechaCorte}'"
                . " WHERE  	n_handset_id                    = {$idHandset}";
        if ($this->_execute($this->_querySQL))
            return true;
        else
            return false;
    }

//<<-------------------------------------------------------- End updateStatusPaymentByHandsetMensual()

    /*     * *
     *   @description:  M�todo para obtener los cobro del dia actual
     *   @param:        void
     *   @return:       array($strHandset,$cobros) $strHandset (String) Cadena con los ids de los equipos
     *                                                $cobros (Array) Arreglo con los cobros pendientes
     *   @updater:      LM
     *   @updated_date: 20/03/2014
     * * */

    public function getCobros() {//<<------------------------------------------------------------ getCobros()
        $cobros = array();

        $fechaActual = date("Y-m-d") . ' 23:59:59';
        $this->_querySQL = "SELECT "
                . "a.n_estatuspayment_id,"
                . "a.d_estatuspayment_fechaproximo,"
                . "a.n_handset_id "
                . "FROM {$this->_baseTbl} a, dat_handset b "
                . "WHERE a.d_estatuspayment_fechaproximo <= '{$fechaActual}' AND a.n_estatus_id = 3"
                . " AND a.n_handset_id = b.n_handset_id"
                . " AND b.n_tipocontrato_id = 1";
        $this->_execute($this->_querySQL);
        $strHandset = '';
        if ($this->numRows() > 0) {
            for ($i = 0; $i < $this->numRows(); $i++) {
                $strHandset .= $this->baseRs->fields['n_handset_id'] . ',';
                $cobros[$this->baseRs->fields['n_handset_id']]['fecha'] = $this->baseRs->fields['d_estatuspayment_fechaproximo'];
                $cobros[$this->baseRs->fields['n_handset_id']]['id'] = $this->baseRs->fields['n_estatuspayment_id'];
                $this->next();
            }
            $strHandset = substr($strHandset, 0, strlen($strHandset) - 1);
        }
        return array($strHandset, $cobros);
    }

//<<-------------------------------------------------------- End getCobros()
}

//<<----------------------------------------------------- End Class Base_Dat_EstatusPayment
?>