<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar Gonz·lez <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Guarda Usuarios			                                *
    ********************************************************************************/

   //---- CONFIG ----//
   require_once '../../config.php';
   require_once '../../../lib/Config/config.php';
   require_once '../../../lib/phpmailer/class.phpmailer.php';   
   
   //---- PAQUETES ----//
	   //---- BD ----//
	   Package::usePackage('BD');
	      //---- Tables ----//
	      Package::import('tables');
           //---- Classes ----//
	   Package::usePackage('Classes');
	      //---- Debug ----//
	      Package::import('Debug');

	//---Validar inicio de sesiÛn       
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
            
            if(isset($_POST['nombreUsuario']))
                $nombreUsuario = $_POST['nombreUsuario'];
            else
            {
                echo $zero;
                exit();
            }

            if(isset($_POST['login']))
                $loginUsuario = $_POST['login'];
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
            
            if(isset($_POST['tipoUser']))
                $idTipoUsuario = $_POST['tipoUser'];
            else
            {
                echo $zero;
                exit();
            }      
            
            if(isset($_POST['nombreTipoUser']))
                $nombreTipoUsuario = $_POST['nombreTipoUser'];
            else
            {
                echo $zero;
                exit();
            }                  
            
            //INSTANCIAS
            $baseUsuario = new Base_Dat_Usuario();
            $baseActivityLog = new Base_Dat_ActivityLog();
            $baseRecoveryPassword = new Base_Dat_RecoveryPassword();
            
            $descripcion = 'Guardar Usuario: Nombre Usuario='.$nombreUsuario.' Login Usuario='.$loginUsuario.' Email Usuario='.$emailUsuario.' Identificador Tipo Usuario='.$idTipoUsuario.' Nombre Tipo Usuario='.$nombreTipoUsuario;
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 16;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            

            
//            if($isSave)
//            {
                $isUsuario = $baseUsuario->isUsuario($emailUsuario, $loginUsuario);

                if($isUsuario == '1')
                {
                    $baseActivityLog->c_activitylog_desc = $descripcion.' Nota = El usuario ya exsite, no se guardo.';
                    $baseActivityLog->save();
                    echo utf8_encode('Existe');
                }
                else
                {
                    $baseUsuario->setPk(0);
                    $baseUsuario->c_usuario_nombre = iconv("UTF-8", "ISO-8859-1", $nombreUsuario);
                    $baseUsuario->c_usuario_login = $loginUsuario;
                    $baseUsuario->c_usuario_password = '';
                    $baseUsuario->c_usuario_email = $emailUsuario;
                    $baseUsuario->n_tipousuario_id = $idTipoUsuario;
                    $baseUsuario->n_estatus_id = 3;

                    $isSave = $baseUsuario->save() ? TRUE:FALSE;

                            $dominio = 'http://'.$_SERVER["SERVER_NAME"];
                            $semilla     = $_CONFIG['SEMILLAEMAIL'];
                    if($isSave)
                    {
                        $idUsuario = $baseUsuario->getPk();
                        $isSave = FALSE;
                        $fechaHoy = date('Y-m-d H:i:s');
                        $token       = base64_encode(base64_encode($semilla)."314159265358979323846".base64_encode($fechaHoy));                        
                        $baseRecoveryPassword->setPk(0);
                        $baseRecoveryPassword->c_recovery_code = $token;
                        $baseRecoveryPassword->n_usuario_id = (int)$idUsuario;
                        $baseRecoveryPassword->d_recovery_fechacreacion = $fechaHoy;
                        $baseRecoveryPassword->d_recovery_fechaexpiracion = date('Y-m-d H:i:s',strtotime($fechaHoy)+86400);
                                
                        $isSave = $baseRecoveryPassword->save()? TRUE: FALSE;
                        
                        if($isSave)
                        {        

                            /**********Env√≠o de correo para activaci√≥n de la cuenta*************/ 
                            $Mail = new PHPMailer();
                            $bodyMail = file_get_contents('../../../vistas/XHTML/Login/RecoveryPassword.html');
                            $bodyMail = str_replace("{LINK}", "<a style='color:#E05414' href='{$dominio}/Password?usr=".md5($semilla.$nombreUsuario)."&token={$token}'>aqu&iacute;</a>.",$bodyMail);
                            $bodyMail = str_replace("{NOMBRE}", $nombreUsuario ,$bodyMail);
                            $bodyMail = str_replace("{LOGIN}", "<a style='color:#1155CC'>".$loginUsuario."</a>",$bodyMail);

                            
                            $Mail->Host     = "fleetrackers.com.mx";
                            $Mail->IsSMTP();
                            $Mail->SMTPDebug = 1;
                            $Mail->Mailer    = "smtp";
                            $Mail->SMTPAuth  = TRUE;
                            $Mail->Username = "soporte@fleetrackers.com.mx";  // a valid email here
                            $Mail->Password = "+Mexico01";
                            $Mail->From      = 'info@nextel.com.mx';
                            $Mail->FromName  = "Nextel de MÈxico";
                            $Mail->Subject   = "Activacion de cuenta";
                            $Mail->IsHTML(true);
                            $Mail->AddEmbeddedImage('../../../css/Home/images/imgBanner01.png', 'imgBanner01.png','../../../css/Home/images/imgBanner01.png','base64','imageimage/png');                            
                            $Mail->AddBCC("cesar.gonzalez@webmaps.com.mx");
                            $Mail->MsgHTML($bodyMail);
                            $Mail->AddAddress($emailUsuario); 

                            if($Mail->Send())
                            {
                                $baseActivityLog->c_activitylog_desc = $descripcion.' Nota = El usuario se guardo.';
                                $baseActivityLog->save();
                                echo $stat=  utf8_encode($idUsuario);
                                $Mail->ClearAddresses();
                            } 
                            else 
                            {
                                    $baseActivityLog->c_activitylog_desc = $descripcion.' Nota = Error al enviar email, no se guardo.';
                                    $baseActivityLog->save();                                
                                    $baseUsuario->setPk($idUsuario);
                                    $baseUsuario->n_estatus_id = 5;
                                    echo $stat= utf8_encode('ErrorMail');
                                    $Mail->ClearAddresses();  
                            }                         
                        }
                        else
                        {
                            $baseActivityLog->c_activitylog_desc = $descripcion.' Nota = El usuario no se guardo.';
                            $baseActivityLog->save();                            
                            $baseUsuario->setPk($idUsuario);
                            $baseUsuario->n_estatus_id = 5;
                            echo utf8_encode('0');
                        }
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = $descripcion.' Nota = El usuario no se guardo.';
                        $baseActivityLog->save();                        
                        echo $zero;
                    }

                }
//            }
//            else
//                echo $zero;
            
?>