<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase activity
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Cat_Activity extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_Activity

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('cat_activity', $id);
            }//<<-------------------------------------------------------- End construct()            
            
            /***
	    *   @description:  Método que obtiene el combo activity
		*   @param:        void
		*   @return:       $comboActivity .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getComboActivity()//<<------------------------------------------------------------ getComboActivity()
            {
                
                
                $nRegistros = 0;
                $options = '';
                $comboActivity = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Tipo Actividad</option></select>';
                $i=0;
                
                $this->_querySQL = "SELECT * FROM cat_activity WHERE n_estatus_id = 3 ORDER BY c_activity_desc";
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {   
                        $options.='<option value = "'.$this->baseRs->fields['n_activity_id'].'">'.$this->baseRs->fields['c_activity_desc'].'</option>';
                        $this->next();
                    }
                    
                    if($options == '')
                        return $comboActivity;
                    else
                    {
                        $comboActivity = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled>Seleccionar Tipo Actividad</option>{ADDOPTION}'.$options.'</select>';
                        return $comboActivity;
                    }
                    
                }
                else
                    return $comboActivity;
            }//<<------------------------------------------------------------ end getComboActivity()
            
            
}//<<----------------------------------------------------- End Class Base_Cat_Activity

?>
