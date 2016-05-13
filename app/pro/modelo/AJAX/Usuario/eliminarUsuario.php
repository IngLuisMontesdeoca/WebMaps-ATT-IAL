<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Elimina Usuarios			                                *
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
            $miUsuario = false;
            
            if(isset($_POST['ID_User']))
                $idUsuario = $_POST['ID_User'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreUsuario']))
                $loginUsuario = $_POST['nombreUsuario'];
            else
            {
                echo $zero;
                exit();
            }
            
            //INSTANCIAS
            $baseUsuario = new Base_Dat_Usuario();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->c_activitylog_desc = 'Eliminar Usuario: Usuario[{Identificadores},{Login}]=Usuario[{'.$idUsuario.'},{'.$loginUsuario.'}]';
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 8;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
            $isSave = $baseActivityLog->save() ? TRUE: FALSE;            
            
            if($isSave)
            {
                $arrayIdUsuario = array();

                $arrayIdUsuario = explode('|', $idUsuario);
                $nIdUsuario = count($arrayIdUsuario);

                for($i=0;$i<$nIdUsuario;$i++)
                {

                    $baseUsuario->setPk((int)$arrayIdUsuario[$i]);
                    $baseUsuario->n_estatus_id=5;
                    $baseUsuario->save();     
                    
                    if($arrayIdUsuario[$i] == $_SESSION['idUsuario'])
                        $miUsuario = true;

                }
                
                if($miUsuario)
                    session_destroy();

                echo utf8_encode('1');
            }
            else
                echo $zero;
            
            
?>