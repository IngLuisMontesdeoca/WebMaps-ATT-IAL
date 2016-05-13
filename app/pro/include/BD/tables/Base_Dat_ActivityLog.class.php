<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase activitylog
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_ActivityLog extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_ActivityLog

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_activitylog', $id);
            }//<<-------------------------------------------------------- End construct()  
            
            /***
	    *   @description:  Método que obtiene la tabla de activitylog
		*   @param:        void
		*   @return:       tableActivityLog .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getTableActivityLog(){//<<------------------------------------------------------------ getTableActivityLog()
		    
                    $tableActivityLog = '';
                    $tBody = '';
                    $i = 0;
                    $nRegistros = 0;
                    $fIni = date('Y-m-d')." 00:00:00";
                    $fFin = date('Y-m-d')." 23:59:59";
                    
                    $this->_querySQL = "SELECT 
                                            usuario.c_usuario_nombre nombreUsuario,
                                            usuario.c_usuario_login loginUsuario,
                                            access.d_accesslog_fechaingreso fIni,
                                            access.d_accesslog_fechasalida fFin,
                                            access.c_usuario_ip ipUsuario,
                                            access.c_accesslog_navegador navegadorAccess,
                                            activity.n_activitylog_id idActivity
                                        FROM
                                            dat_activitylog activity,
                                            dat_accesslog access,
                                            dat_usuario usuario
                                        WHERE
                                            activity.n_accesslog_id = access.n_acceslog_id
                                                AND access.n_usuario_id = usuario.n_usuario_id
                                                AND activity.d_activitylog_date BETWEEN '{$fIni}' AND '{$fFin}'
                                        ORDER BY
                                            access.d_accesslog_fechaingreso                                                    
                    ";
								
		    $this->_execute($this->_querySQL);
			
                    $nRegistros = $this->numRows();
                    
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                                        $tBody.='<tr>
                                                            <td>'.$this->baseRs->fields['nombreUsuario'].'</td>
                                                            <td>'.$this->baseRs->fields['loginUsuario'].'</td>
                                                            <td>'.$this->baseRs->fields['fIni'].'</td>
                                                            <td>'.$this->baseRs->fields['fFin'].'</td>
                                                            <td>'.$this->baseRs->fields['ipUsuario'].'</td>
                                                            <td>'.$this->baseRs->fields['navegadorAccess'].'</td>
                                                        </tr>';
					$this->next();
			   }
                           
                    $tableActivityLog.='<table class="tablesorter cssEqTable" id="tblLogs">
                                            <thead>
                                                <tr>
                                                    <th class="cssLogs01" data-placeholder="Filtro">Nombre</th>
                                                    <th class="cssLogs02" data-placeholder="Filtro">Login</th>
                                                    <th class="cssLogs03" data-placeholder="Filtro">Fecha inicio</th>
                                                    <th class="cssLogs04" data-placeholder="Filtro">Fecha fin</th>
                                                    <th class="cssLogs05" data-placeholder="Filtro">IP</th>
                                                    <th class="cssLogs06" data-placeholder="Filtro">Navegador</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                '.$tBody.'
                                            </tbody>
                                    </table>';
                    
                            return $tableActivityLog;
			}
                        else
                            return '<div class="cssResulTxt">No Se Encontraron Resultados</div>';
		}//<<------------------------------------------------------------ End getTableActivityLog()
                
            /***
	    *   @description:  Método que obtiene la tabla activitylog por parametros
		*   @param:        fIni .- (date)
                *   @param:        fFin .- (date)
                *   @param:        idActivity .- (date)
                *   @param:        nombreUsuario .- (string)
		*   @return:       tableActivityLog .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getTableActivityLogConsulta($fIni = NULL, $fFin = NULL, $idActivity = null, $idUsuario = NULL, $actDesUsuario = NULL){//<<------------------------------------------------------------ getTableActivityLogConsulta()
		    
                    $condition = '';
                    $cFIni = '';
                    $cIdActivity = '';
                    $cIdUsuario = '';
                    $cTable = '';
                    $cActDesUsuario = '';
                    $cGroupBy = '';
                    
                    if(is_null($fIni) || is_null($fFin))
                        $fIni = '';
                    else
                        $cFIni = " AND activity.d_activitylog_date BETWEEN '{$fIni}' AND '{$fFin}'";
                    
                    if(is_null($idActivity) || ((int)$idActivity == 0))
                    {
                        $cTable = $idActivity;
                        $idActivity = '';
                    }
                    else
                        $cIdActivity = "AND activity.n_activity_id = '{$idActivity}' ";
                    
                    if(is_null($idUsuario) || ((int)$idUsuario == 0))
                        $idUsuario = '';
                    else
                        $cIdUsuario = "AND usuario.n_usuario_id = '{$idUsuario}' ";
                        
                    if(is_null($actDesUsuario) || ((int)$actDesUsuario == 0))
                        $actDesUsuario = '';
                    else
                    {
                        if($actDesUsuario == '0')
                            $cActDesUsuario = "AND usuario.n_estatus_id IN ('3','4','6') ";
                        else
                            $cActDesUsuario = "AND usuario.n_estatus_id = 5 ";
                    }
                    
                    if($cTable == 'ing')
                    {
                        $cGroupBy = ' GROUP BY access.d_accesslog_fechaingreso                                        
                                        ORDER BY
                                            access.d_accesslog_fechaingreso';
                    }
                    else
                    {
                        $cGroupBy = ' ORDER BY activity.d_activitylog_date';                        
                    }
                        
                        
                    $condition = $cFIni.$cIdActivity.$cIdUsuario.$cActDesUsuario.$cGroupBy;
                    
                    

                    $tableActivityLog = '';
                    $tBody = '';
                    $i = 0;
                    $nRegistros =0;
                    
                    $this->_querySQL = "SELECT 
                                            usuario.n_usuario_id idUsuario,
                                            usuario.c_usuario_nombre nombreUsuario,
                                            usuario.c_usuario_login loginUsuario,
                                            access.d_accesslog_fechaingreso fIni,
                                            access.d_accesslog_fechasalida fFin,                                            
                                            access.c_usuario_ip ipUsuario,
                                            access.c_accesslog_navegador navegadorAccess,
                                            activity.d_activitylog_date fActivity,
                                            activity.n_activitylog_id idActivity,
                                            activity.c_activitylog_desc nombreActividadLog,
                                            cactivity.c_activity_desc nombreActividad
                                        FROM
                                            dat_activitylog activity,
                                            dat_accesslog access,
                                            dat_usuario usuario,
                                            cat_activity cactivity
                                        WHERE
                                            activity.n_accesslog_id = access.n_acceslog_id
                                            AND access.n_usuario_id = usuario.n_usuario_id
                                            AND cactivity.n_activity_id = activity.n_activity_id
                                            AND cactivity.n_estatus_id = 3
                                            {$condition} 
                    ";
			//var_dump($this->_querySQL);die();
		    $this->_execute($this->_querySQL);
			
                    $nRegistros = $this->numRows();
                    
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                                        if($cTable == 'ing')
                                        {
                                            $tBody.='<tr>
                                                                <td>'.$this->baseRs->fields['nombreUsuario'].'</td>
                                                                <td>'.$this->baseRs->fields['loginUsuario'].'</td>
                                                                <td>'.$this->baseRs->fields['fIni'].'</td>
                                                                <td>'.$this->baseRs->fields['fFin'].'</td>
                                                                <td>'.$this->baseRs->fields['ipUsuario'].'</td>
                                                                <td>'.$this->baseRs->fields['navegadorAccess'].'</td>
                                                            </tr>';
                                            $this->next();
                                        }
                                        else
                                        {
                                            $tBody.='<tr>
                                                                <td>'.$this->baseRs->fields['nombreUsuario'].'</td>
                                                                <td>'.$this->baseRs->fields['loginUsuario'].'</td>
                                                                <td>'.$this->baseRs->fields['nombreActividad'].'</td>
                                                                <td>'.$this->baseRs->fields['fActivity'].'</td>
                                                                <td>'.$this->baseRs->fields['nombreActividadLog'].'</td>
                                                            </tr>';
                                            $this->next();                                            
                                        }
			   }
                     if($cTable == 'ing')
                     {
                            $tableActivityLog.='<table class="tablesorter cssEqTable" id="tblLogs">
                                                    <thead>
                                                        <tr>
                                                            <th class="cssLogs01" data-placeholder="Filtro">Nombre</th>
                                                            <th class="cssLogs02" data-placeholder="Filtro">Login</th>
                                                            <th class="cssLogs03" data-placeholder="Filtro">Fecha inicio</th>
                                                            <th class="cssLogs04" data-placeholder="Filtro">Fecha fin</th>
                                                            <th class="cssLogs05" data-placeholder="Filtro">IP</th>
                                                            <th class="cssLogs06" data-placeholder="Filtro">Navegador</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        '.$tBody.'
                                                    </tbody>
                                            </table>';
                     }
                     else
                     {
                            $tableActivityLog.='<table class="tablesorter cssEqTable" id="tblLogs">
                                                    <thead>
                                                        <tr>
                                                            <th class="cssLogs01" data-placeholder="Filtro">Nombre</th>
                                                            <th class="cssLogs02" data-placeholder="Filtro">Login</th>
                                                            <th class="cssLogs03" data-placeholder="Filtro">Actividad</th>
                                                            <th class="cssLogs04" data-placeholder="Filtro">Hora</th>
                                                            <th class="cssLogs05" data-placeholder="Filtro">Descripción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        '.$tBody.'
                                                    </tbody>
                                            </table>';       
                     }
                    
                            return $tableActivityLog;
			}
                        else
                            return '<div class="cssResulTxt">No Se Encontraron Resultados</div>';
		}//<<------------------------------------------------------------ End getTableActivityLogConsulta()


}//<<----------------------------------------------------- End Class Base_Dat_Usuario

?>
