<?php

	/*****************************************************************
    *                                                           	 *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   	 *
    *   @version:       4.0                                     	 *
	*	since			12/07/2006									 *
	*	modified		17/03/2009									 *
    *   @copiright:     Copyright (c) 2009, SkyTel              	 *
    *   @link:          http://localhost/Polarix/include/DB   Go     *
    *   @description    Clase Abstracta que a�ade las funcionalida-	 *
	*					des CRUD b�sicas de ADOdb.					 *
	*																 *
	*	notes			Se incluye la funcionalidad de Paquetes, pa- *
    *                   tr�n Singleton para instancias de Objetos de *
    *                   Conexi�n a Bases de Datos, constructor y des-*
    *                   tructor para clases hijas y desparametriza-  *
    *                   ci�n del m�todo save()                       *
	*																 *
    *****************************************************************/

	//---- PAQUETES ----//
        //AdoDB
            Package::usePackage('ADODB');
	
  abstract class Base
  {//---------------------------------------------------------------------------------->>> Class Base

	/*
	 * public   String
	 * desc		Cadena que describe el Motor de Base de Datos
	 */
		public $dbType = '';

	/*
	 * public 	Object
	 * desc		Objeto de Conexi�n a Base de Datos ADOdb
	 */
		public $baseDb = null;

	/*
	 * protected String
	 * desc		 Tabla con la que se trabajar�
	 */
		protected $_baseTbl = '';

	/*
     * private  String
     * desc     Nombre del campo llave de la tabla
     */
        protected $_primaryKey = '';

    /*
	 * protected int
	 * desc		 Valor de la llave primaria de la tabla
	 */
		protected $_pk = 0;

	/*
	 * private 	Object
	 * desc		Contendr� la descripci�n de la tabla
	 */
		private $_meta = null;

	/*
	 * public   Object
	 * desc     Objeto utilizado por ADObd en el cual se almacenan resultados de consultas
	 */
		public $baseRs = null;

	/*
	 * protected String
	 * desc		Query SQL a ejecutar
	 */
		protected $_querySQL = '';
        
    /*
     * private static Object
     * desc     Instancia de ADObd para patr�n Singleton
     */        
        private static $_instance = null;

    /*
    * Constructor 
    * @access   protected
    * @params   tableName->Nombre de la Tabla a utilizar
    *           id->id del Registro a cargar
    *           dbType->Motor de Base de Datos a Usar. MySQL por default
    * @methods  this::setAndConnect(), this::setTable(), this::setPk()
    */
	    protected function __construct( $tableName = '', $id = 0 , $dbType='mysql' )
        {//<<-------------------------------------------------------------- __construct()
            $this->setAndConnect( $dbType );
            $this->setTable( $tableName );
            $this->setPk( $id );
        }//<<-------------------------------------------------------------- End __construct()
        
    
    /*
	 * Setea el tipo de Base de Datos a utilizar y realiza la conexi�n correspondiente.
	 * @access	protected
	 * @params	dbType->Tipo de Base de Datos a utilizar. MySql por defecto.
	 *			debug->Especifica si se realizar� el debug de la consulta de definiciones
     *          de campos de la tabla. Falso por default
 	 * method	this::connection(), ADOdb::debug()
	 */
	 	protected function setAndConnect( $dbType='mysql', $debug = false )
		{//<<-------------------------------------------------------------- Method setAndConnect

            $this->dbType = $dbType;
            
            //Patr�n Singleton para Objetos de Conexi�n a Bases de Datos
            self::$_instance = ( empty( self::$_instance ) ) ? self::connection($dbType) : self::$_instance;
            $this->baseDb = self::$_instance;   

			if( $debug === true ) {
				$this->debug();
			}

		}//<<-------------------------------------------------------------- End Method setAndConnect


	/*
	 * Data Base Object Factory.
	 * @access	private static
	 * @param	dbType->Tipo de Base de Datos a utilizar.
	 */
		protected static function connection( $dbType )
		{//<<-------------------------------------------------------------- Method connection
            
            switch( $dbType )
            {//<<---------------- switch dbType
				case 'postgres8':
					$Object = new DataConnectionPostgres();
				break;

				case 'mysql':
					$Object = new DataConnectionMySQL();
				break;

				default:
					$Object = null;
				break;
			}//<<---------------- switch dbType

			return $Object;
		}//<<-------------------------------------------------------------- End Method connection


	/*
	 * Setea la Tabla a trabajar y conecta a la base de datos
	 * @access	protected
	 * @param	tbl->Tabla con la que se trabajar�
 	 * methods	this::_setProps()
	 */
	 	protected function setTable( $tbl = '')
		{//<<-------------------------------------------------------------- Method setTable
			$this->_baseTbl = $tbl;
			$this->_setProps();
		}//<<-------------------------------------------------------------- Method End setTable


	/*
	 * Asigna valor al atributo Llave Primaria
	 * @access	public
	 * @param	$pk->String que determina la llave primaria de la tabla
	 */
		public function setPk( $pk = 0 )
        {//<<-------------------------------------------------------------- Method setPk

            $this->_pk = $pk;

            /*
             * Si la llave primaria es un n�mero y es indistinta de 0,
             * cargamos el contenido del registro correspondiente en los
             * atriburos del V0
             */
			if( ( is_numeric($this->_pk) ) && ( $this->_pk != 0 ) ) {
				$this->_load();
			}

		}//<<-------------------------------------------------------------- End Method setPk


	/*
	 * Obtiene el valor del atributo Llave Primaria
	 * @access	public
	 */
		public function getPk()
        {//<<-------------------------------------------------------------- Method getPk
			return $this->_pk;
		}//<<-------------------------------------------------------------- End Method getPk


	/*
	 * Asigna los campos de la tabla a trabajar como valores del atributo _meta
	 * y construye internamente el VO de la clase que extienda a Base
	 * @access	private
	 * methods	ADOdb::MetaColumns
	 */
		private function _setProps()
		{//<<-------------------------------------------------------------- Method _setProps

			if( empty($this->_baseTbl) ) {
                exit('Especifica la tabla a trabajar');
            }

			//Obtener informaci�n de las Columnas
			$cols = $this->baseDb->MetaColumns($this->_baseTbl, false);

			if(!is_array($cols))
			   return;
			//Asignaci�n de campos al atributo _meta
			foreach ( $cols as $index => $value ) {//<<---------------- Asigna _meta

				$fieldName = $cols[$index]->name;
                $isPrimaryKey = $cols[$index]->primary_key;

				//Asignaci�n del campo Llave Primaria
                if( $isPrimaryKey===true ) {//<<---------------- if isPrimaryKey
                    
					//Auto Incrementos para Postgres y MySQL
                    switch( $this->dbType ) {//<<---------------- switch dbType
                    
                        //Postgres
                        case 'postgres8':
                            
                            $defaultValue = $cols[$index]->default_value;
                            $autoIncrement = "nextval('{$this->_baseTbl}_{$fieldName}_seq'::regclass)";
                            
                            if( $defaultValue === $autoIncrement  ) {
                                $this->_primaryKey = $fieldName;
                            }
                            
                        break;
                        
                        //MySQL
                        case 'mysql':
                        
                            $isAutoIncrement = $cols[$index]->auto_increment;
                            if( $isAutoIncrement === true ) { 
                                $this->_primaryKey = $fieldName;
                            }
                        
                        break;
                        
                        default:
                            $this->_primaryKey = $fieldName;
                        break;
                        
                    }//<<---------------- End switch dbType
                    
				}//<<---------------- End if isPrimaryKey

				$this->_meta->$fieldName = $value->type;

				//Crea la propiedad del VO
				$this->$fieldName = '';

			}//<<---------------- End Asigna _meta


		 }//<<-------------------------------------------------------------- End Method _setProps



	/*
	 * Obtiene las propiedades de la clase extra�das de la tabla y las devuelve en un objeto
	 * @access	private
	 */
		private function _getProps()
		{//<<-------------------------------------------------------------- Method _getProps

			//Arreglo que contiene las propedades de la clase
			$properties = get_object_vars($this);

			//---- Excepciones ----//
				unset($properties['_baseTbl']);
				unset($properties['dbType']);
				unset($properties['_primaryKey']);
				unset($properties['baseDb']);
				unset($properties['_pk']);
				unset($properties['baseRs']);
				unset($properties['_querySQL']);
				unset($properties['_meta']);
			//---------------------//

			return $properties;

		}//<<-------------------------------------------------------------- End Method _getProps


	/*
	 * Retorna un valor a partir del tipo de dato que se trate y de su contenido
	 * @access	private
	 * @params	index->Indice del atributo _meta
     *          value->Contenido a examinar
	 */
		private function _format( $index, $value )
		{//<<-------------------------------------------------------------- Method _format

			switch( $this->_meta->$index ) {//<<---------------- Switch index

			  //Enteros
				case 'int':
                case 'int2':
				case 'int4':
				case 'int8':
					return (empty($value))?0:$value;
				break;

			 //Fecha
				case 'date':
				case 'time':
					return (empty($value))?'null':"'{$value}'";
				break;

			 //Cadenas y otros tipos
				default:
					return "'{$value}'";
				break;

			}//<<---------------- End Switch index

		}//<<-------------------------------------------------------------- End Method _format


	/*
	 * Establece nuevas propiedades p�blicas a partir de un array dado. 
     * Util para recibir variables desde $_POST o $_GET
	 * @access	public
	 * @params	array->Arreglo que contiene los valores a asignar.
	 */
		public function setFromArray( $array )
		{//<<--------------------------------------------------------------  Method format

			if( !is_array($array) ) {//<<---------------- If !is_array
				return false;
			}else{//<<---------------- Else !is_array

				//Asigna valores
				foreach($array as $index => $value) {
					if(isset($this->$index)) {
						 $this->$index = $value;
						 //echo "{$this->index} = {$value}", SALTO;
					}
				}

				return true;

			}//<<---------------- End If !is_array

		}//<<-------------------------------------------------------------- End Method _format


	/*
	 * Obtiene todos los datos de un registro de la Tabla a partir de la llave primaria
	 * @access	private
	 * @params	orderby->Campo por el que se ordenar� la consulta. Llave primaria por default
	 * @methods	this::_execute(), this::next()
	 */
		private function _load( $orderby = '' )
		{//<<-------------------------------------------------------------- Method _load

			//S�lo se efectuar� la consulta si existe llave primaria
			if( empty($this->_pk) ) {//<<---------------- If empty _pk

				return false;

			}else{//<<---------------- Else empty _pk

				$orderby =( empty($orderby) ) ? $this->_primaryKey : $orderby;

				$this->_querySQL = "SELECT * FROM {$this->_baseTbl} WHERE {$this->_primaryKey} = {$this->_pk} ORDER BY {$orderby} ";

				if( !($this->_execute($this->_querySQL)) ) {
					return false;
				} else {
                    $this->next();
                }

			}//<<---------------- End If empty _pk

		}//<<-------------------------------------------------------------- End Method _load


	/*
	 * Obtiene todos los registros de la tabla con la que se est� trabajando ordenados por la llave primaria de la misma
	 * @access	public
	 * @params	orderby->Campo por el que se ordenar� la consulta. Llave primaria por default
	 * @methods	this::_execute()
	 */
		public function loadAll( $orderby = false )
		{//<<-------------------------------------------------------------- Method _loadAll

			$orderby = (!$orderby)? $this->_primaryKey:$orderby;

			$this->_querySQL = "SELECT * FROM {$this->_baseTbl} ORDER BY {$orderby} ";

			if( !($this->_execute($this->_querySQL)) ) {
				echo $this->debug();
				return false;
			}

			return true;
		}//<<-------------------------------------------------------------- End Method _loadAll


	/*
	 * Busca en la tabla con los filtros dados en $array y regresa el primero. Hace busqueda and por default (definido en $type)
	 * @access	public
	 * @params	orderby->Campo por el que se ordenar� la consulta. Llave primaria por default
	 * @methods	this::_execute(), this::setFromArray(), ADOdb::FetchRow()
	 */
		public function loadIf( $array=array(), $type = 'AND', $orderby = '' )
		{//<<-------------------------------------------------------------- Method _loadIf
			$cond = array();
			$cond[] = '1 = 1';
			$orderby = ( empty($orderby) ) ? $this->_primaryKey : $orderby;

			//S�lo se har� la consulta si el parametro es un arreglo
			if( !is_array($array) ) {
				return false;
			}

			//Construye las condiciones
			foreach( $array as $index => $value ) {

				//S�lo consideramos las propiedades establecidas en la clase
				if( !isset($this->$index) ) {
					continue;
				}

				array_push( $cond, "{$index} = {$value}" );
			}

			$conditions = implode( " {$type} ", $cond);
            
            //Construye sentencia SQL
			$this->_querySQL = "SELECT * FROM {$this->_baseTbl} WHERE {$conditions} ORDER BY {$orderby} ";

			return $this->_execute($this->_querySQL);
		}//<<-------------------------------------------------------------- End Method loadIf



	/*
	 * Obtiene le n�mero de registros encontrados en una consulta SELECT
	 * @access	public
	 * @methods	ADODB::_numOfRows()
	 */
		public function numRows()
        {//<<-------------------------------------------------------------- Method numRows
			return $this->baseRs->_numOfRows;
		}//<<-------------------------------------------------------------- End Method numRows



	/*
	 * Traslada los resultados del objeto baseRs de ADOdb a las variables del VO interno
	 * @access	public
	 * @methods ADOdb::FetchRow(), this::setFromArray()
	 */
		public function next()
		{//<<-------------------------------------------------------------- Method next
		  if( $this->baseRs ) {
			   $rows = $this->baseRs->FetchRow();
			   $vectorRows = (!$rows) ? false : $this->setFromArray($rows);
			   return  $vectorRows;
		  }
		}//<<-------------------------------------------------------------- End Method next



	/*
	 * Realiza la sentencia UPDATE o INSERT dependiendo de si se estableci� una llave primaria o no
	 * @access	public
	 * @params	format->Define si se procesaran las cadenas
	 * @methods	this::_getPorps(), this::_getUpdateSQL(), this::_getInsertSQL(), this::_execute()
     *          this::_logSaveErrors()
	 */
		public function save( $format = false )
		{//<<-------------------------------------------------------------- Method save

			//Arreglo con las propiedades de la tabla
			$prop = $this->_getProps();

            //Bandera de ejecuci�n de consultas SQL
            $do = false;

            //Bandera que indica si se realizar� un INSERT o un UPDATE
			$insert = ( $this->_pk!=0 ) ? false : true;
            
            //Acci�n a realizar dependiendo del valor de la llave primaria
            $this->_querySQL = ( $this->_pk!=0 ) ? $this->_getUpdateSQL( $prop, $format ) : $this->_getInsertSQL( $prop, $format );
			//echo($this->_querySQL);
			//---------- Ejecuta sentencia SQL ----------//
				$do = $this->_execute($this->_querySQL);
				//echo $this->_querySQL;
			//-------------------------------------------//

			if( $do === false ) {//<<---------------- If !do
                
                //---------- Log de Errores ----------//  
                    $this->_logSaveErrors();
                //------------------------------------//

			} else {//<<---------------- Else !do

                //Obtener el id reci�n ingresado si se realiz� un INSERT
                if( $insert === true ) {
                    $this->_pk = $this->getLastId();
                }

                //Comprobaci�n
                $this->_load();

            }//<<---------------- End If !do


			return $do;

		}//<<-------------------------------------------------------------- End Method save


    /*
     * Genera consultas UPDATE din�micamente.
     * @access    private
     * @params    Array fields->Arreglo con los campos a editar
     *            boolean escape->Determina si se escapar�n los contenidos de los campos
     * @methods   classes.Escapa::escaparFuncion() 
     */
        private function _getUpdateSQL( $fields = array(), $escape = false )
        {//<<-------------------------------------------------------------- Method _getUpdateSQL
            
            if( !is_array($fields) ) {
                return false;
            }
            
            //Arreglo con los valores de las sentencias
            $sentenceValues = array();
            
            //Crea el arreglo de valores para la sentencia SQL
            foreach($fields as $index => $value){//<<---------------- Crea arreglo valores

                //Identificaci�n de la llave primaria
                if( $index == $this->_primaryKey ) {
                    $id = $this->_pk;
                    continue;
                }
                    
                    $value = $this->$index;

                    //Escape del valor
                    if( $escape === true ) {
                        $value = Escapa::escaparFuncion($value, $this);
                    }

                    //Asignaci�n de valores al arreglo
                    array_push($sentenceValues, "{$index} = {$this->_format($index, $value)}");

            }//<<---------------- End Crea arreglo valores
                
            $values = implode(', ',$sentenceValues); 

            //Construye la sentencia SQL
            $updateSQL = "UPDATE {$this->_baseTbl} SET {$values} WHERE {$this->_primaryKey} = {$id} ";
            
            return $updateSQL;
                
        }//<<-------------------------------------------------------------- End Method _getUpdateSQL
        
        
    /*
     * Genera consultas INSERT din�micamente.
     * @access    private
     * @params    Array fields->Arreglo con los campos a editar
     *            boolean escape->Determina si se escapar�n los contenidos de los campos
     * @methods   classes.Escapa::escaparFuncion() 
     */
        private function _getInsertSQL( $fields = array(), $escape = false )
        {//<<-------------------------------------------------------------- Method _getInsertteSQL
            
            if( !is_array($fields) ) {
                return false;
            }
            
            //Arreglo con los valores de las sentencias
            $sentenceValues = array();
            
            //Indices equivalentes a los nombres de los campos
            $indexKeys = array();

            //Crea el arreglo de valores para la sentencia SQL
            foreach( $fields as $index => $value ) {//<<---------------- Crea arreglo valores

                //Descarta la llave primaria en el arreglo
                if( $index == $this->_primaryKey ) {
                    continue;
                }

                $value = $this->$index;
                array_push($indexKeys,$index);

                //Escape del valor
                if( $escape === true ) {
                    $value = Escapa::escaparFuncion($value, $this);
                }

                //Asignaci�n de valores al arreglo
                array_push($sentenceValues,$this->_format($index,$value));

            }//<<---------------- Crea arreglo valores

            $tableFields = implode(', ',$indexKeys);
            $values = implode(', ',$sentenceValues);
                
            //Construye la sentencia SQL
            $insertSQL = "INSERT INTO {$this->_baseTbl} ({$tableFields}) VALUES ({$values}) ";
            
            return $insertSQL;
                
        }//<<-------------------------------------------------------------- End Method _getInsertSQL
         
        
	/*
	 * Obtiene el ID del �ltimo registro insertado en la tabla.
	 * @access	public
	 * @methods	ADODB_postgres64::pg_insert_id()
     *          ADODB_pdo_mysql::_insertid()
	 */
		public function getLastId()
		{//<<-------------------------------------------------------------- Method _getLastId

			switch($this->dbType)
			{//<<---------------- Switch dbType
				case 'postgres8':
					$id = $this->baseDb->pg_insert_id( $this->_baseTbl, $this->_primaryKey );
				break;

				case 'mysql':
					$id = $this->baseDb->_insertid();
				break;

				default:
					$id = false;
				break;
			}//<<---------------- End Switch dbType

			return $id;
		}//<<-------------------------------------------------------------- End Method _getLastId
        
        
    /*
     * Log de errores en acciones Save
     * @access    private
     * @methods   Bitacora::Bitacora(), this::save()
     */
        private function _logSaveErrors()
        {//<<-------------------------------------------------------------- Method _logSaveErrors
           $errorDesc = "Error msg: {$this->baseDb->_errorMsg}
                              Archivo: {$_SERVER['SCRIPT_FILENAME']}
                              Clase: {$className}
                              Query: {$this->_querySQL} ";
		   exit('Error: '.$errorDesc);
        }//<<-------------------------------------------------------------- End Method _logSaveErrors        

        
    /*
     * Obtener el valor de la ultima query ejecutada
     * @access    public
     * @methods   __getQuerySQL()
     */
        public function __getQuerySQL(){
            return $this->_querySQL;
        }
        

	/*
	 * Borra el registro correspondiete al Id introducido como par�metro
	 * @access	public
	 * @params	id->Id de la llave primaria a borrar. Llave primaria establecida en la instancia por default
	 * @methods	this::_execute()
	 */
		public function delete( $id = false )
        {//<<-------------------------------------------------------------- Method delete
			$id = (!id)? $this->_pk:$id;
			$this->_querySQL = "DELETE FROM {$this->_baseTbl} WHERE {$this->_primaryKey} = {$id} ";
			return $this->_execute($this->_querySQL);
		}//<<-------------------------------------------------------------- End Method delete

        
		/***
	    *   @description:  M�todo que permite iniciar una transacci�n
		*   @param:        void
		*   @return:       boolean
		*   @updater:      TH
		*   @updated_date: 25/08/2010
	    ***/
        public function startTransaction(){//<<--------------------------------------------------------------
		   $this->_querySQL = "BEGIN";
		   return $this->_execute($this->_querySQL);   
		}//<<--------------------------------------------------------------
		
		/***
	    *   @description:  M�todo que hece rollback a una transacci�n
		*   @param:        void
		*   @return:       boolean
		*   @updater:      TH
		*   @updated_date: 25/08/2010
	    ***/
		public function rollbackTransaction(){//<<--------------------------------------------------------------
		   $this->_querySQL = "ROLLBACK";
		   return $this->_execute($this->_querySQL);
		}//<<--------------------------------------------------------------
		
		/***
	    *   @description:  M�todo que ejecuta la transacci�n
		*   @param:        void
		*   @return:       boolean
		*   @updater:      TH
		*   @updated_date: 25/08/2010
	    ***/
		public function commitTransaction(){//<<--------------------------------------------------------------
		   $this->_querySQL = "COMMIT";
		   return $this->_execute($this->_querySQL);   
		}//<<--------------------------------------------------------------
		
	 /*
	 * Ejecuta Sentencias SQL
	 * @access	protected
	 * @params	sql->Sentencia SQL a ejecutar
     * @methods adodb.Adodb::Execute()
     * @returns boolean
	 */
		protected function _execute($sql='')
        {//<<-------------------------------------------------------------- Method _execute
			$sql = ( empty($sql) ) ? $this->_querySQL : $sql;
            return ( ($this->baseRs = $this->baseDb->Execute($sql)) ) ? true : false;
		}//<<-------------------------------------------------------------- End Method _execute


	 /*
     * Realiza el debug de las consultas SQL provenientes de las clases hijas
     * @access    public
     */
        public function debug()
        {//<<-------------------------------------------------------------- Method debug
            return $this->baseDb->debug = true;
        }//<<-------------------------------------------------------------- End Method debug
        

     /*
      *  Destructor General
      *  Liberar Recursos y desconexi�n con la Base de Datos
      *  @access public
      */        
        public function __destruct()
        {//<<-------------------------------------------------------------- __destruct()
            /*if( !empty($this->baseRs) ) {
                $this->baseRs->Close();
            }
            
            $this->baseDb->Close();*/
        }//<<-------------------------------------------------------------- End __destruct()

  }//---------------------------------------------------------------------------------->>> End Class Base

?>