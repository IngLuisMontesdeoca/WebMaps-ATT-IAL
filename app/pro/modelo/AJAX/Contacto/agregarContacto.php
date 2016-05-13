<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Agrega Contactos			                                *
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
            
            if(isset($_POST['idHandset']))
                $idPtn = $_POST['idHandset'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombrePtn']))
                $nombrePtn = $_POST['nombrePtn'];
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
            
            if(isset($_POST['tipoContacto']))
                $idTipo = $_POST['tipoContacto'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreTipoContacto']))
                $nombreIdTipo = $_POST['nombreTipoContacto'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['numeroCorreo']))
                $nombreTipo = $_POST['numeroCorreo'];
            else
            {
                echo $zero;
                exit();
            }
            
            //INSTANCIAS
            $baseContacto = new Base_Dat_Contacto();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 25;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];

            $data = array();
            
            $data['c_contacto_nombre'] = iconv('utf-8', 'iso-8859-1', $nombreContacto);
            $data['c_contacto_numerocorreo'] = $nombreTipo;
            $data['c_contacto_tipocontacto'] = $idTipo;
            $data['d_contacto_fechamodificacion'] = date('Y-m-d H:i:s');            
            $data['n_estatus_id'] = 3;
            $data['n_handset_id'] = $idPtn;
            
            $isSave = $baseContacto->insertaContacto($data);
             
             if(((int)$isSave) != 0)
             {
                $idContacto = $baseContacto->getPk();
                $baseActivityLog->c_activitylog_desc = 'Agregar Contacto: Identificador Ptn='.$idPtn.' Nombre Ptn='.$nombrePtn.' Identificador Contacto='.$idContacto.' Nombre Contacto='.$nombreContacto.' Identificador Tipo='.$idTipo.' Nombre Identificador Tipo='.$nombreIdTipo.' Tipo Descripcion='.$nombreTipo.' Nota= Se Agrego Contacto';
                $baseActivityLog->save();
                //echo utf8_encode($idContacto);  
                echo utf8_encode('1');  
             }
             else
             {
                $baseActivityLog->c_activitylog_desc = 'Agregar Contacto: Identificador Ptn='.$idPtn.' Nombre Ptn='.$nombrePtn.' Nombre Contacto='.$nombreContacto.' Identificador Tipo='.$idTipo.' Nombre Identificador Tipo='.$nombreIdTipo.' Tipo Descripcion='.$nombreTipo.' Nota= No se Agrego Contacto';
                $baseActivityLog->save();
                echo utf8_encode('0');                  
                 
             }
            
?>