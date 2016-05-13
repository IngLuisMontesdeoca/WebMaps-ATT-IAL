<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    dar de alta contraseña			                                *
    ********************************************************************************/

   //---- CONFIG ----//
   require_once '../../config.php';
   require_once '../../../lib/Config/config.php';   
   
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

        if(isset($_POST['cod']))
            $token = $_POST['cod'];
        else
        {
            echo utf8_encode('0');
            exit();
        }
        
        if(isset($_POST['pass1']))
            $password1 = $_POST['pass1'];
        else
        {
            echo utf8_encode('0');
            exit();
        }
        
        if(isset($_POST['pass2']))
            $password2 = $_POST['pass2'];
        else
        {
            echo utf8_encode('0');
            exit();
        } 
        
        if(!($password1 == $password2))
        {
            echo utf8_encode('0');
            exit();            
        }
        
        //Instancias
        $baseRecoveryPassword = new Base_Dat_RecoveryPassword();
        $baseUsuario = new Base_Dat_Usuario();
        
        $infoRecovery = $baseRecoveryPassword->getRecoveryIdUsuario($token);
        
        if($infoRecovery == '0')
        {
            echo utf8_encode('0');
        }
        else
        {
            $baseUsuario->setPk($infoRecovery['idUsuario']);
            $baseUsuario->c_usuario_password = sha1($password1.$_CONFIG['SEMILLA'],false);
            $baseUsuario->n_estatus_id = 3;
            
            $isSave = $baseUsuario->save() ? TRUE:FALSE;
            
            if($isSave)
            {
                $isSave = FALSE;
                $baseRecoveryPassword->setPk($infoRecovery['idRecovery']);
                $baseRecoveryPassword->d_recovery_fechaactivacion = date('Y-m-d H:i:s');
                
                $isSave=$baseRecoveryPassword->save();
                
                if($isSave)
                {
                        if(isset($_SESSION['intentos_session']))
                            $_SESSION['intentos_session'] = 0;  
                        
                        if(isset($_COOKIE["centos"]))
                            setcookie("centos", $_SESSION['intentos_session'], time()-7200);
                        
                        if(isset($_SESSION['intentos_session']))
                            $_SESSION['intentos_session'] = NULL;
                            
                    echo utf8_encode ('1');
                }
                else
                    echo utf8_encode ('0');
            }
            else
            {
                echo utf8_encode('0');
            }
        }
        
        
?>