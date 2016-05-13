<?php

    /****************************************************************
    *                                                         		*
    *   @autor:      	Julio Mora <julio.mora@skytel.com.mx>		*
    *   @version:       1.0                                   		*
    *   @created        24/02/2009                            		*
    *   @copiright:     Copyright (c) 2009, SkyTel            		*
    *   @link:          http://localhost/suauto/include/BD/pages Go	*
    *   @description    Clase para paginar resultados utilizando la *
	*					nomenclatura MySQL para utilizar el prlugin	*
	*					FlexiGrid.js como reportador				*
    *                                                         		*
    ****************************************************************/

    //---- REQUIRE ----//
        //Clase Base
            require_once "Base.class.php";
			
			
  final class Base_Pagina extends Base
  {//<<------------------------------------------------------------------------------- Class Base_Pagina
	
   /*
    * @acces    private String
    * @desc     Cadena que determina el campo para realizar búsquedas del tipo
    *           LIKE 
    */
      private $_query = '';
      
    /*
     * @acces   private String
     * @desc    Cadena que determina el valor del campo para realizar búsquedas
     *          del tipo WHERE
     */ 
      private $_qType = '';
      
    /*
     * @acces   private String
     * @desc    Cadena que determina el campo por el cuál se hará el ordenamiento.
     */
      private $_sortName = '';
      
    /*
     * @acces   private String
     * @desc    Cadena que determina el tipo de ordenamiento. Descendente por default.
     */
      private $_sortOrder = 'ASC';
      
    /*
     * @acces   private Integer
     * @desc    Número de página actual. 1 por default.
     */      
      private $_page = 1;

    /*
     * @acces   private Integer
     * @desc    Número de registros a mostrar en la paginación. 10 por default.
     */
      private $_rp = 10;

    /*
     * @acces   private String
     * @desc    Cadena que representará el complemento WHERE de la búsqueda.
     */      
      private $_where = '';
      
    /*
     * @acces   private String
     * @desc    Cadena que representará el complemento ORDER BY de la búsqueda.
     */      
      private $_sort = '';
      
    /*
     * @acces   private String
     * @desc    Cadena que representará el complemento LIMIT de la búsqueda.
     */      
      private $_limit = '';
    
    /*
     * @acces   private Integer
     * @desc    Número de registros encontrados en la tabla.
     */      
      private $_total = 0;    
      
    /*
     * @acces   private Integer
     * @desc    Número del registro a partir del cuál se realizará la paginación.
     */      
      private $_start = 0;
	
	
	/*
	* Constructor
	* @methods	Base::setAndConect(), Base::setTable(), Base::setPk()
	*/
		function __construct( $table = '' ) 
        {//<<------------------------------------------- Constructor
			
			if( !empty($table) ) {
			
				$this->setAndConnect();
				$this->setTable($table);
				$this->setPk(0);
				
			} else {
				exit('Indique el nombre de la Tabla que desea paginar');
			}
			
		}//<<------------------------------------------- End Constructor
		
   
   /*
    * @method       getPage()
    * @access       final public
    * @description  Obtiene el número de la página actual
    */    
        final public function getPage()
        {//<<------------------------------------------- getPage() 
            return $this->_page;
        }//<<------------------------------------------- End getPage()
    
    
   /*
    * @method       total()
    * @access       final public
    * @description  Calcula el número total de registros que se encuentran en la tabla
    * @params       where->Construcciones AND's, OR's o LIKE para consultar un registro. Vacío por default.
    *               campo->Campo por el cuál se realizará la consulta
    */    
        final public function total( $campo = '')
        {//<<------------------------------------------- totales()
		
            $campo = ( empty($campo) ) ? $this->_sortName : $campo;
			
			if( !empty($campo) ) {
				
				$this->_querySQL = "SELECT COUNT({$campo}) AS total FROM {$this->_baseTbl} {$this->_where} ";
				$this->_execute($this->_querySQL);
				return $this->baseRs->fields['total'];
				
			}else{
				exit('Indique el nombre de un campo para calcular el n&uacute;mero total de registros');
			}
			
        }//<<------------------------------------------- End totales()	
		
		
   /*
    * @method       setValues()
    * @acces        final public
    * @description  Asigna valores a las propiedades de la clase a partir de los valores
    *               de un arreglo. Útil para arreglos $_POST y $_GET
    * @param        array->Arreglo con los valores a setear
    */ 
        final public function setValues( $array = array(), $sortName = '', $especificWhere = '')
        {//<<------------------------------------------- setValues()
            
            if( !is_array( $array ) || empty($sortName) ) {//<<---------------- if is_array
                exit('Indique matriz de valores y campo de ordenamiento por Default');
            }//<<---------------- End if is_array
            
            foreach( $array as $index => $value ) {//<<---------------- foreach $array
    
              switch( $index ){//<<---------------- Switch index
                  
                  case 'query':
                    $this->_query = Escapa::escapar($array['query']);
                  break;
                  
                  case 'qtype':
                    $this->_qType = $array['qtype'];
                  break;
                  
                  case 'sortname':
                    $this->_sortName = $array['sortname'];
                  break;
                  
                  case 'sortorder':
                    $this->_sortOrder = $array['sortorder'];
                  break;
                  
                  case 'page':
                    $this->_page = $array['page'];
                  break;
                  
                  case 'rp':
                    $this->_rp = $array['rp'];
                  break;
                  
                  default:
                    continue;
                  break;
                  
              }//<<---------------- End Switch index
              
            }//<<---------------- End foreach $array
			
			$this->_sortName = ( empty($this->_sortName) ) ? $sortName : $this->_sortName;
            $this->_start = (($this->_page-1) * $this->_rp);
			
			//Definición del complemento WHERE de la sentencia SQL
			if( empty($this->_query) ) {//<<---------------- if empty _query
			
				$this->_where = ( !empty($especificWhere) ) ? "WHERE {$especificWhere}" : '';
				
			}else{//<<---------------- else empty _query
				
				$this->_where = "WHERE {$this->_qType} LIKE '%{$this->_query}%'";
				
				if( !empty($especificWhere) ) {
					$this->_where = "{$this->_where} AND {$especificWhere}";
				}
				
			}//<<---------------- End if empty _query
			
            $this->_sort = "ORDER BY {$this->_sortName} {$this->_sortOrder}";
            $this->_limit = "LIMIT {$this->_start}, {$this->_rp}";
        
        }//<<------------------------------------------- End setValues()
		
     
   /*
    * @method       pages()
    * @access       final public
    * @description  Consulta datos paginados de la tabla
    */
        final public function pages( $arrayFields = array() )
        {//<<------------------------------------------- pages()
            
            $fields = ( ( is_array($arrayFields) ) && ( !empty($arrayFields) ) ) ? implode(', ',$arrayFields) : '*';
			
			$this->_querySQL = "SELECT {$fields} FROM {$this->_baseTbl} ";
			
			//Unión por sustitución de la definición WHERE LIKE                    
			if( !empty($this->_where) ) {
				$this->_querySQL = "{$this->_querySQL} {$this->_where} ";
            }
            
			//Unión por sustitución de la definición ORDER BY y LIMIT
            $this->_querySQL = "{$this->_querySQL} {$this->_sort} {$this->_limit} ";
                                        
            $this->_execute($this->_querySQL);
                        
        }//<<------------------------------------------- End pages()		
		
			
	
    /*
    * Destructor
	* @methods	Base::_desconnect()
    */
        function __destruct() 
        {//<<------------------------------------------- Destructor
            //parent::__destruct();    
        }//<<------------------------------------------- End Destructor  
        
        
  }//<<------------------------------------------------------------------------------- Class Class Base_Pagina

?>