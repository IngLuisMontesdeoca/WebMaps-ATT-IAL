<?php

    /********************************************************************************
    *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              		*
    *   @version:       1.0                                     					*
    *   @created:       13/03/2014                              					*
    *   @copiright:     Copyright (c) 2014, WebMaps              					*
    *   @description    Metodos de acceso a la tabla dat_contacto             *
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_Contacto extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_Contacto

		/***
	    *   @description:  Mï¿½todo constructor de la clase
		*   @param:        id .- (int) Id de la llave primaria
		*   @return:       void
		*   @updater:      LM
		*   @updated_date: 13/03/2014
	    ***/
        function __construct( $id = 0 )
        {//<<------------------------------------------------------------ construct()
		    parent::__construct('dat_contacto', $id);
	    }//<<-------------------------------------------------------- End construct()
            
            	/***
	    *   @description:  Mï¿½todo para obtener lso contactos asociados a un handset
		*   @param:        idHandset .- (string) Id del handset
		*   @return:       arrContactos .- (Array) Arreglo con la informacion de los contactos
		*   @updater:      LM
		*   @updated_date: 13/03/2014
	    ***/
		public function getContactosByHandset( $idHandset = '' ){//<<------------------------------------------------------------ getContactosByHandset()
                    $arrContactos = array();
                    $this->_querySQL = "SELECT n_contacto_id,"
                                        . "c_contacto_nombre,"
                                        . "c_contacto_numerocorreo,"
                                        . "c_contacto_tipocontacto "
                            . "FROM {$this->_baseTbl} WHERE n_handset_id = {$idHandset} AND n_estatus_id = 3";
                    $this->_execute($this->_querySQL); 
                    if($this->numRows() > 0){
	  		 for($i=0; $i<$this->numRows(); $i++){
                            $arrContactos[$i]['idContacto']     = $this->baseRs->fields['n_contacto_id'];
                            $arrContactos[$i]['nombre']         = $this->baseRs->fields['c_contacto_nombre'];
                            $arrContactos[$i]['numerocorreo']   = $this->baseRs->fields['c_contacto_numerocorreo'];
                            $arrContactos[$i]['tipo']           = $this->baseRs->fields['c_contacto_tipocontacto'];
                            $this->next();
			 }
                    }
                    return $arrContactos;
		}//<<-------------------------------------------------------- End getContactosByHandset()
            
                /***
	    *   @description:  Metodo para obtener los numeros que requieren verificacion de tipo de compañia
		*   @param:        
		*   @return:       arrContactos .- (Array) Arreglo con la informacion de los contactos
		*   @updater:      LM
		*   @updated_date: 22/04/2014
	    ***/
                public function getContactosForGetCompany( ){//<<------------------------------------------------------------ getContactosForGetCompany()
                    $arrContactos = array();
                    $this->_querySQL = "SELECT n_contacto_id,"
                                        . "c_contacto_numerocorreo "
                            . "FROM {$this->_baseTbl} WHERE c_contacto_tipocontacto IS NULL AND n_estatus_id = 3";
                    $this->_execute($this->_querySQL); 
                    if($this->numRows() > 0){
	  		 for($i=0; $i<$this->numRows(); $i++){
                            $arrContactos[$i]['idContacto']     = $this->baseRs->fields['n_contacto_id'];
                            $arrContactos[$i]['numerocorreo']   = $this->baseRs->fields['c_contacto_numerocorreo'];
                            $this->next();
			 }
                    }
                    return $arrContactos;
		}//<<-------------------------------------------------------- End getContactosForGetCompany()
	       
	}//<<----------------------------------------------------- End Class Base_Dat_Contacto

?>