<?php

error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Consultar Log			                                *
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
            
            if(isset($_POST['actividad']))
                $idActivity = $_POST['actividad'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreActivity']))
                $nombreActivity = $_POST['nombreActivity'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['fechaFin']))
                $fFin = $_POST['fechaFin'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['fechaIni']))
                $fIni = $_POST['fechaIni'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['usuario']))
                $idUsuario = $_POST['usuario'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['nombreUsuario']))
                $nombreUsuario = $_POST['nombreUsuario'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['enablDisablUs']))
                $actDesUsuario = $_POST['enablDisablUs'];
            else
            {
                echo $zero;
                exit();
            }                        
            
            if($actDesUsuario == '0')
                $dActDesUsuario = ' Estatus Usuario = Activo';
            else
                $dActDesUsuario = ' Estatus Usuario = Inactivo';
            //INSTANCIAS
            $baseActivityLog = new Base_Dat_ActivityLog();                     
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->c_activitylog_desc = 'Consultar Log: Identificador Actividad='.$idActivity.' Nombre Actividad='.$nombreActivity.' Fecha Inicio='.$fIni.' Fecha Fin='.$fFin.' Identificador Usuario='.$idUsuario.' Nombre Usuario='.$nombreUsuario.$dActDesUsuario;
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 20;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
            $isSave = $baseActivityLog->save() ? TRUE: FALSE;
            
            if($isSave)
                echo $baseActivityLog->getTableActivityLogConsulta($fIni, $fFin, $idActivity, $idUsuario, $actDesUsuario);
            else
                echo $zero;
            
?>
