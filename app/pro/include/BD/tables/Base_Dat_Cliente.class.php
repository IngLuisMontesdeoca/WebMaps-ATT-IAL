<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase Cliente			                                *
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_Cliente extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_Cliente

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_cliente', $id);
            }//<<-------------------------------------------------------- End construct()            
        
            /***
	    *   @description:  Método verifica si existe Cliente por nombre
		*   @param:        nombreCliente.- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function isCliente($nombreCliente=''){//<<------------------------------------------------------------ isCliente()
                    
                    $this->_querySQL = "SELECT 
                                            n_cliente_id 
                                        FROM 
                                            dat_cliente 
                                        WHERE 
                                            c_cliente_nombre = '{$nombreCliente}'
                                            AND n_estatus_id = 3
                                        ";
                             
                    $this->_execute($this->_querySQL);
                    
                    if($this->numRows()>0)
                        return '1';
                    else
                        return '0';

                }//<<------------------------------------------------------------ End isCliente()
                
            /***
	    *   @description:  Método verifica combo cliente
		*   @param:        void
		*   @return:       comboCliente .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getComboCliente()
                {//<<------------------------------------------------------------ GetComboCliente()                
                    
                    $nRegistros = 0;
                    $options = '';
                    $comboCliente = '<select class="{CLASSHTML}" id="{IDHTML}"><option value="0" disabled selected>Seleccionar Cliente</option></select>';
                    $i=0;

                    $this->_querySQL = "SELECT n_cliente_id, c_cliente_nombre FROM dat_cliente WHERE n_estatus_id = 3 ORDER BY c_cliente_nombre";

                    $this->_execute($this->_querySQL);
                    
                    $nRegistros = $this->numRows();

                    if($nRegistros > 0)
                    {
                        for($i = 0; $i<$nRegistros; $i++)
                        {
                            $options.='<option value = "'.$this->baseRs->fields['n_cliente_id'].'">'.$this->baseRs->fields['c_cliente_nombre'].'</option>';
                            $this->next();
                        }

                        if($options == '')
                            return $comboCliente;
                        else
                        {
                            $comboCliente = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Cliente</option>'.$options.'</select>';
                            return $comboCliente;
                        }

                    }
                    else
                        return $comboCliente;                    
                    
                }//<<------------------------------------------------------------ end GetComboCliente()                
                
}//<<----------------------------------------------------- End Class Base_Dat_Cliente

?>
