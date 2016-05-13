<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              *
 *   @version:       1.0                                     					*
 *   @created:       21/11/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Clase de acceso a la tabla dat_historicopayment
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_HistoricoPayment extends Base {//<<--------------------------------------------------------- Class Base_Dat_HistoricoPayment

    /*     * *
     *   @description:  Método constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 21/11/2014
     * * */

    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_historicopayment', $id);
    }

    /*     * *
     *   @description:  Método que obtiene la tabla de activitylog
     *   @param:        void
     *   @return:       tableReporte .- (sting)
     *   @autor:        LM
     *   @craeted:      21/11/2014
     * * */

    public function getHistoricoCobro($fIni = NULL, $fFin = NULL, $sPTN, $sFecha, $sTipoCobro, $sContrato, $sMonto, $sEstatus, $sPag, $sLim, $sOrdC, $sOrdA) {

        $sOrder = '';
        $sCol = '';

        switch ($sOrdC) {
            case '0':
                $sCol = 'b.c_handset_ptn';
                break;
            case '1':
                $sCol = 'a.d_historicopayment_fecha';
                break;
            case '2':
                $sCol = 'e.c_tipocobro_desc';
                break;
            case '3':
                $sCol = 'c.c_tipocontrato_desc';
                break;
            case '4':
                $sCol = 'c.d_tipocontrato_monto';
                break;
            case '5':
                $sCol = 'd.c_estatus_desc';
                break;
        }

        switch ($sOrdA) {
            case '0':
                $sOrder = 'DESC';
                break;
            case '1':
                $sOrder = 'ASC';
                break;
        }

        if ($sCol == '') {
            $sCol = 'b.c_handset_ptn';
        }

        if ($sOrder == '') {
            $sOrder = '';
        }
        
        $sCondition = " AND b.c_handset_ptn like '%" . $sPTN . "%'
                        AND a.d_historicopayment_fecha like '%" . $sFecha . "%'
                        AND e.c_tipocobro_desc like '%" . $sTipoCobro . "%'
                        AND c.c_tipocontrato_desc like '%" . $sContrato . "%'
                        AND c.d_tipocontrato_monto like '%" . $sMonto . "%'
                        AND d.c_estatus_desc like '%" . $sEstatus . "%'
                        ORDER BY " . $sCol . " " . $sOrder . "
                        LIMIT " . $sPag . "," . $sLim;

        $tableReport = '';
        $nRegistros = 0;
        $tBody = '';
        $arrResult = array();
        $this->_querySQL = "SELECT a.n_historicopayment_id,"
                . "                a.d_historicopayment_fecha,"
                . "                a.n_handset_id,"
                . "                a.c_historicopayment_transactionID,"
                . "                b.c_handset_ptn,"
                . "                c.c_tipocontrato_desc,"
                . "                c.d_tipocontrato_monto,"
                . "                d.c_estatus_desc as estatus,"
                . "                e.c_tipocobro_desc as tipoCobro"
                . " FROM   dat_historicopayment a, dat_handset b, cat_tipocontrato c, cat_estatus d, cat_tipocobro e"
                . " WHERE  a.n_handset_id = b.n_handset_id "
                . "        AND b.n_tipocontrato_id = c.n_tipocontrato_id"
                . "        AND a.d_historicopayment_fecha BETWEEN '{$fIni}' AND '{$fFin}'"
                . "        AND a.n_estatus_id = d.n_estatus_id"
                . "        AND a.n_tipocobro_id = e.n_tipocobro_id"
                . $sCondition;

            $this->_execute($sql);
        $nRegistros = $this->numRows();
        
        if ($nRegistros > 0) {
            for ($i = 0; $i < $nRegistros; $i++) {
                /*
                  $tipocobro = ($this->baseRs->fields['n_tipocobro_id'] == 1 ) ? 'Normal' : 'Reintento de cobro';
                  $estatus = ($this->baseRs->fields['n_estatus_id'] == 10 ) ? 'Exitoso' : 'Fallido';
                 * */
                $tBody .='<tr>
                             <td>' . $this->baseRs->fields['c_handset_ptn'] . '</td>
                             <td>' . $this->baseRs->fields['d_historicopayment_fecha'] . '</td>
                             <td>' . $this->baseRs->fields['tipoCobro'] . '</td>
                             <td>' . $this->baseRs->fields['c_tipocontrato_desc'] . '</td>
                             <td>' . $this->baseRs->fields['d_tipocontrato_monto'] . '</td>
                             <td>' . $this->baseRs->fields['estatus'] . '</td>
                             <td>' . $this->baseRs->fields['c_historicopayment_transactionID'] . '</td>
                         </tr>';
                $arrResult[$i]['PTN'] = $this->baseRs->fields['c_handset_ptn'];
                $arrResult[$i]['FECHA'] = $this->baseRs->fields['d_historicopayment_fecha'];
                $arrResult[$i]['TIPO'] = $this->baseRs->fields['tipoCobro'];
                $arrResult[$i]['CONTRATO'] = $this->baseRs->fields['c_tipocontrato_desc'];
                $arrResult[$i]['MONTO'] = $this->baseRs->fields['d_tipocontrato_monto'];
                $arrResult[$i]['ESTATUS'] = $this->baseRs->fields['estatus'];
                $arrResult[$i]['TRANSACTION'] = $this->baseRs->fields['c_historicopayment_transactionID'];
                $this->next();
            }
            
            if(($sPTN == '')
                    && ($sFecha == '')
                    && ($sTipoCobro == '')
                    && ($sContrato == '')
                    && ($sMonto == '')
                    && ($sEstatus == ''))
            {
                $sCondition = '';
            }

            $sql = "SELECT count(a.n_historicopayment_id) as nRegistroTotal"
                    . " FROM   dat_historicopayment a, dat_handset b, cat_tipocontrato c, cat_estatus d, cat_tipocobro e"
                    . " WHERE  a.n_handset_id = b.n_handset_id "
                    . "        AND b.n_tipocontrato_id = c.n_tipocontrato_id"
                    . "        AND a.d_historicopayment_fecha BETWEEN '{$fIni}' AND '{$fFin}'"
                    . "        AND a.n_estatus_id = d.n_estatus_id"
                    . "        AND a.n_tipocobro_id = e.n_tipocobro_id"
                    . $sCondition;

            $this->_execute($sql);

            $nRegistros = $this->numRows();

            $nRegistroTotal = 0;

            if ($nRegistros > 0)
                $nRegistroTotal = $this->baseRs->fields['nRegistroTotal'];

            return array($arrResult, $tBody, $nRegistroTotal);
        } else
            return array(NULL, '<div class="cssResulTxt">No Se Encontraron Resultados</div>', 0);
    }

    public function getHistoricoCobroExport($fIni, $fFin) {
        $arrResult = array();
        $this->_querySQL = "SELECT a.n_historicopayment_id,"
                . "                a.d_historicopayment_fecha,"
                . "                a.n_handset_id,"
                . "                a.c_historicopayment_transactionID,"
                . "                b.c_handset_ptn,"
                . "                c.c_tipocontrato_desc,"
                . "                c.d_tipocontrato_monto,"
                . "                d.c_estatus_desc as estatus,"
                . "                e.c_tipocobro_desc as tipoCobro"
                . " FROM   dat_historicopayment a, dat_handset b, cat_tipocontrato c, cat_estatus d, cat_tipocobro e"
                . " WHERE  a.n_handset_id = b.n_handset_id "
                . "        AND b.n_tipocontrato_id = c.n_tipocontrato_id"
                . "        AND a.d_historicopayment_fecha BETWEEN '{$fIni}' AND '{$fFin}'"
                . "        AND a.n_estatus_id = d.n_estatus_id"
                . "        AND a.n_tipocobro_id = e.n_tipocobro_id";
        $this->_execute($this->_querySQL);
        $nRegistros = $this->numRows();
        if ($nRegistros > 0) {
            for ($i = 0; $i < $nRegistros; $i++) {
                $arrResult[$i]['PTN'] = $this->baseRs->fields['c_handset_ptn'];
                $arrResult[$i]['FECHA'] = $this->baseRs->fields['d_historicopayment_fecha'];
                $arrResult[$i]['TIPO'] = $this->baseRs->fields['tipoCobro'];
                $arrResult[$i]['CONTRATO'] = $this->baseRs->fields['c_tipocontrato_desc'];
                $arrResult[$i]['MONTO'] = $this->baseRs->fields['d_tipocontrato_monto'];
                $arrResult[$i]['ESTATUS'] = $this->baseRs->fields['estatus'];
                $arrResult[$i]['TRANSACTIONID'] = $this->baseRs->fields['c_historicopayment_transactionID'];
                $this->next();
            }
        }
        return $arrResult;
    }

//<<-------------------------------------------------------- End construct()  
}

//<<----------------------------------------------------- End Class Base_Dat_HistoricoPayment