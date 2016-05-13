<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase UserType
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Cat_UserType extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_UserType

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('cat_usertype', $id);
            }//<<-------------------------------------------------------- End construct()            
            
            /***
	    *   @description:  Método que obtiene el combo usertype
		*   @param:        void
		*   @return:       $comboUsertype .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getComboUserType()//<<------------------------------------------------------------ getComboUserType()
            {
                
                
                $nRegistros = 0;
                $options = '';
                $comboUserType = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Tipo Plan</option></select>';
                $i=0;
                
                $this->_querySQL = "SELECT * FROM cat_usertype ORDER BY c_usertype_desc";
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {   
                        $options.='<option value = "'.$this->baseRs->fields['n_usertype_id'].'">'.$this->baseRs->fields['c_usertype_desc'].'</option>';
                        $this->next();
                    }
                    
                    if($options == '')
                        return $comboUserType;
                    else
                    {
                        $comboUserType = '<select class="{CLASSHTML}" id="{IDHTML}">{ADDOPTION}'.$options.'</select>';
                        return $comboUserType;
                    }
                    
                }
                else
                    return $comboUserType;
            }//<<------------------------------------------------------------ end getComboUserType()
            
            
}//<<----------------------------------------------------- End Class Base_Cat_UserType

?>
