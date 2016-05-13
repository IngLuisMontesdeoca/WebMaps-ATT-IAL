<?php

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              *
 *   @version:       1.0                                     					*
 *   @created:       25/11/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Clase de acceso a la tabla dat_historicomensajes
 * ****************************************************************************** */

//---- REQUIRES ----//
//Clase Base
require_once "Base.class.php";

class Base_Dat_HistoricoMensajes extends Base {//<<--------------------------------------------------------- Class Base_Dat_HistoricoMensajes

    /*     * *
     *   @description:  Método constructor de la clase
     *   @param:        id .- (int) Id de la llave primaria
     *   @return:       void
     *   @updater:      LM
     *   @updated_date: 25/11/2014
     * * */
    function __construct($id = 0) {//<<------------------------------------------------------------ construct()
        parent::__construct('dat_historicomensajes', $id);
    }

    /*     * *
     *   @description:  Método que obtiene la tabla de activitylog
     *   @param:        void
     *   @return:       tableReporte .- (sting)
     *   @autor:        LM
     *   @craeted:      25/11/2014
     * * */
    public function getHistoricoMensajes($fIni = NULL, $fFin = NULL, 
                                            $sPTN, $sFecha, $sNumero, $sMensaje, $sEstatus,
                                            $sPag,$sLim,$sOrdC,$sOrdA) {
        
					$sOrder = '';
					$sCol = '';
					
					switch($sOrdC)
					{
						case '0':
							$sCol = 'b.c_handset_ptn';
						break;
						case '1':
							$sCol = 'a.d_historicomensajes_fecha';
						break;
						case '2':
							$sCol = 'a.c_historicomensajes_numero';
						break;
						case '3':
							$sCol = 'a.c_historicomensajes_mensaje';
						break;				
						case '4':
							$sCol = 'c.c_estatus_desc';
						break;
					}
					
					switch($sOrdA)
					{
						case '0':
							$sOrder = 'DESC';
						break;
						case '1':
							$sOrder = 'ASC';
						break;				
					}									
                                        
                    if($sCol == '')
                    {
                        $sCol = 'b.c_handset_ptn';
                    }
                    
                    if($sOrder == '')
                    {
                        $sOrder = '';
                    }     
                    
        $sCondition = " AND b.c_handset_ptn like '%".$sPTN."%'
                        AND a.d_historicomensajes_fecha like '%".$sFecha."%'
                        AND a.c_historicomensajes_numero like '%".$sNumero."%'
                        AND a.c_historicomensajes_mensaje like '%".$sMensaje."%'
                        AND c.c_estatus_desc like '%".$sEstatus."%'
                        ORDER BY ".$sCol." ".$sOrder."
                        LIMIT ".$sPag.",".$sLim;
                    
        $tableReport = '';
        $nRegistros = 0;
        $tBody = '';
        $arrResult = array();
        $this->_querySQL = "SELECT a.n_historicomensajes_id,"
                . "                a.d_historicomensajes_fecha,"
                . "                a.c_historicomensajes_numero,"
                . "                a.c_historicomensajes_mensaje,"
                . "                a.n_estatus_id,"
                . "                b.c_handset_ptn,"
                . "                c.c_estatus_desc as estatus"
                . " FROM   dat_historicomensajes a, dat_handset b, cat_estatus c"
                . " WHERE  a.n_handset_id = b.n_handset_id "
                . "        AND a.d_historicomensajes_fecha BETWEEN '{$fIni}' AND '{$fFin}'"
                . "        AND a.n_estatus_id = c.n_estatus_id"
                . $sCondition
;
                
        $this->_execute($this->_querySQL);
        $nRegistros = $this->numRows();
                
        if ($nRegistros > 0) {
            for ($i = 0; $i < $nRegistros; $i++) {
                 /*
                $estatus = 'No atendido';
                if($this->baseRs->fields['n_estatus_id'] == 10){
                    $estatus = 'Exitoso';
                }else if($this->baseRs->fields['n_estatus_id'] == 11){
                    $estatus = 'No exitoso';
                }
                  * */
                  
                $tBody .='<tr>
                             <td>' . $this->baseRs->fields['c_handset_ptn'] . '</td>
                             <td>' . $this->baseRs->fields['d_historicomensajes_fecha'] . '</td>
                             <td>' . $this->baseRs->fields['c_historicomensajes_numero'] . '</td>
                             <td>' . $this->baseRs->fields['c_historicomensajes_mensaje'] . '</td>
                             <td>' . $this->baseRs->fields['estatus'] . '</td>
                         </tr>';
                $arrResult[$i]['PTN'] =  $this->baseRs->fields['c_handset_ptn'];
                $arrResult[$i]['FECHA'] =  $this->baseRs->fields['d_historicomensajes_fecha'];
                $arrResult[$i]['NUMERO'] =  $this->baseRs->fields['c_historicomensajes_numero'];
                $arrResult[$i]['MENSAJE'] =  $this->baseRs->fields['c_historicomensajes_mensaje'];
                $arrResult[$i]['ESTATUS'] =  $this->baseRs->fields['estatus'];
                $this->next();
            }
            
            if(($sPTN == '')
                    && ($sFecha == '')
                    && ($sNumero == '')
                    && ($sMensaje == '')
                    && ($sEstatus== ''))
            {
                $sCondition = '';
            }
            
                $sql = "SELECT count(a.n_historicomensajes_id) as nRegistroTotal"
                        . " FROM   dat_historicomensajes a, dat_handset b, cat_estatus c"
                        . " WHERE  a.n_handset_id = b.n_handset_id "
                        . "        AND a.d_historicomensajes_fecha BETWEEN '{$fIni}' AND '{$fFin}'"
                        . "        AND a.n_estatus_id = c.n_estatus_id"
                        . $sCondition;
                //var_dump($sql);
		    $this->_execute($sql);
			
                    $nRegistros = $this->numRows();    

                    $nRegistroTotal = 0;
                    
                    if($nRegistros>0)
                        $nRegistroTotal = $this->baseRs->fields['nRegistroTotal'];

            return array($arrResult,$tBody,$nRegistroTotal);
        } else
            return array(NULL,'<div class="cssResulTxt">No Se Encontraron Resultados</div>',0);
    }

    public function getHistoricoMensajesExport($fIni, $fFin) {        
        $arrResult = array();
        $this->_querySQL = "SELECT a.n_historicomensajes_id,"
                . "                a.d_historicomensajes_fecha,"
                . "                a.c_historicomensajes_numero,"
                . "                a.c_historicomensajes_mensaje,"
                . "                a.n_estatus_id,"
                . "                b.c_handset_ptn,"
                . "                c.c_estatus_desc as estatus"
                . " FROM   dat_historicomensajes a, dat_handset b, cat_estatus c"
                . " WHERE  a.n_handset_id = b.n_handset_id "
                . "        AND a.d_historicomensajes_fecha BETWEEN '{$fIni}' AND '{$fFin}'"
                . "        AND a.n_estatus_id = c.n_estatus_id";
        $this->_execute($this->_querySQL);
        $nRegistros = $this->numRows();
        if ($nRegistros > 0) {
            for ($i = 0; $i < $nRegistros; $i++) {  
                $arrResult[$i]['PTN'] =  $this->baseRs->fields['c_handset_ptn'];
                $arrResult[$i]['FECHA'] =  $this->baseRs->fields['d_historicomensajes_fecha'];
                $arrResult[$i]['NUMERO'] =  $this->baseRs->fields['c_historicomensajes_numero'];
                $arrResult[$i]['MENSAJE'] =  $this->baseRs->fields['c_historicomensajes_mensaje'];
                $arrResult[$i]['ESTATUS'] =  $this->baseRs->fields['estatus'];
                $this->next();
            }
        }
        return $arrResult;
    }
    
//<<-------------------------------------------------------- End construct()  
}

//<<----------------------------------------------------- End Class Base_Dat_HistoricoMensajes