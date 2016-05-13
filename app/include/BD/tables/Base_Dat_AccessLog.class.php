<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase accesslog
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_AccessLog extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_AccessLog

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_accesslog', $id);
            }//<<-------------------------------------------------------- End construct()
            
            /***
	    *   @description:  Método cierra sesiones antiguas
		*   @param:        idUsuario .- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function setCierraSessionOtraUbicacion($idUsuario='')//<<------------------------------------------------------------ setCierraSessionOtraUbicacion()
                {
                    
                    $tIdUsuario = $_SESSION['idUsuario'];
                    $tNombreUsuario = $_SESSION['nombreUsuario'];
                    $tLoginUsuario = $_SESSION['loginUsuario'];
                    $tEmailUsuario = $_SESSION['emailUsuario'];
                    $TtipoUsuario = $_SESSION['tipoUsuario'];
                        
                    $this->_querySQL = "SELECT n_acceslog_id,
                                                c_accesslog_sessionid
                                                FROM 
                                                    dat_accesslog
                                                WHERE 
                                                   d_accesslog_fechasalida = '0000-00-00 00:00:00'
                                                    AND n_usuario_id = '{$idUsuario}'
                                                ORDER BY 
                                                    d_accesslog_fechaingreso DESC
                                              ";
                  
                    $this->_execute($this->_querySQL);
                    
                    $nRegistros = $this->numRows();
                    
                    if($nRegistros>0)
                    {
                        for($i = 0; $i<$nRegistros; $i++)
                        {
                            
                            session_destroy(); session_id($this->baseRs->fields['c_accesslog_sessionid']); session_start(); session_destroy();
                            //setcookie("SESSION_NAME", $this->baseRs->fields['c_accesslog_sessionid'], time()-140000);
                            $this->setPk((int)$this->baseRs->fields['n_acceslog_id']);
                            $this->d_accesslog_fechasalida = date('Y-m-d H:i:s');
                            $this->save();
                                
                            $this->next();
                        }
                        
                        session_start(); 
                        session_regenerate_id();                                           
                        
                    }
                    
                    
                    $_SESSION['idUsuario'] = $tIdUsuario;
                    $_SESSION['nombreUsuario'] = $tNombreUsuario;
                    $_SESSION['loginUsuario'] = $tLoginUsuario;
                    $_SESSION['emailUsuario'] = $tEmailUsuario;
                    $_SESSION['tipoUsuario'] = $TtipoUsuario;
                    
                    return '1';

                }//<<------------------------------------------------------------ End setCierraSessionOtraUbicacion()
                
            /***
	    *   @description:  Método cierra sesiones antiguas mediante ajax
		*   @param:        idUsuario .- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function setCierraSessionOtraUbicacionAjax($idUsuario='')//<<------------------------------------------------------------ setCierraSessionOtraUbicacionAjax()
                {
                    $this->_querySQL = "SELECT 
                                            n_accesLog_id,
                                            d_accesslog_fechaingreso,
                                            c_accesslog_sessionid c_accesslog_sessionid
                                        FROM
                                            dat_accesslog
                                        WHERE
                                            d_accesslog_fechasalida = '0000-00-00 00:00:00'
                                                AND n_usuario_id = '{$idUsuario}'
                                        ORDER BY d_accesslog_fechaingreso DESC
                                              ";
                  
                    $this->_execute($this->_querySQL);
                    
                    $nRegistros = $this->numRows();
                    
                    if($nRegistros>1)
                    {
                        for($i = 1; $i<$nRegistros; $i++)
                        {
                            
                            session_id($this->baseRs->fields['c_accesslog_sessionid']); session_start(); session_destroy();
                            $this->setPk((int)$this->baseRs->fields['n_acceslog_id']);
                            $this->d_accesslog_fechasalida = date('Y-m-d H:i:s');
                            $this->save();
                                
                            $this->next();
                        }
                        session_start(); 
                        session_regenerate_id();                                           
                    }
                        return '1';

                }//<<------------------------------------------------------------ End setCierraSessionOtraUbicacionAjax()                                                  
                      
            
}//<<----------------------------------------------------- End Class Base_DatAccessLog

?>
