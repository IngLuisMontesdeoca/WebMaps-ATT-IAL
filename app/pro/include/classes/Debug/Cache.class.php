<?php

    /****************************************************************
    *                                                               *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>       *
    *   @version:       2.0                                         *
    *   @since          30/01/2009                                  *
    *   @modified       01/04/2009                                  *
    *   @copiright:     Copyright (c) 2009, SkyTel                  *
    *   @link:          http://localhost/Web2.0 Go                  *
    *   @description    Clase para realizar el cach� de scripts PHP *
    *                                                               *
    *   @modifications  Se adjunta la posibilidad de cachear partes *
    *                   de c�digo en espec�fico.                    * 
    *                                                               *
    *****************************************************************/
    
           
  class Cache 
  {//---------------------------------------------------------------------------------->>> Class Cache
  
    /*
     * private  String
     * desc     Path � ruta donde se almacenar� el cache
     */
        private $_cacheDir = '';
        
    /*
     * private  String
     * desc     Tiempo en segundos en que expira la cache
     */  
        private $_cacheTime = '';
        
    /*
     * private  Boolean
     * desc     Bandera que determina si se realizar� el cache
     */
        private $_caching = false;
        
    /*
     * private  Boolean
     * desc     Bandera que determina si se limpiar� y actualizar� el cache
     */
        private $_cleaning = false;
        
    /*
     * private  Boolean
     * desc     Bandera que determina si se obtendr� el contenido del archivo 
     *          en vez de mostrarlo en pantalla
     */
        private $_getFile = false; 
  
        
    /*
     * private  String
     * desc     Path o ruta del script a cachear
     */
        private $_file = '';
        

    /*
     * Constructor  
     * @descrip  Setea el tiempo que durar� el caching y la ruta de almacenamiento del mismo.  
     * @params   time->Tiempo de expiraci�n del caching en segundos.
     *                 60 seg por defaul.
     *           path->Ruta del directorio donde se almacenar�n los archivos del cach�.
     *                 Directorio tmp por default.
     */
        function __construct( $time = 60, $getFile = false, $path = '' )
        {//<<-------------------------------------------------------------- Constructor
         
            $this->_cacheDir = ( empty($path) ) ? ROOT_CACHE : $path;
            $this->_cacheTime = $time;
            $this->_getFile = $getFile;
			$this->_cacheDir = ( $this->_getFile === true ) ? ROOT_CACHE.'/parts' : $this->_cacheDir;
        
        }//<<-------------------------------------------------------------- End Constructor
             
        
    /*
     * start()
     * @access    public
     * @descrip  Inicia el cach� de un script a partir de par�metros.   
     * @params   action->Booleano que determinar� si se actualizar� el cach�.            
     */
        public function start( $fileName = '', $action = false )
        {//<<-------------------------------------------------------------- start()
            
            $rootCache = "{$this->_cacheDir}/cache_";
            $encriptURI = ( empty($fileName) ) ? md5(urlencode($_SERVER['REQUEST_URI'])) : md5(urlencode($fileName)); 
            $this->_cleaning = $action;
            $this->_file = "{$rootCache}{$encriptURI}";
            
            $exist = file_exists( $this->_file );
            $fileTime = ( $exist === true ) ? ( (fileatime($this->_file)) + $this->_cacheTime ) : 0;
            $actualTime = time();

            //condicional: Existencia del archivo, fecha expiraci�n, acci�n
            if( ( $exist === true ) && ( $fileTime > $actualTime ) && ( $this->_cleaning === false ) ) {//<<---------------- if condition

                if( $this->_getFile === false ) {
                    
                    //Lee el archivo, lo imprime en el buffer de salida y termina la ejecuci�n del script
                    readfile( $this->_file );
                    exit();
                    
                } else {
                    $this->_caching = false;   
                }
                
            
            } else {//<<---------------- else condition
                $this->_caching = true;
            }//<<---------------- End if condition
            
            //Inicio de buffer de salida para caching del script
            ob_start();
            
        }//<<-------------------------------------------------------------- End start()
        
        
    /*
     * getCachingFile()
     * @access   public
     * @descrip  Obtiene el contenido de un archivo y lo devuelve en forma de cadena.
     * @returns  String cachingOutput            
     */
        public function getCachingFile()
        {//<<-------------------------------------------------------------- getFile()
            $cachingOutPut = ( $this->_getFile === true ) ? file_get_contents( $this->_file ) : '';
            return $cachingOutPut;                 
        }//<<-------------------------------------------------------------- End getFile()        

        
    /*
     * close()
     * @access    public
     * @descrip  Detiene el caching del script y muestra el contenido en el explorador.            
     */
        public function close()
        {//<<-------------------------------------------------------------- close()
            
            if ( $this->_caching === true ) {//<<---------------- if _caching
                
                //Recuperar informaci�n del buffer
                $data = ob_get_clean();
                
                //Determinar si hay salida a pantalla
                if( $this->_getFile === false ) { 
                    echo $data;
                }
                
                //Borrar cache si existe
                if( file_exists($this->_file) ) {
                    unlink($this->_file);
                }
                
                //Escribir informaci�n en cache
                $cacheFile = fopen( $this->_file, 'w' );
                fwrite( $cacheFile , $data );
                fclose( $cacheFile );
                
            }else{//<<---------------- else _caching  
                ob_end_clean(); 
            }//<<---------------- End if _caching
            
        }//<<-------------------------------------------------------------- End close()
    

  }//---------------------------------------------------------------------------------->>> End Class Cache
  
?>