<?php

    /********************************************************************************
    *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              		*
    *   @version:       1.0                                     					*
    *   @created:       14/03/2014                              					*
    *   @copiright:     Copyright (c) 2014, WebMaps              					*
    *   @description    Metodos de acceso a la tabla dat_contadortransacciones             *
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_ContadorTransacciones extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_ContadorTransacciones
            
		/***
	    *   @description:  M�todo constructor de la clase
		*   @param:        id .- (int) Id de la llave primaria
		*   @return:       void
		*   @updater:      LM
		*   @updated_date: 13/03/2014
	    ***/
        function __construct( $id = 0 )
        {//<<------------------------------------------------------------ construct()
		    parent::__construct('dat_contadortransacciones', $id);
	    }//<<-------------------------------------------------------- End construct()
            
            	/***
	    *   @description:  M�todo para obtener el numero actual de transacciones
		*   @param:        void
		*   @return:       numeroTransacciones .-(int) Numero de transacciones
		*   @updater:      LM
		*   @updated_date: 14/03/2014
	    ***/
		public function getTransacciones(){//<<------------------------------------------------------------ getTransacciones()
                    $numeroTransacciones = 0;
                    $this->_querySQL = "SELECT n_contadortransacciones_numero "
                            . "FROM {$this->_baseTbl} ";
                    $this->_execute($this->_querySQL); 
                    if($this->numRows() > 0){
	  		 for($i=0; $i<$this->numRows(); $i++){
                            $numeroTransacciones    = $this->baseRs->fields['n_contadortransacciones_numero'];
                            $this->next();
			 }
                    }
                    return $numeroTransacciones;
		}//<<-------------------------------------------------------- End getTransacciones()
            
            /***
	    *   @description:  Metodo para actualizar el numero de transacciones
            *   @param:        $numeroTransacciones .-(int) Numero de transacciones
            *   @return:                            .- (bool) Resultado de la actualizacion
            *   @updater:      LM
            *   @updated_date: 14/03/2014
	    ***/
            public function updateTransacciones($numeroTransacciones)
            {//<<------------------------------------------------------------ updateTransacciones()
                $this->_querySQL = "UPDATE  {$this->_baseTbl} SET n_contadortransacciones_numero = {$numeroTransacciones}";
		$this->querySQL = $this->_querySQL;
                if( $this->_execute( $this->_querySQL ) )
                    return true;
		else 
                    return false;
            }//<<-------------------------------------------------------- End updateTransacciones()
                
	       
	}//<<----------------------------------------------------- End Class Base_Dat_ContadorTransacciones

?>