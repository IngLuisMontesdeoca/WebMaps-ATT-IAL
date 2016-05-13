<?php
error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Agregar Configuracion			                                *
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
            
            if(isset($_POST['idHandset']))
                $idPtn = $_POST['idHandset'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreHandset']))
                $nombrePtn = $_POST['nombreHandset'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['msjEnviados']))
                $idTiempo = $_POST['msjEnviados'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['nombreEnviados']))
                $nombreFrecuencia = $_POST['nombreEnviados'];
            else
            {
                echo $zero;
                exit();
            }                        
            
            if(isset($_POST['msjPeriodoEnvio']))
                $idFrecuencia = $_POST['msjPeriodoEnvio'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['nombrePeriodoEnvio']))
                $nombreTiempo = $_POST['nombrePeriodoEnvio'];
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
            $baseActivityLog->n_activity_id = 23;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
//            if($isSave)
//            {
//                $isHandset = $baseHandset->isHandset($idCliente, $idCuenta, $nombrePtn);

//                if($isHandset == '1')
//                    echo utf8_encode('Existe');
//                else
//                {
                    $baseHandset->setPk((int)$idPtn);
                    $baseHandset->n_handset_intervalo = $idFrecuencia;
                    $baseHandset->n_handset_duracion = $idTiempo;

                    $isSave = $baseHandset->save() ? TRUE:FALSE;

                    if($isSave)
                    {
                        $baseActivityLog->c_activitylog_desc = 'Configurar Envio: Identificador PTN='.$idPtn.' Nombre PTN='.$nombrePtn.' Identificador Frecuencia='.$idFrecuencia.' Nombre Frecuencia='.$nombreFrecuencia.' Identificador Mensaje='.$idTiempo.' Nombre Mensaje='.$nombreTiempo.' Nota=La configuracion se guardo';
                        $baseActivityLog->save();
                        echo utf8_encode ('1');
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = 'Configurar Envio: Identificador PTN='.$idPtn.' Nombre PTN='.$nombrePtn.' Identificador Frecuencia='.$idFrecuencia.' Nombre Frecuencia='.$nombreFrecuencia.' Identificador Mensaje='.$idTiempo.' Nombre Mensaje='.$nombreTiempo.' Nota=La configuracion no se guardo';
                        $baseActivityLog->save();                        
                        echo $zero;
                    }

//                }
 //           }
 //           else
 //               echo $zero;
            
?>