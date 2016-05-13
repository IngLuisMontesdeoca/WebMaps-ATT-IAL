<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase topousuarioaplicacion
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Cat_TipoUsuarioAplicacion extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_TipoUsuarioAplicacion

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('cat_tipousuarioaplicacion', $id);
            }//<<-------------------------------------------------------- End construct()    
            
            
            /***
	    *   @description:  Método que obtiene el combo tipousuario
		*   @param:        void
		*   @return:       $comboTipoUsuario .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/            
            public function getComboTipoUsuario()//<<------------------------------------------------------------ getComboTipoUsuario()
            {
                $nRegistros = 0;
                $options = '';
                $comboTipoUsuario = '<select class="{CLASSHTML}" id="{IDHTML}"><option value="0" disabled selected>Seleccionar Tipo Usuario</option></select>';
                $i = 0;                
                
                
                $this->_querySQL="SELECT * FROM cat_tipousuarioaplicacion WHERE n_estatus_id = 3 ORDER BY c_tipousuario_desc";
                $this->_execute($this->_querySQL);
                $nRegistros = $this->numRows();
                
                if($nRegistros>0)
                {
                    for($i=0; $i<$nRegistros; $i++)
                    {           
                        $options.='<option value="'.$this->baseRs->fields['n_tipousuario_id'].'">'.$this->baseRs->fields["c_tipousuario_desc"].'</option>';
                        $this->next();
                    }
                    
                    if($options=='')
                        return $comboTipoUsuario;
                    else
                    {
                        $comboTipoUsuario = '<select class="{CLASSHTML}" id="{IDHTML}">{ADDOPTION}<option value="0" disabled selected>Seleccionar Tipo Usuario</option>'.$options.'</select>';
                        return $comboTipoUsuario;
                    }
                }
                else
                    return $comboTipoUsuario;
                
            }//<<------------------------------------------------------------ end getComboUserNetwork()            
        
}//<<----------------------------------------------------- End Base_Cat_TopoUsuarioAplicaion

?>
