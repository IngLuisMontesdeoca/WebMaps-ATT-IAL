<?php
error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>                                  *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Página principal del sistema                                                   *
    ********************************************************************************/
    

	//---- CONFIG ----//
	require_once '../../config.php';
        require_once '../../../lib/Config/config.php';
	
	//---- PAQUETES ----//
		//---- BD ----//
		Package::usePackage('BD');
			//---- Tables ----//
			Package::import('tables');
			//---- Views ----//
			Package::import('views');
		//---- Classes ----//
		Package::usePackage('Classes');
			//---- Debug ----//
			Package::import('Debug');
                        
         if(!(Debug::ajaxRequest()))
         {
                 if(is_null(Session::goLogin()))
                     header("Location: ../../Home");
                 exit();
         }                        
	
	//---POST----//
        if(isset($_POST['login']))
            $loginUsuario = $_REQUEST['login'];
        else
        {
            echo utf8_encode('0');
            exit();
        }

        if(isset($_POST['passwd']))
            $passwordUsuario = sha1($_REQUEST['passwd'].$_CONFIG['SEMILLA'],false);
        else
        {
            echo utf8_encode('0');
            exit();            
        }
        
        //INSTANCIAS
        $baseUsuario = new Base_Dat_Usuario();
        $baseAccessLog = new Base_Dat_AccessLog();
        $baseActivityLog = new Base_Dat_ActivityLog();
        
        $ipReal = Session::getRealIP();
        
        $isUsuarioLogin = $baseUsuario->isUsuarioLogin($loginUsuario,$passwordUsuario);
        
        if($isUsuarioLogin == '2')
        {
            echo utf8_encode ('2');
            exit();
        }
        
        
        if($isUsuarioLogin == '1')
        {
            $baseAccessLog->setCierraSessionOtraUbicacion($_SESSION['idUsuario']);
            
            $baseAccessLog->setPk(0);
            $baseAccessLog->d_accesslog_fechaingreso = date('Y-m-d H:i:s');
            $baseAccessLog->c_usuario_ip = $ipReal;
            $baseAccessLog->c_usuario_dns = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $baseAccessLog->c_accesslog_navegador = $_SERVER['HTTP_USER_AGENT'];
            $baseAccessLog->n_usuario_id=$_SESSION['idUsuario'];
            $baseAccessLog->d_accesslog_fechasalida = '0000-00-00 00:00:00';
            $baseAccessLog->c_accesslog_sessionid = session_id();
            
            $isSave = $baseAccessLog->save()? TRUE:FALSE;
            
            if($isSave)
            {
                $isSave= FALSE;
                
                $_SESSION['idLog'] = $baseAccessLog->getPk();
                
                $baseActivityLog->setPk(0);
                $baseActivityLog->c_activitylog_desc = 'Inicia Sesión';
                $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
                $baseActivityLog->n_activity_id = 11;
                $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];

                $isSave = $baseActivityLog->save() ? TRUE: FALSE;    
                
                /*session_id('9a6125a575b053d523050c7726a8fab2'); session_start(); session_destroy();*/
                /*session_id*/
                
                if($isSave)
                    echo utf8_encode ('1');
                else
                    echo utf8_encode ('0');
            }
            else
            {
                
                echo utf8_encode ('0');
            }
        }
        else
            echo utf8_encode($isUsuarioLogin);
        
?>
