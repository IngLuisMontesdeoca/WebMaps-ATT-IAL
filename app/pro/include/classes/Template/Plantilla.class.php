<?php

	/********************************************************************
    *                                                           		*
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   		*
    *   @version:       2.0                                     		*
	*	@since			05/01/2009										*
	*	@modified		23/02/2009										*
    *   @copiright:     Copyright (c) 2009, SkyTel              		*
    *   @link:          http://localhost/Polarix/include/class  Go   	*
    *   @description:   Clase que construye vistas dinámicamente a  	*
	*					partir de plantillas Vista.	                	*
	*	@modifications:	Inclusión del método que obtiene el contenido de*
	*					un archivo y lo devuelve como cadena 			*
	*					getFileContent().								*
	*																	*
    ********************************************************************/
	
  class Plantilla
  {//<<--------------------------------------------------------------------------- Class Plantilla

    /*
     * @access 	private 
     * @type    String
     * @desc	Tipo de Vista del archivo
     */
	    private $_tplType = '';
        
    /*
     * @access  private
     * @type    String
     * @desc    Ruta del archivo que contiene la plantilla
     */
        private $_tplFile = '';

    /*
     * @access 	private 
     * @type    Array
     * @desc	Variables a sustituir en la plantilla
     */	
	    private $_vars = array();

    /*
     * @access 	private
     * @type    String 
     * @desc	Contenido del archivo plantilla en texto plano
     */
	    private $_fd = '';
	    
    /*
     * @access 	private 
     * @type    String
     * @desc	Vista resultante en forma de texto plano
     */	
	    private $_vista = '';
			

	/*
	 * Constructor
	 * @param	archivo->Nombre del archivo que contiene la plantilla
	 */
	function __construct( $archivo = '') 
    {//<<------------------------------------------------- Constructor
		
        if( empty($archivo) ) {
            $this->showError('Especifique la vista a utilizar');
        }
        
        $ext = substr(strrchr($archivo, "."),1);
        
        switch( $ext ) {//<<------------------------------ Switch tipo
        
            case 'html':
                $this->_tplType = 'html';
                $this->_tplFile = ROOT_TEMPLATES."/XHTML/{$archivo}";  
            break;                        
        
            case 'xml':
                $this->_tplType = 'xml';
                $this->_tplFile = ROOT_TEMPLATES."/XML/{$archivo}";
            break;
            
            case 'txt':
                $this->_tplType = 'txt';
                $this->_tplFile = ROOT_TEMPLATES."/txt/{$archivo}";
            break;
            
            default:
                $this->showError( 'Tipo de archivo no v&aacute;lido' );
            break;
        
        }//<<------------------------------ End Switch tipo 
        
        //Error si el archivo no existe
        if( !file_exists( $this->_tplFile ) ) {
            $this->showError( 'No existe el archivo de vista especificado' );
        }
          
	}//<<------------------------------------------------- End Constructor
	
	
	/*
	 * @access 	public
	 * @desc	Asigna las variables que recibe como argumento a un atributo de la clase
	 * @param	vars->Arreglo de variables a sustituir en el template
	 */
	public function asignaVariables( $vars = array() )
    {//<<------------------------------------------------- asignaVariables()
		
        if( !is_array($vars) ) {
            $this->showError('Ingrese los valores a sustituir en la Plantilla en forma de arreglo');
        }
        
        $this->_vars = (empty($this->_vars)) ? $vars : $this->_vars . $vars;
        
	}//<<------------------------------------------------- End asignaVariables()
	
	
	/*
	 * @access 	public
	 * @desc	Construye la Vista y la retorna al usuario final.
	 * @see		this->showError()
	 */
	public function construyeVista()
	{//<<------------------------------------------------- construyeVista()
	
		if( !($this->_fd = fopen($this->_tplFile, 'r')) ) {//<<------------------------------ If open file
			
			$this->showError( "Error al abrir la plantilla {$this->_tplFile}" );
			
		} else {//<<------------------------------ Else open file
		
			$this->_tplFile = fread($this->_fd, filesize($this->_tplFile));
			fclose($this->_fd);
			$this->_vista = $this->_tplFile;
            
            /*  
            * Escape de comillas simples y sustitución de las variables de 
            * la plantilla de {var} a $var.
            */ 
			$this->_vista = str_replace ("'", "\'", $this->_vista);
			$this->_vista = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->_vista);
			
			//Creación de variables temporales por cada elemento del arreglo _vars
            reset($this->_vars);
			while (list($key, $val) = each($this->_vars)) {
				$$key = $val;
			}
			
            //Sustitución de valores
			eval("\$this->_vista = '$this->_vista';");
			
            //Eliminación de las variables temporales
            reset($this->_vars);
			while( list($key, $val) = each($this->_vars) ) {
				unset($$key);
			}
			
            //Descapamos las comillas simples
			$this->_vista = str_replace("\'", "'", $this->_vista);
						
		}//<<------------------------------ End If open file
		
	}//<<------------------------------------------------- End construyeVista()
	
		
    /*
     * @access  public
     * @desc    Retorna al usuario final la vista resultante.
     */
	public function getVista( $show = false ) 
    {//<<------------------------------------------------- getVista()
    
        if( $this->_tplType === 'html' ) {    
            $this->_vista = self::compress( $this->_vista );
        }
        
		if ($show == true){ 
			echo $this->_vista;
		}else{
			return $this->_vista;
		}
        
	}//<<------------------------------------------------- End getVista()

    
     /*
      * @access  static public
      * @desc    Comprime la salida para vistas (X)HTML.
      * @param   buffer->Contenido (X)HTML a comprimir
      */   
     static public function compress( $buffer='' ) 
     {//<<------------------------------------------------- compress()
        
        //Remueve Comentarios
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        
        //Remueve Comentarios del tipo <!-- -->
        $buffer = preg_replace('(<!-- (.*)-->)','',$buffer);
        
        //Remueve saltos de linea, tabuladores y espacios en blanco
        $buffer = str_replace( array("\r\n", "\r", "\n", "\t", '    ', '     ', '      '), '', $buffer );
        
        return $buffer;
        
     }//<<------------------------------------------------- End compress()
	
    
	/*
	 * @access 	private
	 * @desc	Construye la Vista de error y la retorna al usuario final finalizando la
     *          ejecución del script.
	 * @params	message->mensaje de error a mostrar en la plantilla
	 */	
	private function showError( $message = '' ) 
    {//<<------------------------------------------------- showError()
		  
        $this->_tplFile = ROOT_TEMPLATES.'/errors/error.html';
        
        if( !file_exists( $this->_tplFile ) ) {//<<------------------------------ If !file_exist
        
            $tempError = "Se trat&oacute; de mostrar el Error: {$mensaje} pero no existe el archivo 
                          error.html en la carpata vistas/errors/";  
            echo $tempError;
            
        } else {//<<------------------------------ Else !file_exist
        
            $this->asignaVariables( array( 'titulo'=>'de Plantillas', 'ERROR' => $message ) );
            $this->construyeVista();
            echo $this->getVista();
            
        }//<<------------------------------ End If !file_exist
        
        exit();
        
	}//<<------------------------------------------------- End showError()
    	
	
	/*
     * @access  static public
     * @desc    Obtiene el contenido de un archivo y lo devuelve como una cadena.
     * @params  path->Ruta de un archivo
     */
    static public function getFileContent( $path ) 
    {//<<------------------------------------------------- getFileContent()
	
		if( empty($path) ) {
            exit('Ingrese ruta del Archivo a convertir en Cadena');
        }
		
		$ext = substr(strrchr($path, "."),1);
        
        switch( $ext ) {//<<------------------------------ Switch tipo
        
            case 'html':
            	$path = ROOT_TEMPLATES."/XHTML/{$path}";  
            break;                        
        
            case 'xml':
            	$path = ROOT_TEMPLATES."/XML/{$path}"; 
            break;
            
            case 'txt':
            	$path = ROOT_TEMPLATES."/txt/{$path}"; 
            break;
            
            default:
                exit( 'Tipo de archivo a convertir no v&aacute;lido' );
            break;
        
        }//<<------------------------------ End Switch tipo
		
		if( file_exists($path) ) {//<<------------------------------ if file_exist
			
			$fileContent = '';
			$fileContent = file_get_contents($path);
			return $fileContent;
			
		} else {//<<------------------------------ else file_exist
			
			exit('El archivo que desea convertir No Existe');
			
		}//<<------------------------------ End if file_exist
	
	
    }//<<------------------------------------------------- End getFileContent()
		
    
  }//<<--------------------------------------------------------------------------- End Class Plantilla

?>