<?php

error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    consulta usuario			                                *
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
        
            //INSTANCIAS
            $baseUsuario   = new Base_Dat_Usuario();
            $baseActivityLog = new Base_Dat_ActivityLog();        
        
            if(isset($_POST['todos']))
            {
                if($_POST['todos'] == '1')
                {
/*                    $baseActivityLog->setPk(0);
                    $baseActivityLog->c_activitylog_desc = 'Reestablecer Tabla Usuarios';
                    $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
                    $baseActivityLog->n_acivity_id = 7;
                    $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];

                    $isSave = $baseActivityLog->save() ? TRUE: FALSE;

                    if($isSave)
                    {*/
                        echo $baseUsuario->getTableUsuario();
                        exit();                        
/*                    }
                    else
                    {    
                       echo utf8_encode('0');
                       exit();
                    }*/
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
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->c_activitylog_desc = 'Buscar Usuario: Patron = '.$keyPatron.' Criterio = '.$criterio;
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 7;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
            $isSave = $baseActivityLog->save() ? TRUE: FALSE;            
            
            if($isSave)
                echo $baseUsuario->getTableUsuarioConsulta($keyPatron, $criterio);
            else
                echo $zero;
            
?>
