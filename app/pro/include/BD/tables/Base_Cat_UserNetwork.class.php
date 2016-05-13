<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase usernetwork
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Cat_UserNetwork extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_User_Network

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('cat_usernetwork', $id);
            }//<<-------------------------------------------------------- End construct()            
            
            
            /***
	    *   @description:  Método que obtiene el combo red
		*   @param:        void
		*   @return:       $comboRed .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/            
            public function getComboUserNetwork()//<<------------------------------------------------------------ getComboUserNetwork()
            {
                $nRegistros = 0;
                $options = '';
                $comboRed = '<select class="{CLASSHTML}" id="{IDHTML}"><option value="0" disabled selected>Seleccionar Red</option></select>';
                $i = 0;                
                
                
                $this->_querySQL="SELECT * FROM cat_usernetwork ORDER BY c_usernetwork_desc";
                $this->_execute($this->_querySQL);
                $nRegistros = $this->numRows();
                
                if($nRegistros>0)
                {
                    for($i=0; $i<$nRegistros; $i++)
                    {           
                        $options.='<option value="'.$this->baseRs->fields['n_usernetwork_id'].'">'.$this->baseRs->fields["c_usernetwork_desc"].'</option>';
                        $this->next();
                    }
                    
                    if($options=='')
                        return $comboRed;
                    else
                    {
                        $comboRed = '<select class="{CLASSHTML}" id="{IDHTML}">{ADDOPTION}'.$options.'</select>';
                        return $comboRed;
                    }
                }
                else
                    return $comboRed;
                
            }//<<------------------------------------------------------------ end getComboUserNetwork()
        
}//<<----------------------------------------------------- End Class Base_Cat_UserNetwork

?>
