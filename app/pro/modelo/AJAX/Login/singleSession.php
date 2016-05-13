<?php

error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>                                  *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Verifica que solo hay una session                                               *
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
                     echo utf8_encode('SIN SESION');
                 
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
            $baseAccessLog = new Base_Dat_AccessLog();
            
            $baseAccessLog->setCierraSessionOtraUbicacionAjax($_SESSION['idUsuario']);
            
             if(!(isset($_SESSION['idUsuario'])))
             {
                echo utf8_encode('SIN SESION');
                exit();
             }            
            
?>