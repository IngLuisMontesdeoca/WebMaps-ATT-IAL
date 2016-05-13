<?php

    /****************************************************************
    *                                                               *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>       *
    *   @version:       1.0                                         *
    *    @created       20/03/2009                                 *
    *   @copiright:     Copyright (c) 2009, SkyTel                  *
    *   @link:          http://localhost/Web2.0/include/DB   Go     *
    *   @description    Clase que permite realizar conexiones a BD  *
    *                   externas.                                  *
    *                                                               *
    *****************************************************************/
    
    //---- REQUIRES ----//
        //Clase Base         
            require_once 'Base.class.php';

  abstract class Base_External extends Base
  {//---------------------------------------------------------------------------------->>> Class Base_External
  
          
  //---- Parámetros de Conexión ----//
    
    /*
     * private static String
     * desc        Usuario
     */
        private static $_user = '';
        
    /*
     * private static String
     * desc        Contraseña
     */        
        private static $_pass = '';
        
    /*
     * private static String
     * desc        Host
     */            
        private static $_host = '';
        
    /*
     * private static String
     * desc        Base de Datos
     */            
        private static $_db = '';
        
   /*
    * Constructor 
    * @access   protected
    * @params   user->usuario
    *           pass->password
    *           host->host
    *           db->base de datos
    */        
        protected function __construct( $user = '', $pass = '', $host = '', $db = '' )
        {//<<-------------------------------------------------------------- __construct()
            self::$_user = $user;
            self::$_pass = $pass;
            self::$_host = $host;
            self::$_db = $db;
        }//<<-------------------------------------------------------------- End __construct()


   /*
    * Setea el parámetro de conexión Usuario 
    * @access   public
    * @params   user->usuario
    */            
        public function setUser( $user = '' )
        {//<<-------------------------------------------------------------- setUser()
            if( empty($user) ){
                exit('Ingrese Nombre de Usuario');    
            }
            
            self::$_user=$user;        
        }//<<-------------------------------------------------------------- End setUser()
        

   /*
    * Setea el parámetro de conexión Pass 
    * @access   public
    * @params   pass->password
    */        
        public function setPass( $pass = '' )
        {//<<-------------------------------------------------------------- setPass()
            if( empty($pass) ){
                exit('Ingrese Password');    
            }
            
            self::$_pass = $pass;        
        }//<<-------------------------------------------------------------- End setPass()
        
        
   /*
    * Setea el parámetro de conexión Host
    * @access   public
    * @params   host->URL del host a utilizar
    */        
        public function setHost( $host = '' )
        {//<<-------------------------------------------------------------- setHost()
            if( empty($host) ){
                exit('Ingrese Host');    
            }
            
            self::$_host = $host;
        }//<<-------------------------------------------------------------- End setHost()
        

   /*
    * Setea el parámetro de conexión Base de Datos
    * @access   public
    * @params   db->Nombre de la Base de Datos
    */        
        public function setBD( $db = '' )
        {//<<-------------------------------------------------------------- setBD()
            if( empty($db) ){
                exit('Ingrese Base de Datos');    
            }
            
            self::$_db = $db;
        }//<<-------------------------------------------------------------- End setBD()
        
        
    /*
     * Setea el tipo de Base de Datos a utilizar y realiza la conexión correspondiente.
     * @access    protected
     * @param     dbType->Tipo de Base de Datos a utilizar. MySql por defecto.
     * @method    this::externalConnection(), ADOdb::debug()            
     */            
         public function externalConnect( $dbType='mysql') 
        {//<<-------------------------------------------------------------- Method externalConnect
            
            if( empty(self::$_user) || empty(self::$_pass) || empty(self::$_host) || empty(self::$_db)){
                exit("Falta un par&aacute;metro de conexi&oacute;n: <br />
                      User: {self::$_user} <br />
                      Pass: {self::$_pass} <br />
                      Host: {self::$_host} <br />
                      BD: {self::$_db}");
            }
            
            $this->dbType = $dbType;
            $this->baseDb = self::_externalConnection($dbType);
            
        }//<<-------------------------------------------------------------- End Method externalConnect
        
            
    /*
     * Data Base Object Factory.
     * @access   private static
     * @param    dbType->Tipo de Base de Datos a utilizar.
     */            
        private static function _externalConnection( $dbType )
        {//<<-------------------------------------------------------------- Method externalConnection
                        
            switch( $dbType ) 
            {//<<---------------- switch dbType
                case 'postgres8':
                    $Object = new DataConnectionPostgres( self::$_user, self::$_pass, self::$_host, self::$_db );
                break;
                
                case 'mysql':
                    $Object = new DataConnectionMySQL( self::$_user, self::$_pass, self::$_host, self::$_db );
                break;
                
                default:
                    $Object = false;
                break;    
            }//<<---------------- switch dbType
            
            return $Object;
        }//<<-------------------------------------------------------------- End Method externalConnection
        
        
    /*
     * Setea la Tabla a trabajar
     * @access    public
     * @param    table->Tabla con la que se trabajará
     * @methods    parent::setTable()            
     */    
         public function useTable( $table = '')
        {//<<-------------------------------------------------------------- Method useTable
            if( empty($table) ){
                exit('Ingrese Tabla');
            }
            
            $this->setTable($table);
        }//<<-------------------------------------------------------------- End Method useTable
        

    /*
     * Obtener resultados de una Sentencia SQL
     * @access    public
     * @param     sql->Sentencia SQL a ejecutar
     * @methods   parent::_execute()            
     */    
        public function getResultsFrom( $sql = '' )
        {//<<-------------------------------------------------------------- Method getResultsFrom
            $querySQL = str_replace('{table}',$this->_baseTbl, $sql);
            $this->_querySQL = $querySQL;
            $this->_execute();
        }//<<-------------------------------------------------------------- End Method getResultsFrom
        
  
  }//---------------------------------------------------------------------------------->>> End Class Base_External
  
  
?>  