<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase cuenta
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_Cuenta extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_Cuenta

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_cuenta', $id);
            }//<<-------------------------------------------------------- End construct()            
        
            /***
	    *   @description:  Método verifica si existe cuenta por nombre
		*   @param:        idCliente.-(int)
                *   @param:        nombreCuenta.-(string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function isCuenta($idCliente='',$nombreCuenta=''){//<<------------------------------------------------------------ isCuenta()
                    
                    $this->_querySQL = "SELECT 
                                            cuenta.n_cuenta_id,
                                            cuenta.c_cuenta_cuenta,
                                            cliente.n_cliente_id idCliente
                                        FROM
                                            dat_cliente cliente,
                                            dat_cuenta cuenta,
                                            rel_cuentacliente rel_ce
                                        WHERE
                                            cliente.n_cliente_id = rel_ce.n_cliente_id
                                                AND rel_ce.n_cuenta_id = cuenta.n_cuenta_id
                                                AND cuenta.c_cuenta_cuenta = '{$nombreCuenta}'
                                                AND cuenta.n_estatus_id = 3
                                        ";                    
                    
                    $this->_execute($this->_querySQL);
                    
                    if($this->numRows()>0)
                    {
                        if($idCliente == $this->baseRs->fields['idCliente'])
                            return '1';
                        else
                            return '2';
                    }
                    else
                        return '0';

                }//<<------------------------------------------------------------ End isCuenta()
                
            /***
	    *   @description:  Método verifica combo cuenta
		*   @param:        idCliente.- (string)
		*   @return:       comboCliente .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getComboCuenta($idCliente)
                {//<<------------------------------------------------------------ GetComboCuenta()                
                    
                    $nRegistros = 0;
                    $options = '';
                    $comboCuenta = '<select class="{CLASSHTML}" id="{IDHTML}"><option value="0" disabled selected>Seleccionar Cuenta</option></select>';
                    $i=0;

                    $this->_querySQL = "SELECT 
                                            cuenta.n_cuenta_id, cuenta.c_cuenta_cuenta
                                        FROM
                                            dat_cuenta cuenta,
                                            rel_cuentacliente rel_ce
                                        WHERE
                                            cuenta.n_cuenta_id = rel_ce.n_cuenta_id
                                                AND rel_ce.n_cliente_id = '{$idCliente}'
                                                AND cuenta.n_estatus_id = 3
                                        ORDER BY
                                            cuenta.c_cuenta_cuenta
                                        ";

                    $this->_execute($this->_querySQL);
                    
                    $nRegistros = $this->numRows();

                    if($nRegistros > 0)
                    {
                        for($i = 0; $i<$nRegistros; $i++)
                        {
                            $options.='<option value = "'.$this->baseRs->fields['n_cuenta_id'].'">'.$this->baseRs->fields['c_cuenta_cuenta'].'</option>';
                            $this->next();
                        }

                        if($options == '')
                            return $comboCuenta;
                        else
                        {
                            $comboCuenta = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Cuenta</option>'.$options.'</select>';
                            return $comboCuenta;
                        }

                    }
                    else
                        return $comboCuenta;                    
                    
                }//<<------------------------------------------------------------ end getComboCuenta()                  
                
}//<<----------------------------------------------------- End Class Base_Dat_Cuenta

?>
