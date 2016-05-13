<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase usuario
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_Usuario extends Base
	{//<<--------------------------------------------------------- Class Base_Dat_Usuario

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_usuario', $id);
            }//<<-------------------------------------------------------- End construct()  
            
            /***
	    *   @description:  Método que obtiene las usuarios
		*   @param:        void
		*   @return:       tableUsuario .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getTableUsuario(){//<<------------------------------------------------------------ getTableUsuario()
		    
                    $tableUsuario = '';
                    $tBody = '';
                    $i = 0;
                    $nRegistros = 0;
                    
                    $this->_querySQL = "SELECT 
                                            usuario.n_usuario_id id,
                                            usuario.c_usuario_nombre nombre,
                                            usuario.c_usuario_login login,
                                            usuario.c_usuario_email email,
                                            tusuario.c_tipousuario_desc tipo
                                        FROM
                                            dat_usuario usuario,
                                            cat_tipousuarioaplicacion tusuario
                                        WHERE
                                            usuario.n_tipousuario_id = tusuario.n_tipousuario_id
                                            AND usuario.n_estatus_id IN ('3','4','6')
                                        ORDER BY
                                            usuario.c_usuario_nombre
                    ";
								
		    $this->_execute($this->_querySQL);
			
                    $nRegistros = $this->numRows();
                    //var_dump($this->_querySQL);die();
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                                        $tBody.='<tr id="regUser_'.$this->baseRs->fields['id'].'" class="evtLineActiveUsuarios">
                                                            <td>'.($i+1).'</td>
                                                            <td><div><input id="chkregUser_'.$this->baseRs->fields['id'].'" value = "'.$this->baseRs->fields['login'].'" name="chkAdmUsr" type="checkbox" class="cssChckOne"></div></td>
                                                            <td><div><input id="txtNombreEditUser_'.$this->baseRs->fields['id'].'" type="text" class="cssInptTransp evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled="disabled" value = "'.$this->baseRs->fields['nombre'].'"/><div id="dvNombreEditUser_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['nombre'].'</div></div></td>
                                                            <td><div><input type="text" class="cssInptTransp" disabled="disabled" value = "'.$this->baseRs->fields['login'].'"/><div class="cssOculta">'.$this->baseRs->fields['login'].'</div></div></td>
                                                            <td><div><input id="txtEmailEditUser_'.$this->baseRs->fields['id'].'" type="text" class="cssInptTransp  evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled="disabled" value = "'.$this->baseRs->fields['email'].'"/><div id="dvEmailEditUser_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['email'].'</div></div></td>';
                                                            
                                        switch($this->baseRs->fields['tipo'])
                                        {
                                            case 'Administrador':
                                                $tBody.='<td><div><select id="cboTipoUserEdit_'.$this->baseRs->fields['id'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled>
                                                                        <option value="1" selected>'.$this->baseRs->fields['tipo'].'</option>
                                                                        <option value="2">Delegado</option>
                                                                  </select><div id="dvTipoUserEdit_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['tipo'].'</div></td>
                                                            <td><div id="regEditUser_'.$this->baseRs->fields['id'].'" class="cssBtnUEdit evtEdicionUser"></div></td>
                                                        </tr>';                                                    
                                            break;
                                        
                                            case 'Delegado';
                                                $tBody.='<td><div><select id="cboTipoUserEdit_'.$this->baseRs->fields['id'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled>
                                                                        <option value="1">Administrador</option>
                                                                        <option value="2" selected>'.$this->baseRs->fields['tipo'].'</option>
                                                                  </select><div id="dvTipoUserEdit_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['tipo'].'</div></td>
                                                            <td><div id="regEditUser_'.$this->baseRs->fields['id'].'" class="cssBtnUEdit evtEdicionUser"></div></td>
                                                        </tr>';                                                                                                    
                                            break;
                                        
                                            default:
                                            break;
                                        }
					$this->next();
			   }
                           
                    $tableUsuario.='<table class="tablesorter cssEqTable" id="tblser">
                                            <thead>
                                                <tr>
                                                    <th class="filter-false cssET01" data-placeholder="Filtro">#</th>
                                                    <th class="filter-false sorter-false cssET02" data-placeholder="Filtro"><input id="chkAdmUserAll" type="checkbox" class="cssChckAll"></th>
                                                    <th class="cssET03" data-placeholder="Filtro">Nombre</th>
                                                    <th class="cssET04" data-placeholder="Filtro">Login</th>
                                                    <th class="cssET05" data-placeholder="Filtro">E-Mail</th>
                                                    <th class="cssET06" data-placeholder="Filtro">Tipo</th>
                                                    <th class="filter-false sorter-false cssET07" data-placeholder="Filtro">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                '.$tBody.'
                                            </tbody>
                                    </table>';
                            return $tableUsuario;
			}
                        else
                            return '<div class="cssResulTxt">No Se Encontraron Resultados</div>';
		}//<<------------------------------------------------------------ End getTableUsuario()
                
            /***
	    *   @description:  Método que obtiene la tabla usuario por parametros
		*   @param:        keyPatron .- (string)
                *   @param:         criterio .- (string)
		*   @return:       tableUsuario .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
		public function getTableUsuarioConsulta($keyPatron = NULL, $criterio = NULL){//<<------------------------------------------------------------ getTableUsuarioConsulta()

                    $condition = '';
                    
                    if(is_null($keyPatron))
                        $keyPatron = '';
                        
                    if(!(is_null($criterio)))
                    {
                        switch($criterio)
                        {
                            case 'nombre':
                                $condition = "AND c_usuario_nombre LIKE '%{$keyPatron}%'";
                            break;
                        
                            case 'login':
                                $condition = "AND c_usuario_login LIKE '%{$keyPatron}%'";
                            break;
                        
                            case 'correo':
                                $condition = "AND c_usuario_email LIKE '%{$keyPatron}%'";
                            break;
                        
                        }
                    }
                    else
                        $criterio = '';
                    

                    $tableUsuario = '';
                    $tBody = '';
                    $i = 0;
                    $nRegistros =0;
                    
                    $this->_querySQL = "SELECT 
                                            usuario.n_usuario_id id,
                                            usuario.c_usuario_nombre nombre,
                                            usuario.c_usuario_login login,
                                            usuario.c_usuario_email email,
                                            tusuario.c_tipousuario_desc tipo
                                        FROM
                                            dat_usuario usuario,
                                            cat_tipousuarioaplicacion tusuario
                                        WHERE
                                            usuario.n_tipousuario_id = tusuario.n_tipousuario_id
                                            AND usuario.n_estatus_id IN ('3','4','6')
                                            {$condition}
                                        ORDER BY
                                            usuario.c_usuario_nombre                                                
                    ";
                        
		    $this->_execute($this->_querySQL);
			
                    $nRegistros = $this->numRows();
                    
			if($nRegistros > 0)
                        {
			   for($i=0; $i<$nRegistros; $i++)
                           {
                                        $tBody.='<tr id="regUser_'.$this->baseRs->fields['id'].'" class="evtLineActiveUsuarios">
                                                            <td>'.($i+1).'</td>
                                                            <td><div><input id="chkregUser_'.$this->baseRs->fields['id'].'" value = "'.$this->baseRs->fields['login'].'" name="chkAdmUsr" type="checkbox" class="cssChckOne"></div></td>
                                                            <td><div><input id="txtNombreEditUser_'.$this->baseRs->fields['id'].'" type="text" class="cssInptTransp evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled="disabled" value = "'.$this->baseRs->fields['nombre'].'"/><div id="dvNombreEditUser_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['nombre'].'</div></div></td>
                                                            <td><div><input type="text" class="cssInptTransp" disabled="disabled" value = "'.$this->baseRs->fields['login'].'"/><div class="cssOculta">'.$this->baseRs->fields['login'].'</div></div></td>
                                                            <td><div><input id="txtEmailEditUser_'.$this->baseRs->fields['id'].'" type="text" class="cssInptTransp  evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled="disabled" value = "'.$this->baseRs->fields['email'].'"/><div id="dvEmailEditUser_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['email'].'</div></div></td>';
                                                            
                                        switch($this->baseRs->fields['tipo'])
                                        {
                                            case 'Administrador':
                                                $tBody.='<td><div><select id="cboTipoUserEdit_'.$this->baseRs->fields['id'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled>
                                                                        <option value="1" selected>'.$this->baseRs->fields['tipo'].'</option>
                                                                        <option value="2">Delegado</option>
                                                                  </select><div id="dvTipoUserEdit_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['tipo'].'</div></td>
                                                            <td><div id="regEditUser_'.$this->baseRs->fields['id'].'" class="cssBtnUEdit evtEdicionUser"></div></td>
                                                        </tr>';                                                    
                                            break;
                                        
                                            case 'Delegado';
                                                $tBody.='<td><div><select id="cboTipoUserEdit_'.$this->baseRs->fields['id'].'" type="text" class="cssTxtAddUser cssInptTransp evtEnableEdit_'.$this->baseRs->fields['id'].'" disabled>
                                                                        <option value="1">Administrador</option>
                                                                        <option value="2" selected>'.$this->baseRs->fields['tipo'].'</option>
                                                                  </select><div id="dvTipoUserEdit_'.$this->baseRs->fields['id'].'" class="cssOculta">'.$this->baseRs->fields['tipo'].'</div></td>
                                                            <td><div id="regEditUser_'.$this->baseRs->fields['id'].'" class="cssBtnUEdit evtEdicionUser"></div></td>
                                                        </tr>';                                                                                                    
                                            break;
                                        
                                            default:
                                            break;
                                        }
					$this->next();
			   }
                           
                    $tableUsuario.='<table class="tablesorter cssEqTable" id="tblser">
                                            <thead>
                                                <tr>
                                                    <th class="filter-false cssET01" data-placeholder="Filtro">#</th>
                                                    <th class="filter-false sorter-false cssET02" data-placeholder="Filtro"><input id="chkAdmUserAll" type="checkbox" class="cssChckAll"></th>
                                                    <th class="cssET03" data-placeholder="Filtro">Nombre</th>
                                                    <th class="cssET04" data-placeholder="Filtro">Login</th>
                                                    <th class="cssET05" data-placeholder="Filtro">E-Mail</th>
                                                    <th class="cssET06" data-placeholder="Filtro">Tipo</th>
                                                    <th class="filter-false sorter-false cssET07" data-placeholder="Filtro">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                '.$tBody.'
                                            </tbody>
                                    </table>';
                    
                            return $tableUsuario;
			}
                        else
                            return '<div class="cssResulTxt">No Se Encontraron Resultados</div>';
		}//<<------------------------------------------------------------ End getTableUsuarioConsulta()

            /***
	    *   @description:  Método valida si existe usuario
		*   @param:        nombreUsuario .- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function isUsuario($emailUsuario='',$loginUsuario='')//<<------------------------------------------------------------ isUsuario()
                {
                    
                    /*$this->_querySQL = "SELECT n_usuario_id 
                                                FROM 
                                                    dat_usuario
                                                WHERE 
                                                    (c_usuario_login = '{$loginUsuario}'
                                                        OR c_usuario_email = '{$emailUsuario}')
                                                        AND n_estatus_id = 6
                                              ";*/
                    
                    $this->_querySQL = "SELECT n_usuario_id 
                                                FROM 
                                                    dat_usuario
                                                WHERE 
                                                    c_usuario_login = '{$loginUsuario}'
                                                        AND n_estatus_id IN ('3','4','6')
                                              ";                    
                                                        
                    $this->_execute($this->_querySQL);
                    
                    if($this->numRows()>0)
                        return '1';
                    else
                        return '0';
                }//<<------------------------------------------------------------ End isUsuario()      
                
            /***
	    *   @description:  Método que obtiene el combo usuarios con login
		*   @param:        void
		*   @return:       $comboUsuarioLogin .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getComboUsuarioLogin()//<<------------------------------------------------------------ getComboUsuarioLogin()
            {
                
                
                $nRegistros = 0;
                $options = '';
                $comboUsuarioLogin = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled selected>Seleccionar Usuario</option></select>';
                $i=0;
                
                $this->_querySQL = "SELECT * FROM dat_usuario WHERE n_estatus_id = 3 ORDER BY c_usuario_nombre";
                
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {   
                        $options.='<option value = "'.$this->baseRs->fields['n_usuario_id'].'">'.$this->baseRs->fields['c_usuario_nombre'].' ('.$this->baseRs->fields['c_usuario_login'].')</option>';
                        $this->next();
                    }
                    
                    if($options == '')
                        return $comboUsuarioLogin;
                    else
                    {
                        $comboUsuarioLogin = '<select class="{CLASSHTML}" id="{IDHTML}"><option value = "0" disabled>Seleccionar Usuario</option>{ADDOPTION}'.$options.'</select>';
                        return $comboUsuarioLogin;
                    }
                    
                }
                else
                    return $comboUsuarioLogin;
            }//<<------------------------------------------------------------ end getComboUsuarioLogin()  
            
            /***
	    *   @description:  Método valida si existe usuario login
		*   @param:        nombreUsuario .- (string)
                *   @param:         passwordUsuario.- (string)
		*   @return:       respuesta .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function isUsuarioLogin($loginUsuario='',$passwordUsuario='')//<<------------------------------------------------------------ isUsuarioLogin()
                { 
                    $this->_querySQL = "SELECT n_usuario_id,
                                                c_usuario_nombre,
                                                c_usuario_login,
                                                c_usuario_email,
                                                n_tipousuario_id
                                                FROM 
                                                    dat_usuario
                                                WHERE 
                                                    c_usuario_login = '{$loginUsuario}'
                                                        AND c_usuario_password = '{$passwordUsuario}'
                                                        AND n_estatus_id = 3
                                              ";
                                                        
                    $this->_execute($this->_querySQL);

                    if($this->numRows()==1)
                    {
                        
                        if(isset($_SESSION['intentos_session']))
                            $_SESSION['intentos_session'] = 0;  
                        
                        if(isset($_COOKIE["centos"]))
                            setcookie("centos", $_SESSION['intentos_session'], time()-7200);
                        
                        if(isset($_SESSION['intentos_session']))
                            $_SESSION['intentos_session'] = NULL;
                        
                        $_SESSION['idUsuario'] = utf8_encode($this->baseRs->fields['n_usuario_id']);
                        $_SESSION['nombreUsuario'] = utf8_encode($this->baseRs->fields['c_usuario_nombre']);
                        $_SESSION['loginUsuario'] = utf8_encode($this->baseRs->fields['c_usuario_login']);
                        $_SESSION['emailUsuario'] = utf8_encode($this->baseRs->fields['c_usuario_email']);
                        $_SESSION['tipoUsuario'] = utf8_encode($this->baseRs->fields['n_tipousuario_id']);
                        
                        return '1';
                    }
                    else
                    {
                        $this->_querySQL = "SELECT n_usuario_id,
                                                    c_usuario_login,
                                                    n_estatus_id
                                                    FROM 
                                                        dat_usuario
                                                    WHERE 
                                                        c_usuario_login = '{$loginUsuario}'
                                                            AND n_estatus_id IN ('3','4','6')
                                                  ";      
                        $this->_execute($this->_querySQL);
                        
                        if($this->numRows()==1)
                        {
                           if(((int)$this->baseRs->fields['n_estatus_id'] == '4') || ((int)$this->baseRs->fields['n_estatus_id'] == '6'))
                               return '2';
                            
                            if(isset($_COOKIE["centos"]))
                                $_SESSION['intentos_session'] = (int)$_COOKIE["centos"];

                            if(isset($_SESSION['intentos_session']))
                                        $_SESSION['intentos_session'] = $_SESSION['intentos_session'] + 1;
                                    else
                                        $_SESSION['intentos_session'] = 1;

                            setcookie("centos", $_SESSION['intentos_session'], time()+3600);
                            
                            if(isset($_SESSION['intentos_login']))
                            { 
                                if(is_null($_SESSION['intentos_login']))
                                     $_SESSION['intentos_login'] = $loginUsuario;
                                else
                                {
                                    if(($loginUsuario != $_SESSION['intentos_login']))
                                    {

                                        $_SESSION['intentos_session'] = 1;  

                                        setcookie("centos", $_SESSION['intentos_session'], time()-7200);
                                        
                                        $_SESSION['intentos_login'] = $loginUsuario;

                                    }
                                }
                            }
                            else
                            {
                                $_SESSION['intentos_login']=$loginUsuario;
                            }                            

                            if($_SESSION['intentos_session'] >= 10)
                            {
                                    $this->setPk ((int)$this->baseRs->fields['n_usuario_id']);
                                    $this->c_usuario_password = '';
                                    $this->n_estatus_id = 4;
                                    $isSave = $this->save() ? TRUE:FALSE;
                                    if($isSave)
                                    {
                                        setcookie("centos", $_SESSION['intentos_session'], time()-7200);
                                        $_SESSION['intentos_session'] = NULL;
                                        return '2';
                                    }
                                    else
                                        return '3|'.$_SESSION['intentos_session'];
                            }
                            else
                                return '3|'.$_SESSION['intentos_session'];
                        }              
                        else
                        {
                            $_SESSION['intentos_session'] = 0;
                            setcookie("centos", $_SESSION['intentos_session'], time()-7200);
                            return '0';
                        }
                    }
                }//<<------------------------------------------------------------ End isUsuario()                  
                
                
            /***
	    *   @description:  Método encuentra usuario por login
		*   @param:        emailUsuario .- (string)
		*   @return:       arrayInfoUsuario .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/
                
                public function getInfoUsuarioEmail($emailUsuario='')//<<------------------------------------------------------------ getInfoUsuarioEmail()
                {
                    $arrayInfoUsuario = array();
                    $this->_querySQL = "SELECT n_usuario_id,
                                                c_usuario_nombre,
                                                c_usuario_login,
                                                c_usuario_email
                                                FROM 
                                                    dat_usuario
                                                WHERE 
                                                    c_usuario_login = '{$emailUsuario}'
                                                        AND n_estatus_id IN ('3','4','6')
                                              ";
                                 //var_dump($this->_querySQL);die();                   
                    $this->_execute($this->_querySQL);
                    
                    $nRegistros = $this->numRows();
                    
                    if($nRegistros==1)
                    {
                        
                        $arrayInfoUsuario['id'] = $this->baseRs->fields['n_usuario_id'];
                        $arrayInfoUsuario['nombre'] = $this->baseRs->fields['c_usuario_nombre'];
                        $arrayInfoUsuario['login'] = $this->baseRs->fields['c_usuario_login'];
                        $arrayInfoUsuario['email'] = $this->baseRs->fields['c_usuario_email'];
                        
                        return $arrayInfoUsuario;
                    }
                    else
                    {
                        
                        $this->_querySQL = "SELECT  c_usuario_nombre
                                                    FROM 
                                                        dat_usuario
                                                    WHERE 
                                                        c_usuario_login = '{$emailUsuario}'
                                                            AND n_estatus_id IN ('3','4','6')
                                                  ";
                                     
                        $this->_execute($this->_querySQL);

                        $nRegistros = $this->numRows();

                        if($nRegistros==0)
                            return '2';
                        else
                            return '0';
                    }
                }//<<------------------------------------------------------------ End getInfoUsuarioEmail()   
                
            /***
	    *   @description:  Método que obtiene option usuarios activos o desactivos
		*   @param:        $actDesUsuario
		*   @return:       $optionActDesUsuario .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getOptionActDesUsuario($actDesUsuario = '')//<<------------------------------------------------------------ getOptionActDesUsuario()
            {
                
                $nRegistros = 0;
                $options = '';
                $optionActDesUsuario = '<option value = "0" disabled selected>Seleccionar Usuario</option>';
                $i=0;
                
                if($actDesUsuario == '0')
                    $this->_querySQL = "SELECT * FROM dat_usuario WHERE n_estatus_id IN ('3','4','6') ORDER BY c_usuario_nombre";
                else
                    $this->_querySQL = "SELECT * FROM dat_usuario WHERE n_estatus_id = 5 ORDER BY c_usuario_nombre";
                
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {   
                        $options.='<option value = "'.$this->baseRs->fields['n_usuario_id'].'">'.$this->baseRs->fields['c_usuario_nombre'].' ('.$this->baseRs->fields['c_usuario_login'].')</option>';
                        $this->next();
                    }
                    
                    if($options == '')
                        return $optionActDesUsuario;
                    else
                    {
                        $optionActDesUsuario = '<option value = "0" disabled>Seleccionar Usuario</option>{ADDOPTION}'.$options;
                        return $optionActDesUsuario;
                    }
                    
                }
                else
                    return $optionActDesUsuario;
            }//<<------------------------------------------------------------ end getOptionActDesUsuario()  
                
                
        
}//<<----------------------------------------------------- End Class Base_Dat_Usuario

?>
