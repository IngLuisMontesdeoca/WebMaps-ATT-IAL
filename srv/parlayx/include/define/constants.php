<?php

    /****************************************************************
    *                                                               *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>       *
    *   @version:       1.0                                         *
    *   @created        03/02/2009                                  *
    *   @copiright:     Copyright (c) 2009, SkyTel                  *
    *   @link:          http://localhost/suauto/include/defines Go  *
    *   @description    Constantes generales del Proyecto           *
    *                                                               *
    *****************************************************************/

	//Ra�z
		//Ra�z del proyecto
		define( 'ROOT_PROYECT', ROOT );
			//LIB
			define( 'ROOT_LIB', ROOT_PROYECT.'/lib' );
			    //PhpMailer
				define( 'ROOT_PHPMAILER', ROOT_LIB.'/phpMailer_v2.3/class.phpmailer.php' );
				//AdoDB
				define( 'ROOT_ADODB', ROOT_LIB.'/adodb');
				//Estilos
				define( 'ROOT_ESTILOS', ROOT_LIB.'/lookandfeel/estilos.php' );
			//Include
			define( 'ROOT_INCLUDE', ROOT_PROYECT.'/include' );
				//BD
				define( 'ROOT_BD', ROOT_INCLUDE.'/BD' );
				//Classes    
				define( 'ROOT_CLASS', ROOT_INCLUDE.'/classes' );
				//Funciones    
				define( 'ROOT_FUNC', ROOT_INCLUDE.'/functions' );
				//Definiciones    
				define( 'ROOT_DEF', ROOT_INCLUDE.'/define' );		
            //Vistas
            define( 'ROOT_TEMPLATES', ROOT_PROYECT.'/vistas' );
            //Vistas Din�micas
            define( 'ROOT_SCRIPTS', ROOT_PROYECT.'/vistasDinamicas' );
			//CSS
			define( 'ROOT_CSS', ROOT_PROYECT.'/css' );
			
			
	//HTML
	define('ROOT_HTML', '');
		//Java Script
		define( 'HTML_JS',  ROOT_HTML.'/js' );
		//CSS
		define( 'HTML_CSS', ROOT_HTML.'/css' );

    //Semilla Hash
	define('SEMILLA_HASH', '');
	
	//Directorio de HTML para correo
	define( 'HTMLCorreo',ROOT_TEMPLATES.'/XHTML/');
?>