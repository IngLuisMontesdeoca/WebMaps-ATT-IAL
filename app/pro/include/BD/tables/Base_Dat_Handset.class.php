<?php

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase Handset			                                *
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_Handset extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_Handset

		/***
	    *   @description:  Método constructor de la clase
		*   @param:        id .- (int) Id de la llave primaria
		*   @return:       void
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
        function __construct( $id = 0 )
        {//<<------------------------------------------------------------ construct()
		    parent::__construct('dat_handset', $id);
	    }//<<-------------------------------------------------------- End construct()
		
            /***
	    *   @description:  Método que obtiene las handset
		*   @param:        void
		*   @return:       tableHandset .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getHandset($sPTN,$sCuenta,$sCliente,$sRed,
									$sPlan,$sServicio,$sFecha,$sEstatus,
									$sPag,$sLim,$sOrdC,$sOrdA){//<<------------------------------------------------------------ getHandset()
									
					$sOrder = '';
					$sCol = '';
					
					switch($sOrdC)
					{
						case '0':
							$sCol = 'handset.n_handset_id';
						break;
						case '2':
							$sCol = 'handset.c_handset_ptn';
						break;
						case '3':
							$sCol = 'cuenta.c_cuenta_cuenta';
						break;
						case '4':
							$sCol = 'cliente.c_cliente_nombre';
						break;				
						case '5':
							$sCol = 'net.c_usernetwork_desc';
						break;
						case '6':
							$sCol = 'usertype.c_usertype_desc';
						break;
						case '7':
							$sCol = 'tipocontrato.c_tipocontrato_desc';
						break;
						case '8':
							$sCol = 'handset.d_handset_fecharegistro';
						break;	
						case '9':
							$sCol = 'estatus.c_estatus_desc';
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
		    
                    $tBody = '';
                    $i = 0;
                    $nRegistros = 0;
                    
                    if($sCol == '')
                    {
                        $sCol = 'cliente.c_cliente_nombre,
                                 cuenta.c_cuenta_cuenta, 
                                 handset.c_handset_ptn';
                    }
                    
                    if($sOrder == '')
                    {
                        $sOrder = '';
                    }
                    
                    $sCondition = " AND handset.c_handset_ptn like '%".$sPTN."%'
                                    AND cuenta.c_cuenta_cuenta like '%".$sCuenta."%'
                                    AND cliente.c_cliente_nombre like '%".$sCliente."%'
                                    AND net.c_usernetwork_desc like '%".$sRed."%'
                                    AND usertype.c_usertype_desc like '%".$sPlan."%'
                                    AND tipocontrato.c_tipocontrato_desc like '%".$sServicio."%'
                                    AND handset.d_handset_fecharegistro like '%".$sFecha."%'
                                    AND estatus.c_estatus_desc like '%".$sEstatus."%'
                                    ORDER BY ".$sCol." ".$sOrder."
                                    LIMIT ".$sPag.",".$sLim;                    

                    
                    $this->_querySQL = "SELECT 
                                            handset.n_handset_id idPtn,
                                            handset.c_handset_ptn ptn,
                                            estatus.c_estatus_desc estatus,
                                            net.c_usernetwork_desc red,
                                            cuenta.c_cuenta_cuenta cuenta,
                                            cliente.c_cliente_nombre cliente,
                                            usertype.c_usertype_desc plan,
                                            tipocontrato.c_tipocontrato_desc servicio,
                                            date(handset.d_handset_fecharegistro) corte,
                                            handset.n_tipocontrato_id as tipoContrato
                                        FROM
                                            dat_handset handset,
                                            cat_estatus estatus,
                                            cat_usernetwork net,
                                            dat_cuenta cuenta,
                                            dat_cliente cliente,
                                            rel_cuentacliente relce,
                                            cat_usertype usertype,
                                            cat_tipocontrato tipocontrato
                                        WHERE
                                            handset.n_usernetwork_id = net.n_usernetwork_id
                                                AND handset.n_cuenta_id = cuenta.n_cuenta_id
                                                AND handset.n_estatus_id = estatus.n_estatus_id
                                                AND cuenta.n_cuenta_id = relce.n_cuenta_id
                                                AND cliente.n_cliente_id = relce.n_cliente_id
                                                AND handset.n_usertype_id = usertype.n_usertype_id
                                                AND handset.n_tipocontrato_id = tipocontrato.n_tipocontrato_id
                                                AND handset.n_estatus_id IN ('3','6')
                                                ".$sCondition;
//var_dump($this->_querySQL);die();
			//nombre, cuenta , ptn
		    $this->_execute($this->_querySQL);
			
                    $nRegistros = $this->numRows();
                    
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                                        $tBody.='<tr id="regPTN_'.$this->baseRs->fields['idPtn'].'" class="evtLineActiveEquipos">
                                                            <td>'.(((int)$sPag)+$i+1).'</td>
                                                            <td><input id = "'.$this->baseRs->fields['idPtn'].'" value="'.$this->baseRs->fields['ptn'].'" name="chkAdmEqPTN" type="checkbox" class="cssChckOne"></td>
                                                            <td>'.$this->baseRs->fields['ptn'].'</td>
                                                            <td>'.$this->baseRs->fields['cuenta'].'</td>
                                                            <td>'.$this->baseRs->fields['cliente'].'</td>
                                                            <td>'.$this->baseRs->fields['red'].'</td>';

                                                            switch($this->baseRs->fields['plan'])
                                                            {
                                                                case 'CONTROL':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp etvValidatePlan evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="3" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                            <option value="1">POSTPAID</option>
                                                                                            <option value="2">PREPAID</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;
                                                                /*
                                                                case 'MIXTO':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="3">LIBRE</option>
                                                                                            <option value="4" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                            <option value="1">POSTPAID</option>
                                                                                            <option value="2">PREPAID</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;
                                                                */
                                                                case 'POSTPAID':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp etvValidatePlan evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="3">CONTROL</option>
                                                                                            <option value="1" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                            <option value="2">PREPAID</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;

                                                                case 'PREPAID':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp etvValidatePlan evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="3">CONTROL</option>
                                                                                            <option value="1">POSTPAID</option>
                                                                                            <option value="2" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;

                                                                default:
                                                                break;
                                                            }                                        
                                        

                                                            switch($this->baseRs->fields['servicio'])
                                                            {
                                                                case 'Ilimitado':
                                                                    $tBody.='<td><div><select id="cboTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser evtLoadCalendar cssInptTransp evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1" selected>'.$this->baseRs->fields['servicio'].'</option>
                                                                                            <option value="2">Por evento</option>
                                                                                      </select><div id="dvTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['servicio'].'</div>
                                                                             </td>';                                                    
                                                                break;

                                                                case 'Por evento':
                                                                    $tBody.='<td><div><select id="cboTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser evtLoadCalendar cssInptTransp evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1">Ilimitado</option>
                                                                                            <option value="2" selected>'.$this->baseRs->fields['servicio'].'</option>
                                                                                      </select><div id="dvTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['servicio'].'</div>
                                                                             </td>';                                                    
                                                                break;
                                                                
                                                                default:
                                                                break;
                                                            }         
                                                            
                                                            if( $this->baseRs->fields['tipoContrato'] == '1' ){
                                                                $tBody.= "<td><input id='txtEquipoFechaCorte_".$this->baseRs->fields['idPtn']."' type='text' class='cssTxtAddUser' placeholder='Fecha de corte' value='".$this->baseRs->fields['corte']."' disabled='true'></td>";
                                                                //$tBody.= '<td>'.$this->baseRs->fields['corte'].'</td>';
                                                            }else{
                                                                $tBody.= "<td><input id='txtEquipoFechaCorte_".$this->baseRs->fields['idPtn']."' type='text' class='cssTxtAddUser' placeholder='Fecha de corte' value='N/A' disabled='true'></td>";
                                                                //$tBody.='<td>NA</td>';
                                                            }
                                                             
                                        $tBody.='<td>'.$this->baseRs->fields['estatus'].'</td>
                                                        <td><div title="Configurar" class="cssEBtnConfig evtOpenConfigHandset" id="dvEBtnConfig_'.$this->baseRs->fields['idPtn'].'"></div><div title="Agregar Contactos" class="cssEBtnContact evtOpenContactosH" id="dvEBtnContact_'.$this->baseRs->fields['idPtn'].'"></div><div title="Editar" class="cssEEditConfig evtEditHandset" id="dvEEditConfig_'.$this->baseRs->fields['idPtn'].'"></div></td>
                                                    </tr>';
					$this->next();
			   }
                           
                           if(($sPTN == '')
                                   && ($sCuenta == '')
                                   && ($sCliente == '')
                                   && ($sRed == '')
                                   && ($sPlan == '')
                                   && ($sServicio == '')
                                   && ($sFecha == '')
                                   && ($sEstatus == ''))
                           {
                               $sCondition = '';
                           }
                           
                                               
                    $sql = "SELECT 
                                            count(handset.n_handset_id) nRegistroTotal
                                        FROM
                                            dat_handset handset,
                                            cat_estatus estatus,
                                            cat_usernetwork net,
                                            dat_cuenta cuenta,
                                            dat_cliente cliente,
                                            rel_cuentacliente relce,
                                            cat_usertype usertype,
                                            cat_tipocontrato tipocontrato
                                        WHERE
                                            handset.n_usernetwork_id = net.n_usernetwork_id
                                                AND handset.n_cuenta_id = cuenta.n_cuenta_id
                                                AND handset.n_estatus_id = estatus.n_estatus_id
                                                AND cuenta.n_cuenta_id = relce.n_cuenta_id
                                                AND cliente.n_cliente_id = relce.n_cliente_id
                                                AND handset.n_usertype_id = usertype.n_usertype_id
                                                AND handset.n_tipocontrato_id = tipocontrato.n_tipocontrato_id
                                                AND handset.n_estatus_id IN ('3','6')"
                                                .$sCondition

                    ;

		    $this->_execute($sql);
			
                    $nRegistros = $this->numRows();    

                    $nRegistroTotal = 0;
                    
                    if($nRegistros>0)
                        $nRegistroTotal = $this->baseRs->fields['nRegistroTotal'];
                        
                    
                            return array($tBody,$nRegistroTotal);
			}
                        else
                            return array('<div class="cssResulTxt">No Se Encontraron Resultados</div>',0);
		}//<<------------------------------------------------------------ End getHandset()
                
            /***
	    *   @description:  Método que obtiene las handset por parametros
		*   @param:        keyPatron .- (string)
                *   @param:         criterio .- (string)
		*   @return:       tableHandset .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getHandsetConsulta($keyPatron = NULL, $criterio = NULL,
												$sPTN,$sCuenta,$sCliente,$sRed,
												$sPlan,$sServicio,$sFecha,$sEstatus,
												$sPag,$sLim,$sOrdC,$sOrdA){//<<------------------------------------------------------------ getHandsetConsulta()
												
					$sOrder = '';
					$sCol = '';
					
					switch($sOrdC)
					{
						case '0':
							$sCol = 'handset.n_handset_id';
						break;
						case '2':
							$sCol = 'handset.c_handset_ptn';
						break;
						case '3':
							$sCol = 'cuenta.c_cuenta_cuenta';
						break;
						case '4':
							$sCol = 'cliente.c_cliente_nombre';
						break;				
						case '5':
							$sCol = 'net.c_usernetwork_desc';
						break;
						case '6':
							$sCol = 'usertype.c_usertype_desc';
						break;
						case '7':
							$sCol = 'tipocontrato.c_tipocontrato_desc';
						break;
						case '8':
							$sCol = 'handset.d_handset_fecharegistro';
						break;	
						case '9':
							$sCol = 'estatus.c_estatus_desc';
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
                        $sCol = 'cliente.c_cliente_nombre,
                                 cuenta.c_cuenta_cuenta, 
                                 handset.c_handset_ptn';
                    }
                    
                    if($sOrder == '')
                    {
                        $sOrder = '';
                    }            
                    
                    $sCondition = " AND handset.c_handset_ptn like '%".$sPTN."%'
                                    AND cuenta.c_cuenta_cuenta like '%".$sCuenta."%'
                                    AND cliente.c_cliente_nombre like '%".$sCliente."%'
                                    AND net.c_usernetwork_desc like '%".$sRed."%'
                                    AND usertype.c_usertype_desc like '%".$sPlan."%'
                                    AND tipocontrato.c_tipocontrato_desc like '%".$sServicio."%'
                                    AND handset.d_handset_fecharegistro like '%".$sFecha."%'
                                    AND estatus.c_estatus_desc like '%".$sEstatus."%'
                                    ORDER BY ".$sCol." ".$sOrder."
                                    LIMIT ".$sPag.",".$sLim;
					
		   $condition = '';
                    
                    if(is_null($keyPatron))
                        $keyPatron = '';

                    if(!(is_null($criterio)))
                    {
                        switch($criterio)
                        {
                            case 'PTN':
                                $condition = "AND handset.c_handset_ptn LIKE '%{$keyPatron}%'";
                            break;
                        
                            case 'cuenta':
                                $condition = "AND cuenta.c_cuenta_cuenta LIKE '%{$keyPatron}%'";
                            break;
                        
                            case 'cliente':
                                $condition = "AND c_cliente_nombre LIKE '%{$keyPatron}%'";
                            break;
                        
                            case 'red':
                                if($keyPatron != 'Todos')
                                    $condition = "AND net.n_usernetwork_id LIKE '%{$keyPatron}%'";
                            break;
                            
                            case 'plan':
                                if($keyPatron != 'Todos')
                                    $condition = "AND usertype.n_usertype_id LIKE '%{$keyPatron}%'";
                            break;
                            
                            case 'servicio':
                                if($keyPatron != 'Todos')
                                    $condition = "AND tipocontrato.n_tipocontrato_id LIKE '%{$keyPatron}%'";
                            break;                            
                        
                            case 'estatus':
                                if($keyPatron != 'Todos')
                                    $condition = "AND estatus.n_estatus_id LIKE '%{$keyPatron}%'";
                            break;
                        }
                    }
                    else
                        $criterio = '';
                    
                    $tBody = '';
                    $i = 0;
                    $nRegistros =0;
                    
                    $this->_querySQL = "SELECT 
                                            handset.n_handset_id idPtn,
                                            handset.c_handset_ptn ptn,
                                            estatus.c_estatus_desc estatus,
                                            estatus.n_estatus_id idEstatus,
                                            net.c_usernetwork_desc red,
                                            net.n_usernetwork_id idRed,
                                            cuenta.c_cuenta_cuenta cuenta,
                                            cliente.c_cliente_nombre cliente,
                                            usertype.c_usertype_desc plan,
                                            usertype.n_usertype_id idPlan,
                                            tipocontrato.c_tipocontrato_desc servicio,
                                            date(handset.d_handset_fecharegistro) corte,
                                            handset.n_tipocontrato_id as tipoContrato,
                                            tipocontrato.n_tipocontrato_id idServicio
                                        FROM
                                            dat_handset handset,
                                            cat_estatus estatus,
                                            cat_usernetwork net,
                                            dat_cuenta cuenta,
                                            dat_cliente cliente,
                                            rel_cuentacliente relce,
                                            cat_usertype usertype,
                                            cat_tipocontrato tipocontrato
                                        WHERE
                                            handset.n_usernetwork_id = net.n_usernetwork_id
                                                AND handset.n_cuenta_id = cuenta.n_cuenta_id
                                                AND handset.n_estatus_id = estatus.n_estatus_id
                                                AND handset.n_usertype_id = usertype.n_usertype_id
                                                AND handset.n_tipocontrato_id = tipocontrato.n_tipocontrato_id
                                                AND cuenta.n_cuenta_id = relce.n_cuenta_id
                                                AND cliente.n_cliente_id = relce.n_cliente_id
                                                AND handset.n_estatus_id IN ('3','6')
                                                {$condition}
                                                ".$sCondition
			;
		    $this->_execute($this->_querySQL);
                    
                    $nRegistros = $this->numRows();
			
                    $nRegistros = $this->numRows();
                    
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                                        $tBody.='<tr id="regPTN_'.$this->baseRs->fields['idPtn'].'" class="evtLineActiveEquipos">
                                                            <td>'.($i+1).'</td>
                                                            <td><input id = "'.$this->baseRs->fields['idPtn'].'" value="'.$this->baseRs->fields['ptn'].'" name="chkAdmEqPTN" type="checkbox" class="cssChckOne"></td>
                                                            <td>'.$this->baseRs->fields['ptn'].'</td>
                                                            <td>'.$this->baseRs->fields['cuenta'].'</td>
                                                            <td>'.$this->baseRs->fields['cliente'].'</td>
                                                            <td>'.$this->baseRs->fields['red'].'</td>';

                                                            switch($this->baseRs->fields['plan'])
                                                            {
                                                                case 'CONTROL':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp etvValidatePlan evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1">POSTPAID</option>
                                                                                            <option value="2">PREPAID</option>
                                                                                            <option value="3" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;
                                                                /*
                                                                case 'MIXTO':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="3">LIBRE</option>
                                                                                            <option value="4" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                            <option value="1">POSTPAID</option>
                                                                                            <option value="2">PREPAID</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;
                                                                */
                                                                case 'POSTPAID':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp etvValidatePlan evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                            <option value="2">PREPAID</option>
                                                                                            <option value="3">CONTROL</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;

                                                                case 'PREPAID':
                                                                    $tBody.='<td><div><select id="cboTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp etvValidatePlan evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1">POSTPAID</option>
                                                                                            <option value="2" selected>'.$this->baseRs->fields['plan'].'</option>
                                                                                            <option value="3" selected>CONTROL</option>
                                                                                      </select><div id="dvTipoPlanEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['plan'].'</div>
                                                                             </td>';                                                    
                                                                break;

                                                                default:
                                                                break;
                                                            }                                        
                                        

                                                            switch($this->baseRs->fields['servicio'])
                                                            {
                                                                case 'Ilimitado':
                                                                    $tBody.='<td><div><select id="cboTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1" selected>'.$this->baseRs->fields['servicio'].'</option>
                                                                                            <option value="2">Por evento</option>
                                                                                      </select><div id="dvTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['servicio'].'</div>
                                                                             </td>';                                                    
                                                                break;

                                                                case 'Por evento':
                                                                    $tBody.='<td><div><select id="cboTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEditHandset_'.$this->baseRs->fields['idPtn'].'" disabled>
                                                                                            <option value="1">Ilimitado</option>
                                                                                            <option value="2" selected>'.$this->baseRs->fields['servicio'].'</option>
                                                                                      </select><div id="dvTipoServicioEdit_'.$this->baseRs->fields['idPtn'].'" class="cssOculta">'.$this->baseRs->fields['servicio'].'</div>
                                                                             </td>';                                                    
                                                                break;
                                                                
                                                                default:
                                                                break;
                                                            }                                           
                                                               
                                                            if( $this->baseRs->fields['tipoContrato'] == '1' ){
                                                                $tBody.= "<td><input id='txtEquipoFechaCorte_".$this->baseRs->fields['idPtn']."' type='text' class='cssTxtAddUser' placeholder='Fecha de corte' value='".$this->baseRs->fields['corte']."' disabled='true'></td>";
                                                                //$tBody.= '<td>'.$this->baseRs->fields['corte'].'</td>';
                                                            }else{
                                                                $tBody.= "<td><input id='txtEquipoFechaCorte_".$this->baseRs->fields['idPtn']."' type='text' class='cssTxtAddUser' placeholder='Fecha de corte' value='N/A' disabled='true'></td>";
                                                                //$tBody.='<td>NA</td>';
                                                            }
                                                            
                                        $tBody.='<td>'.$this->baseRs->fields['estatus'].'</td>
                                                        <td><div title="Configurar" class="cssEBtnConfig evtOpenConfigHandset" id="dvEBtnConfig_'.$this->baseRs->fields['idPtn'].'"></div><div title="Agregar Contactos" class="cssEBtnContact evtOpenContactosH" id="dvEBtnContact_'.$this->baseRs->fields['idPtn'].'"></div><div title="Editar" class="cssEEditConfig evtEditHandset" id="dvEEditConfig_'.$this->baseRs->fields['idPtn'].'"></div></td>
                                                    </tr>';
					$this->next();
			   }
                           
                           if(($sPTN == '')
                                   && ($sCuenta == '')
                                   && ($sCliente == '')
                                   && ($sRed == '')
                                   && ($sPlan == '')
                                   && ($sServicio == '')
                                   && ($sFecha == '')
                                   && ($sEstatus == ''))
                           {
                               $sCondition = '';
                           }                           
			   
                    $sql = "SELECT 
                                            count(handset.n_handset_id) as nRegistroTotal
                                        FROM
                                            dat_handset handset,
                                            cat_estatus estatus,
                                            cat_usernetwork net,
                                            dat_cuenta cuenta,
                                            dat_cliente cliente,
                                            rel_cuentacliente relce,
                                            cat_usertype usertype,
                                            cat_tipocontrato tipocontrato
                                        WHERE
                                            handset.n_usernetwork_id = net.n_usernetwork_id
                                                AND handset.n_cuenta_id = cuenta.n_cuenta_id
                                                AND handset.n_estatus_id = estatus.n_estatus_id
                                                AND handset.n_usertype_id = usertype.n_usertype_id
                                                AND handset.n_tipocontrato_id = tipocontrato.n_tipocontrato_id
                                                AND cuenta.n_cuenta_id = relce.n_cuenta_id
                                                AND cliente.n_cliente_id = relce.n_cliente_id
                                                AND handset.n_estatus_id IN ('3','6')
                                                {$condition}"
                                                .$sCondition
                    ;			   
                           		   
				$this->_execute($sql);
			
                $nRegistros = $this->numRows();    

                $nRegistroTotal = 0;
                    
                if($nRegistros>0)
                    $nRegistroTotal = $this->baseRs->fields['nRegistroTotal'];
                        
                    
				return array($tBody,$nRegistroTotal);
							
			}
			else
				return array('<div class="cssResulTxt">No Se Encontraron Resultados</div>',0);
		}//<<------------------------------------------------------------ End getHandsetConsulta()

            /***
	    *   @description:  Método valida si existe handset
		*   @param:        nombrePtn .- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function isHandset($idCliente='', $idCuenta='', $nombrePtn='')//<<------------------------------------------------------------ isHandset()
                {
                    
                    
                        $this->_querySQL = "SELECT 
                                            n_handset_id
                                        FROM
                                            dat_handset handset
                                        WHERE
                                                handset.c_handset_ptn = '{$nombrePtn}'
                                                /*AND handset.n_estatus_id = 3*/
                                              ";

                    $this->_execute($this->_querySQL);
                    
                    if($this->numRows()>0)
                        return '1';
                    else
                        return '0';
                }//<<------------------------------------------------------------ End isHandset()
                
                
            /***
	    *   @description:  Método que obtiene las configuraciones de envio
		*   @param:        void
		*   @return:       configuracionEnvio .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getConfiguracionEnvio($idPtn = NULL){//<<------------------------------------------------------------ getConfiguracionEnvio()
		    
                    $layerConfiguracion = '';
                    $optionFrecuencia = '';
                    $optionTiempo = '';
                    $i = 0;
                    $nRegistros = 0;
                    
                    if(is_null($idPtn))
                        return '0';
                    
                    $this->_querySQL = "SELECT 
                                            handset.n_handset_id idPtn,
                                            handset.c_handset_ptn ptn,
                                            handset.n_handset_intervalo idIntervalo,
                                            handset.n_handset_duracion idDuracion
                                            
                                        FROM
                                            dat_handset handset
                                        WHERE
                                                handset.n_handset_id = {$idPtn}
                                                AND handset.n_estatus_id IN ('3','6')
                    ";
								
		    $this->_execute($this->_querySQL);
			
                    $nRegistros = $this->numRows();
                    
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                               /*var_dump($idPtn);
                               var_dump($this->baseRs->fields['idFrecuencia']);die();*/
                               switch($this->baseRs->fields['idIntervalo'])
                               {
                                   case '1':
                                            $optionFrecuencia = '<option value ="0" disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1" selected>.:: 1 ::.</option>
                                                                        <option value="3">.:: 3 ::.</option>
                                                                        <option value="5">.:: 5 ::.</option>
                                                                        <option value="10">.:: 10 ::.</option>
                                                                        <option value="30">.:: 30 ::.</option>
                                                                        <option value="60">.:: 60 ::.</option>';
                                   break;
                               
                                   case '3':
                                            $optionFrecuencia = '<option value ="0" disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1">.:: 1 ::.</option>
                                                                        <option value="3" selected>.:: 3 ::.</option>
                                                                        <option value="5">.:: 5 ::.</option>
                                                                        <option value="10">.:: 10 ::.</option>
                                                                        <option value="30">.:: 30 ::.</option>
                                                                        <option value="60">.:: 60 ::.</option>';
                                   break;
                               
                                   case '5':
                                            $optionFrecuencia = '<option value ="0" disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1">.:: 1 ::.</option>
                                                                        <option value="3">.:: 3 ::.</option>
                                                                        <option value="5" selected>.:: 5 ::.</option>
                                                                        <option value="10">.:: 10 ::.</option>
                                                                        <option value="30">.:: 30 ::.</option>
                                                                        <option value="60">.:: 60 ::.</option>';
                                   break;
                               
                                   case '10':
                                            $optionFrecuencia = '<option value ="0" disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1">.:: 1 ::.</option>
                                                                        <option value="3">.:: 3 ::.</option>
                                                                        <option value="5">.:: 5 ::.</option>
                                                                        <option value="10" selected>.:: 10 ::.</option>
                                                                        <option value="30">.:: 30 ::.</option>
                                                                        <option value="60">.:: 60 ::.</option>';
                                   break;
                               
                                   case '30':
                                            $optionFrecuencia = '<option value ="0" disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1">.:: 1 ::.</option>
                                                                        <option value="3">.:: 3 ::.</option>
                                                                        <option value="5">.:: 5 ::.</option>
                                                                        <option value="10">.:: 10 ::.</option>
                                                                        <option value="30" selected>.:: 30 ::.</option>
                                                                        <option value="60">.:: 60 ::.</option>';
                                   break;
                               
                                   case '60':
                                            $optionFrecuencia = '<option value ="0" disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1">.:: 1 ::.</option>
                                                                        <option value="3">.:: 3 ::.</option>
                                                                        <option value="5">.:: 5 ::.</option>
                                                                        <option value="10">.:: 10 ::.</option>
                                                                        <option value="30">.:: 30 ::.</option>
                                                                        <option value="60" selected>.:: 60 ::.</option>';
                                   break;          
                               
                                   default:
                                            $optionFrecuencia = '<option value ="0" selected disabled>.:: Seleccionar opcion ::.</option>
                                                                        <option value ="1">.:: 1 ::.</option>
                                                                        <option value="3">.:: 3 ::.</option>
                                                                        <option value="5">.:: 5 ::.</option>
                                                                        <option value="10">.:: 10 ::.</option>
                                                                        <option value="30">.:: 30 ::.</option>
                                                                        <option value="60">.:: 60 ::.</option>';                                       
                                   break;
                                   
                               }
                               
                               switch($this->baseRs->fields['idDuracion'])
                               {

                                   case '3':
                                       $optionTiempo='<option value="0" disabled>.:: Seleccionar opcion ::.</option>
                                                        <option value="3" selected>.:: 3 ::.</option>
                                                        <option value="6">.:: 6 ::.</option>
                                                        <option value="12">.:: 12 ::.</option>
                                                        <option value="24">.:: 24 ::.</option>';
                                   break;
                               
                                   case '6':
                                       $optionTiempo='<option value="0" disabled>.:: Seleccionar opcion ::.</option>
                                                        <option value="3">.:: 3 ::.</option>
                                                        <option value="6" selected>.:: 6 ::.</option>
                                                        <option value="12">.:: 12 ::.</option>
                                                        <option value="24">.:: 24 ::.</option>';
                                   break;
                               
                                   case '12':
                                       $optionTiempo='<option value="0" disabled>.:: Seleccionar opcion ::.</option>
                                                        <option value="3">.:: 3 ::.</option>
                                                        <option value="6">.:: 6 ::.</option>
                                                        <option value="12" selected>.:: 12 ::.</option>
                                                        <option value="24">.:: 24 ::.</option>';
                                   break;
                               
                                   case '24':
                                       $optionTiempo='<option value="0" disabled>.:: Seleccionar opcion ::.</option>
                                                        <option value="3">.:: 3 ::.</option>
                                                        <option value="6">.:: 4 ::.</option>
                                                        <option value="12">.:: 12 ::.</option>
                                                        <option value="24" selected>.:: 24 ::.</option>';
                                   break;
                               
                                   default:
                                       $optionTiempo='<option value="0" disabled selected>.:: Seleccionar opcion ::.</option>
                                                        <option value="3">.:: 3 ::.</option>
                                                        <option value="6">.:: 6 ::.</option>
                                                        <option value="12">.:: 12 ::.</option>
                                                        <option value="24">.:: 24 ::.</option>';
                                   break;
                                   
                               }
                                
                               return $layerConfiguracion = '<div>
                                                                 Duracion (hrs):
                                                                 <select id="selMEnviados" class="cssSelBC01">
                                                                     '.$optionTiempo.'
                                                                 </select>
                                                             </div>
                                                             <div>
                                                                 Intervalo (min):
                                                                 <select id="selPEnvio" class="cssSelBC01">
                                                                     '.$optionFrecuencia.'
                                                                 </select>
                                                             </div>';
			   }                           
			}
                        else
                            return utf8_encode ('0');
		}//<<------------------------------------------------------------ End getConfiguracionEnvio()
  
            /***
	    *   @description:  Método que inserta un equipo
		*   @param:        void
		*   @return:       respuesta .- (boolean)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                public function insertHandset($data)
                {		    
                    $this->_querySQL = "INSERT INTO dat_handset(`c_handset_ptn`
                                                                , `d_handset_fecharegistro`
                                                                , `n_usernetwork_id`
                                                                , `n_cuenta_id`
                                                                , `n_estatus_id`                                                                
                                                                , `n_usertype_id`
                                                                , `n_tipocontrato_id`
                                                                , `n_tiposervicio_id`) 
                                                VALUES ('{$data['c_handset_ptn']}'
                                                        ,'{$data['d_handset_fecharegistro']}'
                                                        ,'{$data['n_usernetwork_id']}'
                                                        ,'{$data['n_cuenta_id']}'
                                                        ,'{$data['n_estatus_id']}'                                                            
                                                        ,'{$data['n_usertype_id']}'
                                                        ,'{$data['n_tipocontrato_id']}'
                                                        ,{$data['n_tiposervicio_id']})";    
                                                        //var_dump($this->_querySQL);die();
                                                        
                    if ($this->_execute($this->_querySQL)!= 1)
                        return false;
                    
                    $idHandSet = $this->getLastId();
                    
                    if($idHandSet == 0)
                        return false;
                    
                    switch($data['n_tipocontrato_id'])
                    {
                                    //mensual
                                   case "1":
                                       
                                        $fecha1 = explode("-",(date("Y-m-d H:i:s")));
                                        $hora1 = explode(" ",$fecha1[2]);

                                        $fecha2 = explode("-",(date("Y-m-d")));

                                        /*$fecha2[1] = "1";
                                        $fecha2[2]="30";
                                        $fecha2[0]="2014";*/

                                        $mes=(int)$fecha2[1]+1;

                                        if($mes>=13)
                                                $mes = "1";
                                        else
                                                $mes = (string)$mes;

                                        if(checkdate($mes,$fecha2[2],$fecha2[0]))
                                        {
                                                //existe fecha
                                                $fechaAlta= $fecha2[0]."-".$fecha2[1]."-".$fecha2[2]." ".$hora1[1];
                                                $fechaCorte= $fecha2[0]."-".$mes."-".$fecha2[2]." ".$hora1[1];
                                        }
                                        else
                                        {
                                                //no existe fecha
                                                //calcula el ultimo dia del siguiente mes
                                                $dia = date("d",(mktime(0,0,0,$mes+1,1,$fecha2[0])-1));
                                                $fechaAlta= $fecha2[0]."-".$fecha2[1]."-".$fecha2[2]." ".$hora1[1];
                                                $fechaCorte= $fecha2[0]."-".$mes."-".$dia." ".$hora1[1];
                                        }
                                        
                                       $this->_querySQL = "INSERT INTO dat_estatuspayment(`d_estatuspayment_fechaproximo`
                                                                                   , `n_handset_id`
                                                                                   , `n_estatus_id`) 
                                                                   VALUES ('{$fechaCorte}'
                                                                           ,'{$idHandSet}'
                                                                           ,'3')";
                                                                           //var_dump($this->_querySQL);die();
                                       if ($this->_execute($this->_querySQL)!= 1)
                                           return false;
                                       else
                                           return $idHandSet;
                                       break;
                                       
                                   //evento
                                   case "2":
                                       $this->_querySQL = "INSERT INTO dat_estatuspayment(`d_estatuspayment_fechaproximo`
                                                                                   , `n_handset_id`
                                                                                   , `n_estatus_id`) 
                                                                   VALUES (NULL
                                                                           ,'{$idHandSet}'
                                                                           ,'3')";
                                                                           //var_dump($this->_querySQL);die();
                                       if ($this->_execute($this->_querySQL)!= 1)
                                           return false;                                                                              
                                       else
                                           return $idHandSet;
                                       break;
                                       
                                   default:
                                       return false;
                                       break;
                    }
                    
                }
                
            /***
	    *   @description:  Método que suspende equipo
		*   @param:        void
		*   @return:       respuesta .- (boolean)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                public function suspendeHandset($data)
                {		    
                    $data = str_replace("|", ",", $data);
                    //var_dump("('".$data."')");
                                                        
                    $this->_querySQL = "UPDATE dat_handset SET
                                                n_estatus_id=6,
                                                n_canalcancelacion_id=1
                                                WHERE n_handset_id IN ({$data})";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;
                    
                    $this->_querySQL = "UPDATE dat_estatuspayment SET
                                                n_estatus_id=4 
                                                WHERE n_handset_id IN ({$data})";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;
                    
                    $this->_querySQL = "UPDATE dat_envioalarma SET
                                                n_envioalarma_mensajesenviados = 0,n_estatus_id = 4
                                                WHERE n_handset_id IN ({$data})";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;
                    
                    $this->_querySQL = "UPDATE dat_alarma SET
                                                n_estatus_id=2 
                                                WHERE n_handset_id IN ({$data}) 
                                                        AND  n_estatus_id NOT IN (2)";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;                    
                    
                    return true;
                    

                }                
                
            /***
	    *   @description:  Método que elimina equipo
		*   @param:        void
		*   @return:       respuesta .- (boolean)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                public function eliminarHandset($data)
                {		    
                    $data = str_replace("|", ",", $data);
                    //var_dump("('".$data."')");
                                                        
                    $this->_querySQL = "UPDATE dat_handset SET
                                                n_estatus_id=5, 
                                                n_canalcancelacion_id = 1
                                                WHERE n_handset_id IN ({$data})";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;
                    
                    $this->_querySQL = "UPDATE dat_estatuspayment SET
                                                n_estatus_id=4 
                                                WHERE n_handset_id IN ({$data})";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;
                    
                    $this->_querySQL = "UPDATE dat_envioalarma SET
                                                n_envioalarma_mensajesenviados = 0,n_estatus_id = 4
                                                WHERE n_handset_id IN ({$data})";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;
                    
                    $this->_querySQL = "UPDATE dat_alarma SET
                                                n_estatus_id=2
                                                WHERE n_handset_id IN ({$data}) 
                                                        AND  n_estatus_id NOT IN (2)";
                                                
                    //var_dump($this->_querySQL);die();
                                                        
                    if (!$this->_execute($this->_querySQL))
                        return false;                    
                    
                    return true;
                    

                }                                
                
                
                
	}//<<----------------------------------------------------- End Class Base_Dat_Handset

?>