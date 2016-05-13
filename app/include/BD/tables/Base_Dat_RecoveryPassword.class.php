<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase recoverupassword
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_RecoveryPassword extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_RecoveryPassword

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_recoverypassword', $id);
            }//<<-------------------------------------------------------- End construct()            
            

            /***
	    *   @description:  Método obtine la fecha de expiracion
		*   @param:        token .- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function getRecoveryFechaExpira($token)//<<------------------------------------------------------------ getRecoveryFechaExpira()
                {
                    
                    $this->_querySQL = "SELECT d_recovery_fechaexpiracion
                                                FROM 
                                                    dat_recoverypassword
                                                WHERE 
                                                    c_recovery_code = '{$token}'
                                                    AND d_recovery_fechaactivacion = '0000-00-00 00:00:00'
                                              ";
                                                    //var_dump($this->_querySQL);die();

                    $this->_execute($this->_querySQL);

                    if($this->numRows() == 1)
                        return $this->baseRs->fields['d_recovery_fechaexpiracion'];
                    else
                        return '0';
                }//<<------------------------------------------------------------ End getRecoveryFechaExpira()      
                
            /***
	    *   @description:  Método obtiene id usuario
		*   @param:        token .- (string)
		*   @return:       $arrRecovery .- (array)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function getRecoveryIdUsuario($token)//<<------------------------------------------------------------ getRecoveryIdUsuario()
                {
                    $arrRecovery = array();
                    $this->_querySQL = "SELECT n_usuario_id,
                                                n_recovery_id
                                                FROM 
                                                    dat_recoverypassword
                                                WHERE 
                                                    c_recovery_code = '{$token}'
                                              ";
                                                        
                    $this->_execute($this->_querySQL);

                    if($this->numRows() == 1)
                    {
                        $arrRecovery['idUsuario'] = $this->baseRs->fields['n_usuario_id'];
                        $arrRecovery['idRecovery'] = $this->baseRs->fields['n_recovery_id'];
                        return $arrRecovery;
                    }
                    else
                        return '0';
                }//<<------------------------------------------------------------ End getRecoveryIdUsuario()                      
                            
                      
            
}//<<----------------------------------------------------- End Class Base_Dat_RecoveryPassword

?>
