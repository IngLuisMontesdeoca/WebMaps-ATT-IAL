<?php

    /****************************************************************
    *                                                               *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>       *
    *   @version:       2.0                                         *
    *   @since          30/01/2009                                  *
    *   @modified       01/04/2009                                  *
    *   @copiright:     Copyright (c) 2009, SkyTel                  *
    *   @link:          http://localhost/Web2.0 Go                  *
    *   @description    Clase para realizar el caché de scripts PHP *
    *                                                               *
    *   @modifications  Se adjunta la posibilidad de cachear partes *
    *                   de código en específico.                    * 
    *                                                               *
    *****************************************************************/
    
           
  class Cache 
  {//---------------------------------------------------------------------------------->>> Class Cache
  
    /*
     * private  String
     * desc     Path ó ruta donde se almacenará el cache
     */
        private $_cacheDir = '';
        
    /*
     * private  String
     * desc     Tiempo en segundos en que expira la cache
     */  
        private $_cacheTime = '';
        
    /*
     * private  Boolean
     * desc     Bandera que determina si se realizará el cache
     */
        private $_caching = false;
        
    /*
     * private  Boolean
     * desc     Bandera que determina si se limpiará y actualizará el cache
     */
        private $_cleaning = false;
        
    /*
     * private  Boolean
     * desc     Bandera que determina si se obtendrá el contenido del archivo 
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
     * @descrip  Setea el tiempo que durará el caching y la ruta de almacenamiento del mismo.  
     * @params   time->Tiempo de expiración del caching en segundos.
     *                 60 seg por defaul.
     *           path->Ruta del directorio donde se almacenarán los archivos del caché.
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
     * @descrip  Inicia el caché de un script a partir de parámetros.   
     * @params   action->Booleano que determinará si se actualizará el caché.            
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

            //condicional: Existencia del archivo, fecha expiración, acción
            if( ( $exist === true ) && ( $fileTime > $actualTime ) && ( $this->_cleaning === false ) ) {//<<---------------- if condition

                if( $this->_getFile === false ) {
                    
                    //Lee el archivo, lo imprime en el buffer de salida y termina la ejecución del script
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
                
                //Recuperar información del buffer
                $data = ob_get_clean();
                
                //Determinar si hay salida a pantalla
                if( $this->_getFile === false ) { 
                    echo $data;
                }
                
                //Borrar cache si existe
                if( file_exists($this->_file) ) {
                    unlink($this->_file);
                }
                
                //Escribir información en cache
                $cacheFile = fopen( $this->_file, 'w' );
                fwrite( $cacheFile , $data );
                fclose( $cacheFile );
                
            }else{//<<---------------- else _caching  
                ob_end_clean(); 
            }//<<---------------- End if _caching
            
        }//<<-------------------------------------------------------------- End close()
    

  }//---------------------------------------------------------------------------------->>> End Class Cache
  
?>