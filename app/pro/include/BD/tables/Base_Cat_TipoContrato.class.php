<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase tipocontrato
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Cat_TipoContrato extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_TipoContrato

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('cat_tipocontrato', $id);
            }//<<-------------------------------------------------------- End construct()            
            
            /***
	    *   @description:  Método que obtiene el combo tipocontrato
		*   @param:        void
		*   @return:       $comboTipoContrato .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getComboTipoContrato()//<<------------------------------------------------------------ getComboTipoContrato()
            {
                
                
                $nRegistros = 0;
                $options = '';
                $comboTipoContrato = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Tipo Contrato</option></select>';
                $i=0;
                
                $this->_querySQL = "SELECT * FROM cat_tipocontrato WHERE n_estatus_id = 3 ORDER BY c_tipocontrato_desc";
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {   
                        $options.='<option value = "'.$this->baseRs->fields['n_tipocontrato_id'].'">'.$this->baseRs->fields['c_tipocontrato_desc'].'</option>';
                        $this->next();
                    }
                    
                    if($options == '')
                        return $comboTipoContrato;
                    else
                    {
                        $comboTipoContrato = '<select class="{CLASSHTML}" id="{IDHTML}">{ADDOPTION}'.$options.'</select>';
                        return $comboTipoContrato;
                    }
                    
                }
                else
                    return $comboTipoContrato;
            }//<<------------------------------------------------------------ end getComboTipoContrato()
            
            
}//<<----------------------------------------------------- End Class Base_Cat_TipoContrato

?>
