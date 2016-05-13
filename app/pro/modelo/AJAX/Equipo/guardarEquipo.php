<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Guarda Equipos			                                *
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
              
            //ASIGNAR VARIABLES
            $zero = utf8_encode('0');
            
            if(isset($_POST['idEmpresa']))
                $idCliente = $_POST['idEmpresa'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreEmpresa']))
                $nombreCliente = $_POST['nombreEmpresa'];
            else
            {
                echo $zero;
                exit();
            }                   

            if(isset($_POST['idCuenta']))
                $idCuenta = $_POST['idCuenta'];
            else
            {
                echo $zero;
                exit();
            }       
            
            if(isset($_POST['nombreCuenta']))
                $nombreCuenta = $_POST['nombreCuenta'];
            else
            {
                echo $zero;
                exit();
            }                               
            
            if(isset($_POST['PTN']))
                $nombrePtn = $_POST['PTN'];
            else
            {
                echo $zero;
                exit();
            }              
            
            if(isset($_POST['idRed']))
                $idRed = $_POST['idRed'];
            else
            {
                echo $zero;
                exit();
            }      
            
            if(isset($_POST['nombreRed']))
                $nombreRed = $_POST['nombreRed'];
            else
            {
                echo $zero;
                exit();
            }                               
            
            if(isset($_POST['idPlan']))
                $idPlan = $_POST['idPlan'];
            else
            {
                echo $zero;
                exit();
            }      
            
            if(isset($_POST['nombrePlan']))
                $nombrePlan = $_POST['nombrePlan'];
            else
            {
                echo $zero;
                exit();
            }                                           
            
            if(isset($_POST['idServicio']))
                $idServicio = $_POST['idServicio'];
            else
            {
                echo $zero;
                exit();
            }      
            
            if(isset($_POST['nombreServicio']))
                $nombreServicio = $_POST['nombreServicio'];
            else
            {
                echo $zero;
                exit();
            }                                                       
            
            if(isset($_POST['fechaCorte']))
                $fechaCorte = $_POST['fechaCorte'];
            else
            {
                echo $zero;
                exit();
            }    
            
            //INSTANCIAS
            $baseHandset = new Base_Dat_Handset();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 6;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
//            if($isSave)
//            {
                $isHandset = $baseHandset->isHandset($idCliente, $idCuenta, $nombrePtn);

                if($isHandset == '1')
                    echo utf8_encode('Existe');
                else
                {
                    /*$baseHandset->setPk(0);*/
                    $data = Array();
                    $data['c_handset_ptn'] = $nombrePtn;
                    $data['n_usernetwork_id'] = $idRed;
                    $data['n_cuenta_id'] = $idCuenta;
                    $data['n_estatus_id'] = 3;
                    $data['n_usertype_id'] = $idPlan;
                    $data['n_tipocontrato_id'] = $idServicio;
                    $data['n_canalsuscripcion_id'] = 1;
                    $data['d_handset_fecharegistro'] = $fechaCorte;
                    $data['n_tiposervicio_id'] = 2;
                    $isSave = $baseHandset->insertHandset($data);
                    //die($isSave);

                    //$isSave = $baseHandset->save() ? TRUE:FALSE;

                    if($isSave)
                    {
                        $idPtn = $baseHandset->getPk();
                        $baseActivityLog->c_activitylog_desc = 'Guardar Equipo: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Identificador Cuenta='.$idCuenta.' Nombre Cuenta='.$nombreCuenta.' Identificador PTN='.$idPtn.' Nombre PTN='.$nombrePtn.' Identificador Red='.$idRed.' Nombre Red='.$nombreRed.' Identificador Plan='.$idPlan.' Nombre Plan='.$nombrePlan.' Identificador Servicio='.$idServicio.' Nombre Servicio='.$nombreServicio.' Nota=El equipo se guardo';
                        $baseActivityLog->save();
                        echo utf8_encode ($isSave);
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = 'Guardar Equipo: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Identificador Cuenta='.$idCuenta.' Nombre Cuenta='.$nombreCuenta.' Nombre PTN='.$nombrePtn.' Identificador Red='.$idRed.' Nombre Red='.$nombreRed.' Identificador Plan='.$idPlan.' Nombre Plan='.$nombrePlan.' Identificador Servicio='.$idServicio.' Nombre Servicio='.$nombreServicio.' Nota=El equipo no se guardo';
                        $baseActivityLog->save();                        
                        echo $zero;
                    }

                }
 //           }
 //           else
 //               echo $zero;
            
?>