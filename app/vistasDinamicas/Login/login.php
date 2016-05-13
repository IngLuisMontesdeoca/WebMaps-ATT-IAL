<?php

    /********************************************************************************
    *   @autor:         Jorge A. Cepeda <jorge.cepeda@webmaps.com.mx> 	            *
    *   @version:       1.0                                     					*
    *   @created:       06/02/2014                    								*
    *   @copiright:     Copyright (c) 2014, WebMaps              					*
    *   @description    Login 					       								*
    ********************************************************************************/
	    
	//---- CONFIG ----//
	require_once '../config.php';
	
	//---- PAQUETES ----//
		//---- Classes ----//
		Package::usePackage('Classes');
			//---- template ----//
			Package::import('Template');
                        
                    $arrVariables['CERRARSESION']='';
                    $arrVariables['CLASSCERRARSESION']='';                                            
        
        if(isset($_GET['destoy']))
        {
            switch($_GET['destoy'])
            {
                case '1':
                    
                    $arrVariables['CERRARSESION']='Sesion Cerrada Exitosamente';
                    $arrVariables['CLASSCERRARSESION']='cssValidation_Green';                    
                    
                break;
                
                case '2':
                    
                    $arrVariables['CERRARSESION']='Contraseña registrada correctamente';
                    $arrVariables['CLASSCERRARSESION']='cssValidation_Green';                    
                    
                break;
            }
        }
        
	$arrVariables['scripts'] = '
	                          	<script type="text/javascript" src="'.HTML_JS.'/jQuery/plugins/md5-min.js"></script>
                                        <script type="text/javascript" src="'.HTML_JS.'/validacionFormularios.js"></script>
	                            ';
	
	//--------------- XHTML VIEW ------------------//
	$XHTML = new Plantilla('Login/login.html');
	$XHTML->asignaVariables($arrVariables);
	$XHTML->construyeVista();
	$XHTML->getVista(true);
	//---------------------------------------------// 
?>