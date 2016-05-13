<?php

	/************************************************************
    *                                                           *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>	*
    *   @version:       2.0                                     *
	*	since			12/07/2006								*
	*	modified		12/03/2009								*
    *   @copiright:     Copyright (c) 2008, SkyTel              *
    *   @link:          http://localhost/skyWeb/include/BD   Go *
    *   Descripción:    Clase para log básico de errores de 	*
	*					inserción y edición de datos en una BD.	*
	*															*
    ************************************************************/

	//---- REQUIRE ----//
		//Clase Base
			require_once "Base.class.php";
	
  final class Base_Bitacora extends Base 
  {//---------------------------------------------------------------------------------->>> Class Base_Bitacora
		
	/*
	* Constructor
	* @methods	Base::setAndConect(), Base::setPk()
	* @params	categoria->Categoría del Error a reportar.
	*			accion->Acción que se trató de ejecutar <<INSERT o UPDATE>>
	*			objetivo->Objetivo de la bitácora
	*			comentarios->Comentarios que describen el error ocurrido
	*/
		function __construct( $categoria, $accion, $objetivo, $comentarios )
		{//<<-------------------------------------------------------------- Constructor
			
			 parent::__construct('bitacora', 0);
	
            //Seteo de campos
			$this->idWebUser = ( isset($_SESSION['idWebUser']) ) ? Escapa::escapar( $_SESSION['idWebUser'] ) : 0;
			$this->hora = date('H:i:s');
			$this->fecha = date('Y-m-d');
			$this->accion = $accion;
			$this->objetivo = $objetivo;
			$this->comentarios =  $comentarios;
			$this->ip = $_SERVER['REMOTE_ADDR'];
            $this->categoria = $categoria;
			$this->url = ( isset($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER']:'';
			
		}//<<-------------------------------------------------------------- End Constructor
				
		
	/*
	* Destructor
	*/
		function __destruct(){
			//echo "El objeto Base_Bitacora ha sido destruido";
		}
				
  }//---------------------------------------------------------------------------------->>> End Class Base_Bitacora

?>