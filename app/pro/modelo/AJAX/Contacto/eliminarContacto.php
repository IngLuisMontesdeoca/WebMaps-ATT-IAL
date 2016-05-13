<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Elimina Contactos			                                *
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
            
            if(isset($_POST['idContacto']))
                $idContacto = $_POST['idContacto'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreContacto']))
                $nombreContacto = $_POST['nombreContacto'];
            else
            {
                echo $zero;
                exit();
            }
            
            //INSTANCIAS
            $baseContacto = new Base_Dat_Contacto();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->c_activitylog_desc = 'Eliminar Contacto: PTNs[{Identificadores},{Nombres}]=PTNs[{'.$idContacto.'},{'.$nombreContacto.'}]';
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 24;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
            $isSave = $baseActivityLog->save() ? TRUE: FALSE;            
            
            if($isSave)
            {
                $arrayIdContacto = array();
                $arrayNombreContacto = array();

                $arrayIdContacto = explode('|', $idContacto);
                $arrayNombreContacto = explode('|', $nombreContacto);
                $nContacto = count($arrayIdContacto);

                for($i=0;$i<$nContacto;$i++)
                {
                    $baseContacto->setPk((int)$arrayIdContacto[$i]);
                    $baseContacto->n_estatus_id=5;
                    $baseContacto->save();                    
                }

                echo utf8_encode('1');
            }
            else
                echo $zero;
            
?>