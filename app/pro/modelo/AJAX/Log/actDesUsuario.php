<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    cargar activos y desactivos Usuarios			                                *
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
            
              $zero = utf8_encode('0');              
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
              if(isset($_POST['enablDisablUs']))
                  $actDesUsuario = $_POST['enablDisablUs'];
              else
              {
                  echo utf8_encode($zero);
                  exit();
              }
              
              //INSTANCIAS
              $baseUsuario = new Base_Dat_Usuario();

              $optionActDesUsuario = $baseUsuario->getOptionActDesUsuario($actDesUsuario);
              
              if($actDesUsuario == '0')
                $optionActDesUsuario = str_replace('{ADDOPTION}','<option value = "0" selected>Todos los Usuarios Activos</option>', $optionActDesUsuario);
              else
                $optionActDesUsuario = str_replace('{ADDOPTION}','<option value = "0" selected>Todos los Usuarios Inactivos</option>', $optionActDesUsuario);                  
              
              echo $optionActDesUsuario;

              
?>