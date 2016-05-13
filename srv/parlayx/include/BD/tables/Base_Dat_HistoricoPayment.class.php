<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              		*
 *   @version:       1.0                                     					*
 *   @created:       19/11/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Metodos de acceso a la tabla dat_historicopayment             *
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_HistoricoPayment extends Base {//<<--------------------------------------------------------- Class Base_Dat_HistoricoPayment

    /*     * *
     *   @description:  M�todo constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 19/11/2014
     * * */

    public $_query = '';
    
    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_historicopayment', $id);
    }

//<<-------------------------------------------------------- End construct()

    public function getHistoricoCobro() {
        $nRegistros = 0;
        $ids = '';
        $arrResult = array();
        $_query = "SELECT a.n_historicopayment_id,"
                . "                a.d_historicopayment_fecha,"
                . "                date(a.d_historicopayment_fecha) as fdate,"
                . "                time(a.d_historicopayment_fecha) as ftime,"
                . "                a.n_handset_id,"
                . "                a.n_estatus_id,"
                . "                a.n_tipocobro_id,"
                . "                b.c_handset_ptn,"
                . "                c.c_tipocontrato_desc,"
                . "                c.d_tipocontrato_monto,"
                . "                a.c_historicopayment_transactionID"
                . " FROM   dat_historicopayment a, dat_handset b, cat_tipocontrato c"
                . " WHERE  a.n_handset_id = b.n_handset_id "
                . "        AND b.n_tipocontrato_id = c.n_tipocontrato_id"
                . "        AND a.c_historicopayment_processed IN ('0')";
        $this->_execute($_query);
        $nRegistros = $this->numRows();
        if ($nRegistros > 0) {
            for ($i = 0; $i < $nRegistros; $i++) {
                $tipocobro = ($this->baseRs->fields['n_tipocobro_id'] == 1 ) ? 'Normal' : 'Reintento de cobro';
                $estatus = ($this->baseRs->fields['n_estatus_id'] == 10 ) ? 'Exitoso' : 'Fallido';
                $arrResult[$i]['PTN'] = $this->baseRs->fields['c_handset_ptn'];
                $arrResult[$i]['FECHA'] = $this->baseRs->fields['d_historicopayment_fecha'];
                $arrResult[$i]['DATE'] = $this->baseRs->fields['fdate'];
                $arrResult[$i]['TIME'] = $this->baseRs->fields['ftime'];
                $arrResult[$i]['TIPO'] = $tipocobro;
                $arrResult[$i]['CONTRATO'] = $this->baseRs->fields['c_tipocontrato_desc'];
                $arrResult[$i]['MONTO'] = $this->baseRs->fields['d_tipocontrato_monto'];
                $arrResult[$i]['ESTATUS'] = $estatus;
                $arrResult[$i]['TRANSACTION'] = $this->baseRs->fields['c_historicopayment_transactionID'];
                $ids .= $this->baseRs->fields['n_historicopayment_id'] . ',';
                $this->next();
            }
            $ids = substr($ids, 0, strlen($ids) - 1);
        }
        return array($arrResult, $ids);
    }

    public function updateRegsProcessed($ids) {
        $_query = "UPDATE dat_historicopayment SET c_historicopayment_processed = '1' WHERE n_historicopayment_id IN ({$ids});";
        return $this->_execute($_query);
    }

    
    public function insertHistoricoPayment($handset,$estatus,$transactionID) {
        $this->_query = "INSERT INTO dat_historicopayment(d_historicopayment_fecha,n_handset_id,n_estatus_id,n_tipocobro_id,c_historicopayment_transactionID) "
                . " VALUES('".date('Y-m-d H:i:s')."',{$handset},{$estatus},1,'{$transactionID}');";
        return $this->_execute($this->_query);
    }
    
}

//<<----------------------------------------------------- End Class Base_Dat_HistoricoPayment
?>