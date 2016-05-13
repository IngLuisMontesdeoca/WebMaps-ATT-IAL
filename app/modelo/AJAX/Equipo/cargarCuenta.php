<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    carga cuentas			                                *
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
              if(isset($_POST['idEmpresa']))
                  $idCliente = $_POST['idEmpresa'];
              else
              {
                  echo utf8_encode($zero);
                  exit();
              }
              
              //INSTANCIAS
              $baseCuenta = new Base_Dat_Cuenta();

              $comboCuenta = $baseCuenta->getComboCuenta($idCliente);
              
              $arrayBuscar = array('{CLASSHTML}','{IDHTML}');
              
              $arrayReemplazar = array('cssSel100','selLCuenta');
              $comboCuenta = str_replace($arrayBuscar, $arrayReemplazar, $comboCuenta);
              
              echo utf8_encode($comboCuenta);

              
?>