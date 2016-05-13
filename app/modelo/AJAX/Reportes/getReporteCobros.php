<?php
error_reporting (5); 

/* * ******************************************************************************
 *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              *
 *   @version:       1.0                                     					*
 *   @created:       21/11/2014                              					*
 *   @copiright:     Copyright (c) 2014, WebMaps              					*
 *   @description    Obtener reporte historico de cobros			                                *
 * ****************************************************************************** */

//---- CONFIG ----//
require_once '../../config.php';

//---- PAQUETES ----//
//---- BD ----//
Package::usePackage('BD');
//---- Tables ----//
Package::import('tables');
//---- Classes ----//
Package::usePackage('Classes');
//---- Debug ----//
Package::import('Debug');


//---Validar inicio de sesión       
if (!(Debug::ajaxRequest())) {
    if (is_null(Session::goLogin()))
        header("Location: ../../Home");

    exit();
}
else {
    if (!(isset($_SESSION['idUsuario']))) {
        echo utf8_encode('SIN SESION');
        exit();
    }
}

if (isset($_POST['fechaFin']))
    $fFin = $_POST['fechaFin'];
else {
    echo $zero;
    exit();
}

if (isset($_POST['fechaIni']))
    $fIni = $_POST['fechaIni'];
else {
    echo $zero;
    exit();
}

$tipoReporte = $_POST['tipoReporte'];

$baseActivityLog = new Base_Dat_ActivityLog();

$baseActivityLog->setPk(0);
$baseActivityLog->c_activitylog_desc = 'Reporte de cobros: Fecha Inicio=' . $fIni . ' Fecha Fin=' . $fFin;
$baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
$baseActivityLog->n_activity_id = 27;
$baseActivityLog->n_accesslog_id = (int) $_SESSION['idLog'];
$isSave = $baseActivityLog->save() ? TRUE : FALSE;

if ($isSave) {
    if ($tipoReporte == '1') {
        
        
					$sFiltro = array('','','','','');
					$sOrderA = '';
					$sOrderC = '';				
					
					if(is_array($_GET['filter']))
					{
						for($i = 0, $s = count($sFiltro); $i < $s; $i++)
						{
							if(array_key_exists ( $i , $_GET['filter'] ))
							{
								$sFiltro[$i] = $_GET['filter'][$i];
							}
							else
								$sFiltro[$i] = '';
							if(is_array($_GET['column']))	
                                                        {
                                                            if(array_key_exists ( $i , $_GET['column'] ))
                                                            {
                                                                    $sOrderA = $_GET['column'][$i];
                                                                    $sOrderC = $i;
                                                            }
                                                        }
						}
					}
					else
					{
						for($i = 0, $s = count($sFiltro); $i < $s; $i++)
						{
						
                                                        if(is_array($_GET['column']))
                                                        {
                                                            if(array_key_exists ( $i , $_GET['column'] ))
                                                            {
                                                                    $sOrderA = $_GET['column'][$i];
                                                                    $sOrderC = $i;
                                                            }
                                                        }
							
						}		
					}					        
        
        $baseDatHistoricoPayment = new Base_Dat_HistoricoMensajes();
        $result = $baseDatHistoricoPayment->getHistoricoMensajes($fIni,$fFin,$sFiltro[0],$sFiltro[1],$sFiltro[2],$sFiltro[3],$sFiltro[4],($_GET['pag']*$_GET['tam']),$_GET['tam'],$sOrderC,$sOrderA);
        
        $_SESSION['EXCEL'] = $result[0];
        $_SESSION['TIPOREPORTE'] = 'Message';
        
        $arrHeaders = array("PTN","Fecha","Numero","Mensaje","Estatus");
        $total_registros = $result[2];
        $tBody = $result[1];        
        echo json_encode(array("registros"=>$total_registros,"tBody"=>'"'.$tBody.'"',"header"=>$arrHeaders));
        
    } else {
        
					$sFiltro = array('','','','','','');
					$sOrderA = '';
					$sOrderC = '';				
					
					if(is_array($_GET['filter']))
					{
						for($i = 0, $s = count($sFiltro); $i < $s; $i++)
						{
							if(array_key_exists ( $i , $_GET['filter'] ))
							{
								$sFiltro[$i] = $_GET['filter'][$i];
							}
							else
								$sFiltro[$i] = '';
							if(is_array($_GET['column']))	
                                                        {
                                                            if(array_key_exists ( $i , $_GET['column'] ))
                                                            {
                                                                    $sOrderA = $_GET['column'][$i];
                                                                    $sOrderC = $i;
                                                            }
                                                        }
						}
					}
					else
					{
						for($i = 0, $s = count($sFiltro); $i < $s; $i++)
						{
						
                                                        if(is_array($_GET['column']))
                                                        {
                                                            if(array_key_exists ( $i , $_GET['column'] ))
                                                            {
                                                                    $sOrderA = $_GET['column'][$i];
                                                                    $sOrderC = $i;
                                                            }
                                                        }
							
						}		
					}					        
        
        $baseDatHistoricoPayment = new Base_Dat_HistoricoPayment();
        $result = $baseDatHistoricoPayment->getHistoricoCobro($fIni,$fFin,$sFiltro[0],$sFiltro[1],$sFiltro[2],$sFiltro[3],$sFiltro[4],$sFiltro[5],($_GET['pag']*$_GET['tam']),$_GET['tam'],$sOrderC,$sOrderA);
        
        $_SESSION['EXCEL'] = $result[0];
        $_SESSION['TIPOREPORTE'] = 'Payment';
        
        $arrHeaders = array("PTN","Fecha","Tipo de cobro","Contrato","Monto","Estatus","TransactionID");
        $total_registros = $result[2];
        $tBody = $result[1];        
        echo json_encode(array("registros"=>$total_registros,"tBody"=>'"'.$tBody.'"',"header"=>$arrHeaders));
    }
} else
    echo $zero;