<?php
error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Consulta Equipos			                                *
    ********************************************************************************/

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
            
              
         $pagerLimit = 10;     
              
	//---Validar inicio de sesión       
         if(!(Debug::ajaxRequest()))
         {
                 if(is_null(Session::goLogin()))
                     header("Location: ../../Home");
                 
                 exit();
         }
         else
         {
             if(!(isset($_SESSION['idUsuario'])))
             {
                echo utf8_encode('SIN SESION');
                exit();
             }
         }
        
        
            //INSTANCIAS
            $baseHandSet   = new Base_Dat_Handset();            
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            if(isset($_GET['todos']))
            {
                if($_GET['todos'] == '1')
                {

					$sFiltro = array('','','','','','','','','','');
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

                    $respuestaHandset = $baseHandSet->getHandset($sFiltro[2],$sFiltro[3],$sFiltro[4],$sFiltro[5],$sFiltro[6],$sFiltro[7],$sFiltro[8],$sFiltro[9],($_GET['pag']*$_GET['tam']),$_GET['tam'],$sOrderC,$sOrderA);
                    $arrHeaders = array("#","","PTN","Cuenta","Cliente","Red","Plan","Servicio","Fecha de corte","Estatus","Opciones");
                    $total_registros = $respuestaHandset[1];
                    $tBody = $respuestaHandset[0];
                    
                    echo json_encode(array("registros"=>$total_registros,"tBody"=>'"'.$tBody.'"',"header"=>$arrHeaders));                        
                    exit();
                }
                else
                {
                    echo utf8_encode ('0');
                    exit();
                }
            }                
        
            //ASIGNAR VARIABLES
            $zero = utf8_encode('0');
            
            if(isset($_POST['keyPatron']))
                $keyPatron = $_POST['keyPatron'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['criterio']))
                $criterio = $_POST['criterio'];
            else
            {
                echo $zero;
                exit();
            }
            
            if($criterio=='red' || $criterio=='estatus' || $criterio=='plan' || $criterio=='servicio')
            {
                $tKeypatron = explode ('|', $keyPatron);
                $keyPatron = $tKeypatron[0];
                $descripcion = $tKeypatron[1];
            }
            else
                $descripcion = $keyPatron;
				
				
					$sFiltro = array('','','','','','','','','','');
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

            $baseActivityLog->setPk(0);
            $baseActivityLog->c_activitylog_desc = 'Consultar Equipos: Patron='.$descripcion.' Criterio='.$criterio;
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 1;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
            $isSave = $baseActivityLog->save() ? TRUE: FALSE;
                        
            if($isSave)
			{
                $respuestaHandset = $baseHandSet->getHandsetConsulta($keyPatron,$criterio,$sFiltro[2],$sFiltro[3],$sFiltro[4],$sFiltro[5],$sFiltro[6],$sFiltro[7],$sFiltro[8],$sFiltro[9],($_GET['pag']*$_GET['tam']),$_GET['tam'],$sOrderC,$sOrderA);
                $arrHeaders = array("#","","PTN","Cuenta","Cliente","Red","Plan","Servicio","Fecha de corte","Estatus","Opciones");
                $total_registros = $respuestaHandset[1];
                $tBody = $respuestaHandset[0];
                    
                echo json_encode(array("registros"=>$total_registros,"tBody"=>'"'.$tBody.'"',"header"=>$arrHeaders));
			}
            else
                echo $zero;
            
?>
