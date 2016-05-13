<?php

	/************************************************************
    *                                                           *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   *
    *   @version:       1.0                                     *
	*	@created		08/01/2009								*
    *   @copiright:     Copyright (c) 2008, SkyTel              *
    *   @link:          http://addra.stopntrack.com/suauto   Go *
    *   Descripción:    Clase que permite la conexión a MySQL	*
	*					a partir de los drivers de ADOdb.	 	*
	*															*
    ************************************************************/
	
	//---- REQUIRE ----//
		//ADOdb 
            require_once 'adodb.inc.php'; 
			require_once 'adodb-mysqlt.inc.php';
			
	class DataConnectionMySQL extends ADODB_mysqlt
	{//<<--------------------------------------------------------------------------- Class DataConnection
	 
	  /*
	   * Constructor
	   * methods	ADOdb::Connect()
	   */	
		function __construct( $user = '', $password = '', $host = '', $db = '')
		{//<<-------------------------------------------------------------------- Construct
		    						
			if( empty($user) && empty($password) && empty($host) && empty($db) ) {
			
				$arrDominios = explode(".", $_SERVER['SERVER_NAME']);
				$file = "BD.ini";
				
				$ini  = parse_ini_file(ROOT_ADODB.'/ini/'.$file);
				$host = $ini['WebMaps.serv'];
				$user = $ini['WebMaps.user'];
				$pass = $ini['WebMaps.pass'];
				$db   = $ini['WebMaps.basd'];	
			}
			
			$conn = &$this;
			$conn = ADONewConnection('mysqlt');
			$this->user                             = $user;
			$this->password                         = $pass;
			$this->host                             = $host;
			$this->database                         = $db;
			$this->Connect();
						
		}//<<-------------------------------------------------------------------- End Construct
	
	}//<<--------------------------------------------------------------------------- Class DataConnection

?>
