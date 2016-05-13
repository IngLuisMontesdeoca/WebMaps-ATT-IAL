<?php

error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Editar Equipos			                                *
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
            
            if(isset($_POST['idPTN']))
                $idPtn = $_POST['idPTN'];
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
            $baseActivityLog->n_activity_id = 22;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
//            if($isSave)
//            {
//                $isHandset = $baseHandset->isHandset($idCliente, $idCuenta, $nombrePtn);

//                if($isHandset == '1')
//                    echo utf8_encode('Existe');
//                else
//                {
                    $baseHandset->setPk((int)$idPtn);
                    $baseHandset->n_usertype_id = (int)$idPlan;
                    $baseHandset->n_tipocontrato_id = (int)$idServicio;
                    if( (int)$idServicio == 1){
                        $baseHandset->d_handset_fecharegistro = $fechaCorte;
                    }

                    $isSave = $baseHandset->save() ? TRUE:FALSE;

                    if($isSave)
                    {
                        $baseActivityLog->c_activitylog_desc = 'Editar Equipo: Identificador PTN='.$idPtn.' Nombre PTN='.$nombrePtn.' Identificador Plan='.$idPlan.' Nombre Plan='.$nombrePlan.' Identificador Servicio='.$idServicio.' Nombre Servicio='.$nombreServicio.' Nota=El equipo se edito';
                        $baseActivityLog->save();
                        echo utf8_encode ('1');
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = 'Editar Equipo: Identificador PTN='.$idPtn.' Nombre PTN='.$nombrePtn.' Identificador Plan='.$idPlan.' Nombre Plan='.$nombrePlan.' Identificador Servicio='.$idServicio.' Nombre Servicio='.$nombreServicio.' Nota=El equipo no se edito';
                        $baseActivityLog->save();                        
                        echo $zero;
                    }

//                }
 //           }
 //           else
 //               echo $zero;
            
?>