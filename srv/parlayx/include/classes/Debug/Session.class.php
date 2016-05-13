<?php

    /************************************************************************
    *                                                                   	*
    *    @autor:         Julio Mora <julio.mora@skytel.com.mx>          	*
	*	 @updater:		 Joel Navarrete <joel.navarrete@webmaps.com.mx>
    *    @version:       1.0                                            	*
    *    @created        22/04/2009                                     	* 
    *    @copiright:     Copyright (c) 2009, SkyTel                     	*
    *    @link:          https://localhost/skyWeb/include/classes/Debug Go	*
    *    @description:   Clase que determina si se ha iniciado correctamente*
    *					 una Session en el servidor y toma desiciones sobre *
	*					 dónde dirigir al usuario en caso de errores.		*
    *                                                                   	* 
    ************************************************************************/
        
  abstract class Session
  {//---------------------------------------------------------------------------------->>> Class Session

	/*
	* Scripts que ameritan excepciones
	* @access    private static
	*/	
		private static $_exeptions = array('usersLogin.php', 'listenToLogin.php');
		
	/*
	* URL del login
	* @access    private static
	*/		
		private static $_login = '../../Login';
		
		private static $_realIP = '127.0.0.1';
	
		
	/*
	* Redirige a la página de Login
	* @access    public static
	*/
		public static function goLogin()
		{//<<-------------------------------------------------------------- Method goLogin  
						
			//Excepciones
			if( in_array( basename($_SERVER['SCRIPT_FILENAME']), self::$_exeptions) ) {
				return true;
			} else {	
				if( !isset($_SESSION['idUsuario']) ) {
					self::_goTo( self::$_login );
				}
			}
			
		}//<<-------------------------------------------------------------- End Method goLogin
		
		
	/*
	* Redirige a la página de Login
	* @access    public static
	*/
		public static function goToLogin()
		{//<<-------------------------------------------------------------- Method goToLogin 
			self::_goTo( self::$_login );
		}//<<-------------------------------------------------------------- End Method goToLogin
		
		/*
	     * Redirige a la página de Login
	     * @access    public static
	     */
		public static function goToLoginAjax( $parent = FALSE )
		{//<<-------------------------------------------------------------- goToLoginAjax()
		  /* 
                  if($parent)
		      return "<script type='text/javascript'>\$(document).ready(function(){ alert('Se ha detectado inactividad en el sistema y su sesión ha caducado. Por motivos de seguridad se recomienda que vuelva a iniciar sesión.');      window.parent.location.href = '../../vistasDinamicas/LogOut/logOut.php'; });</script>";
		   else
		      return "<script type='text/javascript'>\$(document).ready(function(){ alert('Se ha detectado inactividad en el sistema y su sesión ha caducado. Por motivos de seguridad se recomienda que vuelva a iniciar sesión.');      window.location.href = '../../vistasDinamicas/LogOut/logOut.php'; });</script>";
		*/
                }//<<-------------------------------------------------------------- End goToLoginAjax()
		
	/*
	* Redirige a la página principal
	* @access    public static
	*/
		public static function goHome()
		{//<<-------------------------------------------------------------- Method goHome  
			
			$home = ROOT_DNS.'/Home/';
			
			if( basename($_SERVER['SCRIPT_FILENAME']) == basename($home) ){
			    $home = ROOT_DNS.'/Logout/';
            }
			
			self::_goTo( $home );
			
		}//<<-------------------------------------------------------------- End Method goHome
        
		
	/*
	* Redirección desde cabeceras
	* @access    private static
	* @param	 String link->URL destino
	*/
		private static function _goTo( $link = '' )
		{//<<-------------------------------------------------------------- Method _goTo  
			header("Location: {$link}");
		}//<<-------------------------------------------------------------- End Method _goTo
		
		
	/*
	* Obtiene la IP de acceso
	* @access    private static
	* @param	 none
	*/
	
		public static function getRealIP()
		{//<<-------------------------------------------------------------- Method getRealIP  
			if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
			{
				self::$_realIP = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");
				$entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
				reset($entries);
				while (list(, $entry) = each($entries))
				{
					$entry = trim($entry);
					if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
					{
						$private_ip = array(
							'/^0\./',
							'/^127\.0\.0\.1/',
							'/^192\.168\..*/',
							'/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
							'/^10\..*/'
						);
						$found_ip = preg_replace($private_ip, self::$_realIP, $ip_list[1]);
						if (self::$_realIP != $found_ip)
						{
						   self::$_realIP = $found_ip;
						   break;
						}
					}
				}
			}else
			{
				self::$_realIP = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown" );
			}
			return(self::$_realIP);
			
		}//<<-------------------------------------------------------------- End Method getRealIP
		
  
  }//---------------------------------------------------------------------------------->>> End Class Session

?>
