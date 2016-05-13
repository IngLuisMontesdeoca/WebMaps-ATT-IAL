<?php
/********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Metodos de la clase Contacto
    ********************************************************************************/

	//---- REQUIRES ----//
	//Clase Base
        require_once "Base.class.php";

	class Base_Dat_Contacto extends Base
	{//<<--------------------------------------------------------- Class Base_Cat_Contacto

                    /***
                *   @description:  Método constructor de la clase
                    *   @param:        id .- (int) Id de la llave primaria
                    *   @return:       void
                    *   @updater:      CG
                    *   @updated_date: 07/02/2014
                ***/
            function __construct( $id = 0 )
            {//<<------------------------------------------------------------ construct()
                        parent::__construct('dat_contacto', $id);
            }//<<-------------------------------------------------------- End construct()            
            
            /***
	    *   @description:  Método que obtiene la tabla de contactos
		*   @param:        void
		*   @return:       $tableContacto .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function getTableContacto($idPtn)//<<------------------------------------------------------------ getTableContacto()
            {
                
                if(is_null($idPtn))
                    return '<div class="cssResulTxt">No Se Encontraron Resultados</div>';
                
                $tableContacto = '';
                $tBody = '';
                $nRegistros = 0;
                $i=0;
                
                $this->_querySQL = "SELECT * FROM dat_contacto WHERE n_handset_id ={$idPtn} AND n_estatus_id = 3 ORDER BY c_contacto_nombre";
                
                $this->_execute($this->_querySQL);
                
                $nRegistros = $this->numRows();
                
                if($nRegistros > 0)
                {
                    for($i = 0; $i<$nRegistros; $i++)
                    {   
                       
                        if(strlen(trim($this->baseRs->fields['c_contacto_tipocontacto'])) == 0)
                            $this->baseRs->fields['c_contacto_tipocontacto'] = '1';
                        
                        switch($this->baseRs->fields['c_contacto_tipocontacto'])
                        {
                        
                            case '1':
                                $optionTipo = '<div>
                                                    <select id="cboTipoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="evtTipoContanctoEdit cssTxtAddUser cssInptTransp evtEnableEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" type="text" disabled>
                                                        <option selected="" value="1">.:: Telefono ::.</option>
                                                        <option value="2">.:: Correo ::.</option>
                                                    </select>
                                                    <div id="dvTipoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssOculta">Telefono</div>
                                                </div>
                                                </td>
                                                <td>
                                                    <div>
                                                    <input id="correoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssInptTransp evtEnableEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" disabled="disabled" type="text" value="'.$this->baseRs->fields['c_contacto_numerocorreo'].'" maxlength="12">
                                                        <div id="dvCorreoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssOculta">'.$this->baseRs->fields['c_contacto_numerocorreo'].'</div>
                                                    </div> 
                                                </td>
                                                <td>
                                                    <div id="regEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" class="cssBtnUEdit evtEditContacto" title="Editar"></div>
                                                </td>                                                        
                                            </tr>';
                            break;
                        
                            case '2':
                                $optionTipo = '<div>
                                                    <select id="cboTipoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="evtTipoContanctoEdit cssTxtAddUser cssInptTransp evtEnableEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" type="text" disabled>
                                                        <option value="1">.:: Telefono ::.</option>
                                                        <option selected="" value="2">.:: Correo ::.</option>
                                                    </select>
                                                    <div id="dvTipoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssOculta">Correo</div>
                                                </div>
                                                </td>
                                                <td>
                                                    <div>
                                                    <input id="correoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssInptTransp evtEnableEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" disabled="disabled" type="text" value="'.$this->baseRs->fields['c_contacto_numerocorreo'].'" maxlength="50">
                                                        <div id="dvCorreoContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssOculta">'.$this->baseRs->fields['c_contacto_numerocorreo'].'</div>
                                                    </div> 
                                                </td>
                                                <td>
                                                    <div id="regEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" class="cssBtnUEdit evtEditContacto" title="Editar"></div>
                                                </td>                                                        
                                            </tr>';                                
                            break;                        
                            
                        }
                        
                        $tBody.= '<tr id="regContacto_'.$this->baseRs->fields['n_contacto_id'].'" class="evtLineActiveContactos">
                                    <td>'.($i+1).'</td>
                                    <td>
                                        <div>
                                            <input name="chkAdmEqContacto" id="chkContacto_'.$this->baseRs->fields['n_contacto_id'].'" class="cssChckOne" type="checkbox" name="chkContactAdd" value="">
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input id="nombreContactoEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssInptTransp evtEnableEditContacto_'.$this->baseRs->fields['n_contacto_id'].'" disabled="disabled" type="text" value="'.$this->baseRs->fields['c_contacto_nombre'].'" maxlength="70">
                                            <div id="dvNombreContacotEdit_'.$this->baseRs->fields['n_contacto_id'].'" class="cssOculta">'.$this->baseRs->fields['c_contacto_nombre'].'</div>
                                        </div>
                                    </td>
                                    <td>
                                        '.$optionTipo;
                        $this->next();
                    }                    
                    
                    $tHeader = '<tr>
                                    <th class="filter-false cssEAddC01" data-placeholder="Filtro">#</th>
                                    <th class="filter-false sorter-false cssEAddC02" data-placeholder="Filtro"><input id="chkAdmContactosAll" type="checkbox" class="cssChckAll"/></th>
                                    <th class="cssEAddC03" data-placeholder="Filtro">Nombre</th>
                                    <th class="cssEAddC04" data-placeholder="Filtro">Tipo</th>
                                    <th class="cssEAddC05" data-placeholder="Filtro">Número / Correo</th>
                                    <th class="filter-false sorter-false cssEAddC06" data-placeholder="Filtro">Opciones</th>
                                </tr>';
                    
                    $tableContacto='<table id="tableAppContacts" class="cssEqTable tablesorter">
                                            <thead>
                                                '.$tHeader.'
                                            </thead>
                                            <tbody id="tbAddBodyContacts">
                                                '.$tBody.'
                                            </tbody> 
                                        </table>';
                    
                    return $tableContacto;
                }
                else
                    return '<div class="cssResulTxt">No Se Encontraron Resultados</div>';
            }//<<------------------------------------------------------------ end getComboActivity()
            
            /***
	    *   @description:  Método que obtiene inserta contactos
		*   @param:        void
		*   @return:       idRegistro .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function insertaContacto($data)//<<------------------------------------------------------------ insertaContacto()
            {
                

                    if($data['c_contacto_tipocontacto'] == '1')
                    {
                        $this->_querySQL = "INSERT INTO dat_contacto 
                                            (`c_contacto_nombre`
                                                , `c_contacto_numerocorreo`
                                                , `d_contacto_fechamodificacion`
                                                , `n_estatus_id`
                                                , `n_handset_id`) 
                                        VALUES 
                                            ('{$data['c_contacto_nombre']}',
                                            '{$data['c_contacto_numerocorreo']}',
                                            '{$data['d_contacto_fechamodificacion']}',
                                            '3',
                                            '{$data['n_handset_id']}')";
                    }
                    else
                    {
                        
                        $this->_querySQL = "INSERT INTO dat_contacto 
                                            (`c_contacto_nombre`
                                                , `c_contacto_numerocorreo`
                                                , `c_contacto_tipocontacto`
                                                , `d_contacto_fechamodificacion`
                                                , `n_estatus_id`
                                                , `n_handset_id`) 
                                        VALUES 
                                            ('{$data['c_contacto_nombre']}',
                                            '{$data['c_contacto_numerocorreo']}',
                                            '{$data['c_contacto_tipocontacto']}',
                                            '{$data['d_contacto_fechamodificacion']}',
                                            '3',
                                            '{$data['n_handset_id']}')";                        
                        
                    }
                    //var_dump($this->_querySQL);die();
                $this->_execute($this->_querySQL);
                
                return $this->getLastId();
                
            }            
            
            /***
	    *   @description:  Método que actualiza contactos
		*   @param:        void
		*   @return:       idRegistro .- (sting)
		*   @updater:      CG
		*   @updated_date: 07/02/2014
	    ***/                        
        
            public function actualizaContacto($data)//<<------------------------------------------------------------ insertaContacto()
            {
                
                    if($data['c_contacto_tipocontacto'] == '1')
                    {
                        $this->_querySQL = "UPDATE dat_contacto 
                                                SET 
                                                    `c_contacto_nombre`='{$data['c_contacto_nombre']}',
                                                    `c_contacto_numerocorreo`='{$data['c_contacto_numerocorreo']}',
                                                    `c_contacto_tipocontacto`=NULL,
                                                    `d_contacto_fechamodificacion`='{$data['d_contacto_fechamodificacion']}'
                                                    
                                                WHERE 
                                                    `n_contacto_id`='{$data['idContacto']}'";
                    }
                    else
                    {
                        $this->_querySQL = "UPDATE dat_contacto 
                                                SET 
                                                    `c_contacto_nombre`='{$data['c_contacto_nombre']}',
                                                    `c_contacto_numerocorreo`='{$data['c_contacto_numerocorreo']}',
                                                    `c_contacto_tipocontacto`='{$data['c_contacto_tipocontacto']}',
                                                    `d_contacto_fechamodificacion`='{$data['d_contacto_fechamodificacion']}'
                                                    
                                                WHERE 
                                                    `n_contacto_id`='{$data['idContacto']}'";
                        
                    }
                    //var_dump($this->_querySQL);die();
                
                if($this->_execute($this->_querySQL))
                    return true;
                else
                    return false;
                
            }                        
            
            
}//<<----------------------------------------------------- End Class Base_Cat_Activity

?>
