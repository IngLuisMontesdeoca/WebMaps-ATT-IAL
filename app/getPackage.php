<?php

    /****************************************************************
    *                                                               *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>       *
    *   @version:       1.0                                         *
    *   @created        03/02/2009                                  *
    *   @copiright:     Copyright (c) 2009, SkyTel                  *
    *   @link:          http://192.168.4.125/apps/v3/avl/      		*
    *   @description    Acceso a los paquetes del Sistema.          *
    *   @notes          Para poder incluir este archivo es necesario*
    *                   definir la constante 'access' en el script  *
    *                   que lo solicite.                            *
    *                                                               *
    *****************************************************************/
    
	defined('access') or die('Acceso Denegado');
    
	//RAÍZ DEL PROYECTO
	$root = $_SERVER['DOCUMENT_ROOT'];
		$rootProyect = "{$root}";
			//Include
			$include = "{$rootProyect}include";
				//Clasees
				$classes = "{$include}/classes";
					//Packages
					$package = "{$classes}/Packages";				
				//Define
				$define = "{$include}/define";
					
	//Constantes
	require_once "{$define}/constants.php";
	//Packages
    require_once "{$package}/Package.class.php";
    
    unset($root, $rootProyect, $include, $classes, $package, $define);
    
	//---- Paquetes ----//
    	//---- Classes ----//
			Package::usePackage('Classes');
			//---- Debug ----//
				Package::import('Debug');

?>