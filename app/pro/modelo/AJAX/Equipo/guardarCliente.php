<?php

error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Guarda Cliente			                                *
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
            $isSave = FALSE;
            $isCliente = '0';
            $baseCliente = NULL;
            $nombreCliente = '';
            
            if(isset($_POST['nombreEmpresa']))
                $nombreCliente = $_POST['nombreEmpresa'];
            else
            {
                echo $zero;
                exit();
            }
            

            //INSTANCIAS
            $baseCliente = new Base_Dat_Cliente();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 4;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
//            if($isSave)
//            {
                $isCliente = $baseCliente->isCliente($nombreCliente);

                if($isCliente == '1')
                {
                    $baseActivityLog->c_activitylog_desc = 'Guardar Cliente: Nombre Cliente='.$nombreCliente.' Nota= La cliente ya existe';
                    $baseActivityLog->save();
                    echo $zero;
                }
                else
                {
                    $baseCliente->setPk(0);
                    $baseCliente->c_cliente_nombre=iconv("UTF-8", "ISO-8859-1", strtoupper($nombreCliente));
                    $baseCliente->n_estatus_id=3;

                    $isSave = $baseCliente->save() ? TRUE : FALSE;

                    if($isSave)
                    {
                        $idCliente = $baseCliente->getPk();
                        $baseActivityLog->c_activitylog_desc = 'Guardar Cliente: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Nota=La cliente se guardo';
                        $baseActivityLog->save();                        
                        echo utf8_encode($idCliente);
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = 'Guardar Cliente: Nombre Cliente='.$nombreCliente.' Nota=La cliente no se guardo';
                        $baseActivityLog->save();
                        echo utf8_encode('0');
                    }
                }
//            }
//            else
//                echo $zero
            
?>