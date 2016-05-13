<?php

    /************************************************************************
    *                                                                   	*
    *    @autor:         Julio Mora <julio.mora@skytel.com.mx>          	*
    *    @version:       1.0                                            	*
    *    @created        14/04/2009                                     	*
    *    @copiright:     Copyright (c) 2008, SkyTel                     	*
    *    @link:          https://localhost/skyWeb/include/classes/Debug Go	*
    *    @description:   Clase que encripta y desencripta cierta informa-	*
	*					 ción que se utiliza en el Sistema.					*
	*                                                                   	*
    ************************************************************************/
        
  abstract class Encripta
  {//---------------------------------------------------------------------------------->>> Class Encripta
  
  	/*
	 * enc64()
	 * @access:			public static
	 * @description:	Encripta en base 64 el contenido de una variable
	 */	
		public static function enc64( $varContent = '' )
		{
			return base64_encode($varContent);
		}

  	/*
	 * dec64()
	 * @access:			public static
	 * @description:	Des-encripta en base 64 el contenido de una variable
	 */			
		public static function dec64( $varContent = '' )
		{
			return base64_decode($varContent);
		}


  	/*
	 * encURL()
	 * @access:			public static
	 * @description:	Codifica el contenido de una variable en formato URL
	 */			
		public static function encURL( $varContent = '' )
		{
			return urlencode($varContent);
		}


  	/*
	 * decURL()
	 * @access:			public static
	 * @description:	De-codifica el contenido de una variable en formato URL
	 */			
		public static function decURL( $varContent = '' )
		{
			return urldecode($varContent);
		}


  	/*
	 * encPass()
	 * @access:			public static
	 * @description:	Encripta en md5 el contenido de una variable que se utilizará
	 *					como password.
	 */			
		public static function encPass( $pass = '' )
		{
			$semilla = 'semilla';
			return md5("{$semilla}{$pass}");
		}
  
  
  }//---------------------------------------------------------------------------------->>> End Class Encripta