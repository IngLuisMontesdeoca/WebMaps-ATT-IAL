<?php

error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>                                  *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    cierra sesion                                                   *
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
        
        //INSTANCIAS
        $baseAccessLog = new Base_Dat_AccessLog();
        $baseActivityLog = new Base_Dat_ActivityLog();
        

        $baseAccessLog->setPk((int)$_SESSION['idLog']);
        $baseAccessLog->d_accesslog_fechasalida = date('Y-m-d H:i:s');

        $isSave = $baseAccessLog->save()? TRUE:FALSE;
            
        if($isSave)
        {
            $isSave= FALSE;
                
            $_SESSION['idLog'] = $baseAccessLog->getPk();
                
            $baseActivityLog->setPk($_SESSION['idLog']);
            $baseActivityLog->c_activitylog_desc = 'Cierra Sesión';
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 12;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];

            $isSave = $baseActivityLog->save() ? TRUE: FALSE;                
                
            if($isSave)
            {
                session_destroy();
                echo utf8_encode ('1');
            }
            else
                echo utf8_encode ('0');
        }
        else
            echo utf8_encode ('0');
        
?>