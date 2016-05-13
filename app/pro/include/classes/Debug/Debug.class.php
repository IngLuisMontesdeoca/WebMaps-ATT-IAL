<?php

	/********************************************************************
    *                                                           	    *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   	    *
	*   @updater:       Tomás H. Muñoz <tomas.hernandez@webmaps.com.mx> *
    *   @version:       1.0                                     	    *
	*	@created:		25/02/2009									    *
	*   @updated:       26/02/2010                                      *
    *   @copiright:     Copyright (c) 2009, SkyTel              	    *
    *   @link:          http://localhost/suauto/include/class   Go	    *
    *   @description    Clase con métodos útiles para el debug de 	    *
	*					aplicaciones Web.							    *
	*																    *
    *********************************************************************/
	
  abstract class Debug
  {//---------------------------------------------------------------------------------->>> Class Debug
   
   /*
	* Muestra en pantalla la descripción, en forma legible para humanos, de una variable
	* @access	static public
	* @params	var->Variable a describir.
	*/
		static public function varDesc( $var = NULL )
		{//<<-------------------------------------------------------------- varDesc()
				
				ob_start();//<<----------------------- Inicio buffer
					var_dump($var);
					$description = ob_get_contents();
				ob_end_clean();//<<------------------- Fin buffer
				
				$description = "<pre>{$description}</pre>";
				
				return $description;
			
		}//<<-------------------------------------------------------------- End varDesc()
		
		
	/*
     * @access  static public
     * @desc    Obtiene información de un archivo.
     * @params  path->Ruta de un archivo
     */
		static public function fileData( $path ) 
		{//<<------------------------------------------------- fileData()
				
				// Vaciamos la caché de lectura de disco
				clearstatcache();
				
				// Comprobamos si el fichero existe
				$data["exists"] = is_file($path);
				
				// Comprobamos si el fichero es escribible
				$data["writable"] = is_writable($path);
				
				// Leemos los permisos del fichero
				$data["chmod"] = ($data["exists"] ? substr(sprintf("%o", fileperms($path)), -4) : false);
				
				// Extraemos la extensión, un sólo paso
				$data["ext"] = substr(strrchr($path, "."),1);
				
				// Primer paso de lectura de ruta
				$data["path"] = array_shift(explode(".".$data["ext"],$path));
				
				// Primer paso de lectura de nombre
				$data["name"] = array_pop(explode("/",$data["path"]));
				
				// Ajustamos nombre a FALSE si está vacio
				$data["name"] = ($data["name"] ? $data["name"] : false);
				
				// Ajustamos la ruta a FALSE si está vacia
				$data["path"] = ($data["exists"] ? ($data["name"] ? realpath(array_shift(explode($data["name"],$data["path"]))) : realpath(array_shift(explode($data["ext"],$data["path"])))) : ($data["name"] ? array_shift(explode($data["name"],$data["path"])) : ($data["ext"] ? array_shift(explode($data["ext"],$data["path"])) : rtrim($data["path"],"/")))) ;
				
				// Ajustamos el nombre a FALSE si está vacio o a su valor en caso contrario
				$data["filename"] = (($data["name"] OR $data["ext"]) ? $data["name"].($data["ext"] ? "." : "").$data["ext"] : false);
				
				// Devolvemos los resultados
				return $data;
				
		}//<<------------------------------------------------- End fileData()
		
		
	/*
     * @access  static public
     * @desc    Determina si un archivo es accedido vía AJAX y si se accede
	 *			por el archivo que lo debe acceder
     * @params  requestPath->URL del archivo que puede acceder a ese AJAX
     */
		static public function isAjax( $requestPath ) 
		{//<<------------------------------------------------- isAjax()
			
			$requestPathURL = ROOT_URL."/{$requestPath}";
			$requestPathURL2 = ROOT_URL2."/{$requestPath}";
			$requestPathDNS = ROOT_DNS."/{$requestPath}";
			
						
			//Comprobación de que la petición es vía AJAX
			if( !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest') ) {//<<----------------------- if AJAX
			
	 			exit('ACCESO DENEGADO: Este archivo no admite peticiones desde el sitio actual');
				
	 		} else {//<<----------------------- else AJAX
				
				
				switch( $_SERVER['HTTP_REFERER'] ) {//<<----------------------- switch REFERER
					
					case $requestPathURL:
					case $requestPathURL2:
					case $requestPathDNS:
						return true;
					break;
					
					default:
						exit("ACCESO DENEGADO: Acceso desde archivo incorrecto {$_SERVER['HTTP_REFERER']}");
					break;
				
				}//<<----------------------- End switch REFERER
				
			}//<<----------------------- End if AJAX
			
		}//<<------------------------------------------------- End isAjax()	
		
	   /***
	    *   @description:  Método que determina únicamente si una petición se hace vía AJAX
		*   @param:        void
		*   @return:       boolean
		*   @updater:      TH
		*   @updated_date: 26/02/2010
	    ***/
		static public function ajaxRequest(){
		   return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false;
		}	


	/*
     * @access  static public
     * @desc    Determina si una variable contiene algo y si dicho contenido es numérico
     * @params  var->variable a examinar
     */
		static public function isNum( $var = 0 ) 
		{//<<------------------------------------------------- isNum()
			return ( !empty($var) && is_numeric($var) ) ? true : false;
		}//<<------------------------------------------------- End isNum()
		
	/*
     * @access  static public
     * @desc    Determina si una variable contiene algo y si dicho contenido es sólo alfabético
     * @params  var->variable a examinar
     */
		static public function isAlfa( $var = '' ) 
		{//<<------------------------------------------------- isNum()
			return ( !empty($var) && is_string($var) && !is_numeric($var) ) ? true : false;
		}//<<------------------------------------------------- End isNum()
		
  }//---------------------------------------------------------------------------------->>> Class Debug
  
?>