<?php
    /********************************************************************************
    *   @autor:         Luis Montes <luis.montes@webmaps.com.mx>              		*
    *   @version:       1.0                                     					*
    *   @created:       13/03/2014                              					*
    *   @copiright:     Copyright (c) 2014, WebMaps              					*
    *   @description    Metodos de acceso a la tabla dat_envioalarma                *
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_EnvioAlarma extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_EnvioAlarma

		/***
	    *   @description:  M�todo constructor de la clase
		*   @param:        id .- (int) Id de la llave primaria
		*   @return:       void
		*   @updater:      LM
		*   @updated_date: 13/03/2014
	    ***/
        function __construct( $id = 0 )
        {//<<------------------------------------------------------------ construct()
		    parent::__construct('dat_envioalarma', $id);
	    }//<<-------------------------------------------------------- End construct()
	       
		/***
	    *   @description:  M�todo para obtener las alarmas a enviar
		*   @param:        void
		*   @return:       arrAlarmas .- (string) Cadena con las alarmas
		*   @updater:      LM
		*   @updated_date: 13/03/2014
	    ***/
		public function getAlarmsToSend(){//<<------------------------------------------------------------ getAlarmsToSend()
                    $arrAlarmas = array();
                    $this->_querySQL = "SELECT n_handset_id,d_envioalarma_fechaproximo,n_envioalarma_mensajesenviados FROM {$this->_baseTbl} "
                                    . "WHERE (d_envioalarma_fechaproximo <= CURRENT_TIMESTAMP() OR n_envioalarma_mensajesenviados = 0) "
                                    . "AND n_estatus_id = 3 AND n_envioalarma_mensajesenviados < 4";
                    $this->_execute($this->_querySQL); 
			$strHandset = '';
			if($this->numRows() > 0){
	  		   for($i=0; $i<$this->numRows(); $i++){
                               $strHandset .= $this->baseRs->fields['n_handset_id'].',';
                               $arrAlarmas[$this->baseRs->fields['n_handset_id']]['fecha'] = $this->baseRs->fields['d_envioalarma_fechaproximo'];
                               $arrAlarmas[$this->baseRs->fields['n_handset_id']]['enviados'] = $this->baseRs->fields['n_envioalarma_mensajesenviados'];
                               $this->next();
			   }
			   $strHandset =  substr(  $strHandset , 0 ,  strlen($strHandset) - 1 );
			}
                     return array($strHandset,$arrAlarmas);
		}//<<-------------------------------------------------------- End getAlarmsToSend()
		   
		   
            /***
	    *   @description:  M�todo para actualizar los mensajes enviados de un handset
		*   @param:        $idHandset       .- (int)   id del handset
             *                     $numeroMensajes  .- (string) Numero de mensajes enviados
		*   @return:                        .- (bool) Resultado de la actualizacion
		*   @updater:      LM
		*   @updated_date: 14/03/2014
	    ***/
            public function updateEnvioByHandset($idHandset,$numeroMensajes)
            {//<<------------------------------------------------------------ updateEnvioByHandset()
                $minute = 5;
                if( $numeroMensajes == 1 ){
                    $minute = 15;
                }else if( $numeroMensajes == 2 ){
                    $minute = 30;
                }
                
                $proximoEnvio = ($numeroMensajes < 3)? "d_envioalarma_fechaproximo = DATE_ADD(d_envioalarma_fechaproximo, INTERVAL {$minute} MINUTE)," : "";
                $estatus      = ($numeroMensajes == 3)? "n_estatus_id = 4," : ""; 
                $this->_querySQL = "UPDATE  {$this->_baseTbl} "
				."SET       {$proximoEnvio}{$estatus}n_envioalarma_mensajesenviados = n_envioalarma_mensajesenviados+1 "
				."WHERE     n_handset_id = {$idHandset}";
		if( $this->_execute( $this->_querySQL ) )
                    return true;
		else 
                    return false;
            }//<<-------------------------------------------------------- End updateEnvioByHandset()
                
            /***
	    *   @description:  M�todo para cancelar el envio de mensajes de un equipo
		*   @param:        $idHandset       .- (int)   id del handset
		*   @return:                        .- (bool) Resultado de la actualizacion
		*   @updater:      LM
		*   @updated_date: 20/03/2014
	    ***/
            public function cancelEnvioByHandset($idHandset)
            {//<<------------------------------------------------------------ cancelEnvioByHandset()
                $this->_querySQL = "UPDATE  {$this->_baseTbl} SET n_estatus_id = 4 WHERE n_handset_id = {$idHandset}";
		if( $this->_execute( $this->_querySQL ) )
                    return true;
		else 
                    return false;
            }//<<-------------------------------------------------------- End cancelEnvioByHandset()
            
            
            /***
	    *   @description:  M�todo para activar el envio de mensajes de un equipo
		*   @param:        $idHandset       .- (int)   id del handset
		*   @return:                        .- (bool) Resultado de la actualizacion
		*   @updater:      LM
		*   @updated_date: 20/03/2014
	    ***/
            public function activateEnvioByHandset($idHandset)
            {//<<------------------------------------------------------------ activateEnvioByHandset()
                $this->_querySQL = "UPDATE {$this->_baseTbl} SET n_estatus_id = 3 WHERE n_handset_id = {$idHandset}";
		if( $this->_execute( $this->_querySQL ) )
                    return true;
		else 
                    return false;
            }//<<-------------------------------------------------------- End activateEnvioByHandset()
            
	}//<<----------------------------------------------------- End Class Base_Dat_EnvioAlarma

?>