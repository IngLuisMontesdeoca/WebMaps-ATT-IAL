<?php

    /************************************************************************
    *                                                                   	*
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>           	*
    *   @version:       1.0                                             	*
    *   @created        17/03/2006                                      	*
    *   @copiright:     Copyright (c) 2009, SkyTel                      	*
    *   @link:          http://localhost/skyWeb/include/classes/Packages Go	*
    *   @description    Clase que permite importar paquetes al Sistema  	*
    *                                                                   	*
    ************************************************************************/
    
	//---- REQUIRE ----//
		//Autoload
			require_once ROOT_FUNC.'/autoLoad.function.php';
    
  abstract class Package 
  {//<<---------------------------------------------------- Class Package

      
	  /*
	  * @access			private static
	  *	@type			Array
	  *	@description	Arreglo de Paquetes Generales
	  */
	  private static $_inUse = array('ADODB'=>false,
									 'BD'=>false,
									 'Classes'=>false,
									 'Functions'=>false,
									 'Define'=>false,
									 'Sockets'=>false);
	  
	  
	  /*
      * usePackage()
      * @access         public static
      * @description    Permite importar Paquetes Generales en el Sistema.
      * @params         String package->Nombre del Paquete a importar
      */
        public static function usePackage( $package = '')
        {//<<------------------------------------------- usePackage()
            
            $package = strtoupper($package);
			
			switch( $package ) {//<<---------------- Switch package

			    //AdoDB
			    case 'ADODB':
                    $adoDbDrivers = ROOT_ADODB.'/drivers';
                    $newPath = PATH_SEPARATOR.ROOT_ADODB.PATH_SEPARATOR.$adoDbDrivers;
					$pack = 'ADODB';
			    break;

			    //BD
			    case 'BD':
					$BDMotors = ROOT_BD.'/motors'; 
				    $newPath = PATH_SEPARATOR.ROOT_BD.PATH_SEPARATOR.$BDMotors;
					$pack = 'BD';
			    break;   

			    //Classes
			    case 'CLASSES':
				    $newPath = PATH_SEPARATOR.ROOT_CLASS;
					$pack = 'Classes';
			    break;                
                
			    //Funciones
			    case 'FUNCTIONS':
                     $newPath = PATH_SEPARATOR.ROOT_FUNC;
					 $pack = 'Functions';
                break;

                //Definiciones
                case 'DEFINE':
                     $newPath = PATH_SEPARATOR.ROOT_DEF;
					 $pack = 'Define';
                break;
				
				//Sockets
                case 'SOCKETS':
                     $newPath = PATH_SEPARATOR.ROOT_SOCK;
					 $pack = 'Sockets';
                break;

			    default:
				    exit("El Paquete General {$package} no existe");
			    break;

		    }//<<---------------- End Switch package
            
            
            self::_setIncludePath( $newPath, $pack );

	    }//<<------------------------------------------- End usePackage()
		


      /*
      * import()
      * @access         public static
      * @description    Permite importar Paquetes Espec�ficos en el Sistema.
      * @params         String package->Nombre del Paquete a importar
      */
        public static function import( $package = '')
        {//<<------------------------------------------- import()
            
            $package = strtoupper($package);
			
			switch( $package ) {//<<---------------- Switch package
				
				//---- BD ----//
					//External
					case 'EXTERNAL':
						self::_isSetPack('BD', 'External');
						$BDExternal = ROOT_BD.'/external';
						$newPath = PATH_SEPARATOR.$BDExternal; 
					break;
				
					//Tables
					case 'TABLES':
						self::_isSetPack('BD', 'Tables');
						$BDTables = ROOT_BD.'/tables';
						$newPath = PATH_SEPARATOR.$BDTables; 
					break;
					
					//Views
					case 'VIEWS':
						self::_isSetPack('BD', 'Views');
						$BDViews = ROOT_BD.'/views';
						$newPath = PATH_SEPARATOR.$BDViews; 
					break;
					
					//Pages
					case 'PAGES':
						self::_isSetPack('BD', 'Pages');
						$BDPages = ROOT_BD.'/pages';
						$newPath = PATH_SEPARATOR.$BDPages;
					break;    
				
				//---- Classes ----//
					//Formulario
					case 'FORM':
						self::_isSetPack('Classes', 'Form');
						$newPath = PATH_SEPARATOR.ROOT_CLASS.'/Form';
					break;
					
					//Debug
					case 'DEBUG':
						self::_isSetPack('Classes', 'Debug');
						$newPath = PATH_SEPARATOR.ROOT_CLASS.'/Debug';
					break;
									
					//Template
					case 'TEMPLATE':
						 self::_isSetPack('Classes', 'Template');
						 $newPath = PATH_SEPARATOR.ROOT_CLASS.'/Template';
					break;
					
					//Utilities
					case 'UTILITIES':
						 self::_isSetPack('Classes', 'Utilities');
						 $newPath = PATH_SEPARATOR.ROOT_CLASS.'/Utilities';
					break;
					
					//ParlayX
					case 'PARLAYX':
						 self::_isSetPack('Classes', 'Parlayx');
						 $newPath = PATH_SEPARATOR.ROOT_CLASS.'/Parlayx';
					break;
                                    
					//WS
					case 'WS':
						 self::_isSetPack('Classes', 'ws');
						 $newPath = PATH_SEPARATOR.ROOT_CLASS.'/Parlayx/ws';
					break;
					
					//Cadenas
					case 'CADENAS':
						 self::_isSetPack('Classes', 'Cadenas');
						 $newPath = PATH_SEPARATOR.ROOT_CLASS.'/Cadenas';
					break;
				
				//---- Sockets ----//
					//Interfaces
					case 'INTERFACES_SOCKET':
						 self::_isSetPack('Sockets', 'Interfaces');
						 $newPath = PATH_SEPARATOR.ROOT_SOCK.'/interfaces';
					break;
				
					//Servers
					case 'SERVERS':
						 self::_isSetPack('Sockets', 'Servers');
						 $newPath = PATH_SEPARATOR.ROOT_SOCK.'/servers';
					break;
					
					//Commands
					case 'COMMANDS':
						 self::_isSetPack('Sockets', 'Commands');
						 $newPath = PATH_SEPARATOR.ROOT_SOCK.'/commands';
					break;
					
				
			    default:
				    exit("El Paquete de Importaci&oacute;n {$package} no existe");
			    break;

		    }//<<---------------- End Switch package
            
            
            self::_setIncludePath( $newPath, '' );

	    }//<<------------------------------------------- End import()		
        

    /*
     * _isSetPack() 
     * @access          private static
     * @description     Eval�a si se est� utilizando el paquete General del cual depende
	 *					un paquete de importaci�n
	 * @params			String pack->nombre del Paquete General
	 *					String subPack->nombre del SubPaquete que se desea utilizar
     */
        private static function _isSetPack( $pack = '', $subPack = '' )
        {//<<------------------------------------------- _isSetPack()       
            
			if( self::$_inUse[$pack] == false ) {
				exit("Debe usar el paquete {$pack} antes de importar {$subPack}");
			}
             
        }//<<------------------------------------------- End _isSetPack()



     
    /*
     * _setIncludePath() 
     * @access          private static
     * @description     Setea un path en las rutas de inicio del Script en ejecuci�n
     * returns          boolean
     */
        private static function _setIncludePath( $newPath, $pack )
        {//<<------------------------------------------- _setIncludePath()       
            
            $actualPath = get_include_path();
            
            if( !ereg( $newPath, $actualPath) ) {
			
                set_include_path( "{$actualPath}{$newPath}" );
				if( !empty($pack) ){
					self::$_inUse[$pack] = true;
				}
				
                return true;
				
            } else {
                return false;
            }
             
        }//<<------------------------------------------- End _setIncludePath()   
                
        
  }//<<---------------------------------------------------- End Class Package
  

?>