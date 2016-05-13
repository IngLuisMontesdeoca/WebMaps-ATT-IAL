<?php

error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED | E_WARNING));
    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>                                  *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    recuperacion de contraseña			                                *
    ********************************************************************************/
	    
	//---- CONFIG ----//
	require_once '../config.php';
	require_once ROOT_ESTILOS;
	
	//---- PAQUETES ----//
		//---- BD ----//
		Package::usePackage('BD');
			//---- Tables ----//
			Package::import('tables');
			//---- Tables ----//
			Package::import('views');
		//---- Classes ----//
		Package::usePackage('Classes');
			//---- template ----//
			Package::import('Template');
			//---- debig ----//
			Package::import('Debug');
			//---- debig ----//
			Package::import('Cadenas');

        if(isset($_GET['token']))
            $token = $_GET['token'];
        else
        {
            Session::goLogin();
            exit();
        }
        
        if(isset($_GET['usr']))
            $nombreUsuario = $_GET['usr'];
        else
        {
            Session::goLogin();
            exit();
        }
        
        $arrVariables['FORMULARIO'] = '';
        
        $arrVariables['scripts']="<script type='text/javascript' src='../../js/jQuery/plugins/md5-min.js'></script>
                                    <script type='text/javascript' src='../../js/Login/activateCuenta.js'></script>";
        
        //Instancias
        $baseRecoveryPassword = new Base_Dat_RecoveryPassword();
        
        $fechaRecovery = $baseRecoveryPassword->getRecoveryFechaExpira($token);
        
        
        if($fechaRecovery == '0')
        {
            $vista = str_replace('{DOMINIO}', 'http://'.$_SERVER["SERVER_NAME"], file_get_contents(ROOT_TEMPLATES.'/XHTML/Login/_password_3.html'));
            $arrVariables['EXPIRA'] = $vista;
        }
        else
        {
                $a = strtotime(substr(date('Y-m-d H:i:s'),0,-3));
                $b  = strtotime(substr($fechaRecovery,0,-3));

                $duraciona = $b-$a;
                
                $horas = (int) ($duraciona/3600);  
                if(0 > $horas)
                {
                    $vista = str_replace('{DOMINIO}', 'http://'.$_SERVER["SERVER_NAME"], file_get_contents(ROOT_TEMPLATES.'/XHTML/Login/_password_1.html'));
                    $arrVariables['EXPIRA'] = $vista;
                }
                else
                {
                    $formulario = str_replace('{TOKEN}', $token, file_get_contents(ROOT_TEMPLATES.'/XHTML/Login/_password_2.html'));
                    $arrVariables['EXPIRA'] = $formulario;
                }
        }
        
	//--------------- XHTML VIEW ------------------//
	$XHTML = new Plantilla('Login/password.html');
	$XHTML->asignaVariables($arrVariables);
	$XHTML->construyeVista();
	$XHTML->getVista(true);
        
	//---------------------------------------------// 
?>