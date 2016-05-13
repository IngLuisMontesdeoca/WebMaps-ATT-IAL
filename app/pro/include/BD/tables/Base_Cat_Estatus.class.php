<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase estatus
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Cat_Estatus extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_Estatus

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('cat_estatus', $id);
            }//<<-------------------------------------------------------- End construct()            
            
            /***
	    *   @description:  Método que obtiene el combo estatus
		*   @param:        void
		*   @return:       $comboEstatus .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getComboEstatus($idsEstatus='')//<<------------------------------------------------------------ getComboEstatus()
            {
                
                
                $nRegistros = 0;
                $options = '';
                $comboEstatus = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Estatus</option></select>';
                $i=0;
                $idsEstatus = str_replace(",","','",$idsEstatus);
                
                $this->_querySQL = "SELECT * FROM cat_estatus WHERE n_estatus_id IN ('{$idsEstatus}') ORDER BY c_estatus_desc";
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {                        
                        $options.='<option value = "'.$this->baseRs->fields['n_estatus_id'].'">'.$this->baseRs->fields['c_estatus_desc'].'</option>';
                        $this->next();
                    }
                    
                    if($options == '')
                        return $comboEstatus;
                    else
                    {
                        $comboEstatus = '<select class="{CLASSHTML}" id="{IDHTML}">{ADDOPTION}'.$options.'</select>';
                        return $comboEstatus;
                    }
                    
                }
                else
                    return $comboEstatus;
            }//<<------------------------------------------------------------ end getComboEstatus()
            
            
}//<<----------------------------------------------------- End Class Base_Cat_Estatus

?>
