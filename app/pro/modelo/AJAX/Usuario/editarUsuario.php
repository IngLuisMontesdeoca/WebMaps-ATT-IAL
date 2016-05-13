<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Edita Usuarios			                                *
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
            
            if(isset($_POST['id']))
                $idUsuario = $_POST['id'];
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
            
            if(isset($_POST['email']))
                $emailUsuario = $_POST['email'];
            else
            {
                echo $zero;
                exit();
            }              
            
            if(isset($_POST['tipoUsuario']))
                $idTipoUsuario = $_POST['tipoUsuario'];
            else
            {
                echo $zero;
                exit();
            }      
            
            if(isset($_POST['nombreTipoUsuario']))
                $nombreTipoUsuario = $_POST['nombreTipoUsuario'];
            else
            {
                echo $zero;
                exit();
            }                  

            //INSTANCIAS
            $baseUsuario = new Base_Dat_Usuario();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 9;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];        
            
//            if($isSave)
//            {
                $arrayIdUsuario = array();

                $arrayIdUsuario = explode('|', $idUsuario);
                $nIdUsuario = count($arrayIdUsuario);

                for($i=0;$i<$nIdUsuario;$i++)
                {

                    $baseUsuario->setPk((int)$arrayIdUsuario[$i]);
                    $baseUsuario->c_usuario_nombre = iconv("UTF-8", "ISO-8859-1", $nombreUsuario);
                    $baseUsuario->c_usuario_email = $emailUsuario;
                    $baseUsuario->n_tipousuario_id = $idTipoUsuario;
                    $baseUsuario->n_estatus_id = 3;

                    $isSave = $baseUsuario->save()? TRUE:FALSE;
                    
                    if($isSave)
                    {
                        $baseActivityLog->c_activitylog_desc = 'Editar Usuario: Identificador Usuario='.$idUsuario.' Nombre Usuario='.$nombreUsuario.' Identificador Tipo Usuario='.$idTipoUsuario.' Nombre Tipo Usuario='.$nombreTipoUsuario.' Email Usuario='.$emailUsuario.' Nota=Usuario editado';
                        $baseActivityLog->save();
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = 'Editar Usuario: Identificador Usuario='.$idUsuario.' Nombre Usuario='.$nombreUsuario.' Identificador Tipo Usuario='.$idTipoUsuario.' Nombre Tipo Usuario='.$nombreTipoUsuario.' Email Usuario='.$emailUsuario.' Nota=Usuario no editado';
                        $baseActivityLog->save();
                        echo $zero;
                        exit();
                    }

                }
                
                echo utf8_encode('1');
//            }
  //          else
//                echo $zero;
            
            
?>