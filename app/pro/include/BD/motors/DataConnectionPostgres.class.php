<?php

	/****************************************************************
    *                                                           	*
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   	*
    *   @version:       1.0                                     	*
	*	@created		08/01/2009									*
    *   @copiright:     Copyright (c) 2008, SkyTel              	*
    *   @link:          http://planfia.skytel.com.mx   Go        	*
    *   Descripción:    Clase que permite la conexión a Postgrees	*
	*					a partir de los drivers de ADOdb.	 		*
	*																*
    *****************************************************************/
	
	//---- REQUIRE ----//
		//ADOdb
			require_once "adodb.inc.php";
            require_once "adodb-postgres7.inc.php";
	
    		
	class DataConnectionPostgres extends ADODB_postgres7
	{//<<--------------------------------------------------------------------------- Class DataConnection
	
	  /*
	   * Constructor
	   * methods	ADOdb::Connect()
	   */	
		function __construct( $user = '', $password = '', $host = '', $db = '' )
		{//<<-------------------------------------------------------------------- Construct
								
			$conn = &$this;
			$conn = ADONewConnection('postgres8');
			$this->user                             = $user;
			$this->password                         = $password;
			$this->host                             = $host;
			$this->database                         = $bd;
			$this->Connect();
						
		}//<<-------------------------------------------------------------------- End Construct
	
	}//<<--------------------------------------------------------------------------- Class DataConnection

?>