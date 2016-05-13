<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Reactivar Equipos			                                *
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
            
            if(isset($_POST['PTNs']))
                $ptn = $_POST['PTNs'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombrePTNs']))
                $nombrePtn = $_POST['nombrePTNs'];
            else
            {
                echo $zero;
                exit();
            }
            
            //INSTANCIAS
            $baseHandSet = new Base_Dat_Handset();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->c_activitylog_desc = 'Suspender Equipo: '.$ptn.','.$nombrePtn;
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 21;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
            $isSave = $baseActivityLog->save() ? TRUE: FALSE;
            
            
            if($isSave)
            {
                $arrayPtn = array();

                $arrayPtn = explode('|', $ptn);
                $nPtn = count($arrayPtn);

                for($i=0;$i<$nPtn;$i++)
                {

                    $baseHandSet->setPk((int)$arrayPtn[$i]);
                    $baseHandSet->n_estatus_id=3;
                    $baseHandSet->n_canalsuscripcion_id=1;
                    $baseHandSet->save();                    

                }

                echo utf8_encode('1');
            }
            else
                echo $zero;
            
            
?>