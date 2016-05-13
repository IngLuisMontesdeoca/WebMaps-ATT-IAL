<?php
error_reporting (5); 
    /********************************************************************************
    *   @autor:         Cesar Gonz·lez <cesar.gonzalez@webmaps.com.mx>                                  *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    P·gina principal del sistema                                                   *
    ********************************************************************************/
    
	//---- CONFIG ----//
	ini_set('memory_limit', '1500M');
	ini_set("max_execution_time","120"); 
	
	require_once '../config.php';
	require_once ROOT_ESTILOS;
	
	//---- Para evitar que se cargue la p√°gina cuando el usuario presiona el bot√≥n atr√°s del navegador  y para que la sesi√≥n no expire ----//
	header('Cache-Control: no-cache,no-store,max-age=0,s-maxage=0,must-revalidate');
	header("Pragma: no-cache");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header('Content-Type: text/html; charset=iso-8859-1');
	
	//---- PAQUETES ----//
		//---- BD ----//
		Package::usePackage('BD');
			//---- Tables ----//
			Package::import('tables');
			//---- Tables ----//
			Package::import('views');
		//---- Classes ----//
		Package::usePackage('Classes');
			//---- template ----//
			Package::import('Template');
			//---- debig ----//
			Package::import('Debug');
			//---- debig ----//
			Package::import('Cadenas');
	
	//---Validar inicio de sesion
	Session::goLogin();	
        
        //script java
        $arrVariables['SCRIPTSHEADER']="
        <link href='../../css/Home/jquery-ui-1.10.3.custom.css' type='text/css' rel='stylesheet'>
        <link href='../../css/timepicker-1.4.3.css' type='text/css' rel='stylesheet'> 
        <link href='../../css/tablesorter/css/jquery.tablesorter.pager.css' type='text/css' rel='stylesheet'>
        <link href='../../css/tablesorter/css/theme.default.css' type='text/css' rel='stylesheet'>	
        <link href='../../css/tablesorter/css/theme.blue.css' type='text/css' rel='stylesheet'>	
        
        <script type='text/javascript' src='../../js/jQuery/jquery-2.1.0.min.js'></script>
        <script type='text/javascript' src='../../js/jQuery/plugins/jquery-ui-1.10.3.js'></script>
        <script type='text/javascript' src='../../js/jQuery/plugins/timepicker-1.4.3.js'></script>
        <script type='text/javascript' src='../../js/tableSorter/js/jquery.tablesorter.js'></script>
        <script type='text/javascript' src='../../js/tableSorter/js/jquery.tablesorter.widgets.js'></script>
        <script type='text/javascript' src='../../js/tableSorter/js/widgets/jquery.tablesorter.pager.js'></script>		
        <script type='text/javascript' src='../../js/Home/Usiarios/adminUsuarios.js'></script>
        <script type='text/javascript' src='../../js/Home/Logs/Logs.js'></script>	
        <script type='text/javascript' src='../../js/Home/Reportes/reportes.js'></script>		
        <script type='text/javascript' src='../../js/validacionFormularios.js'></script>
        <script type='text/javascript' src='../../js/Home/tableSorter.js'></script>
		
        
        
       
            <script type='text/javascript'>
                var urlApp = 'http://".$_SERVER["SERVER_NAME"]."/';
                \$(document).ready(function(){
                                                
                (function(\$) {
                    \$.timepicker.regional['es'] = {
                        timeOnlyTitle: 'Elegir una hora',
                        timeText: 'Hora',
                        hourText: 'Horas',
                        minuteText: 'Minutos',
                        secondText: 'Segundos',
                        millisecText: 'Milisegundos',
                        timezoneText: 'Huso horario',
                        currentText: 'Ahora',
                        closeText: 'Cerrar',
                        timeFormat: 'hh:mm',
                        amNames: ['a.m.', 'AM', 'A'],
                        pmNames: ['p.m.', 'PM', 'P'],
                        ampm: false
                    };
                \$.timepicker.setDefaults(\$.timepicker.regional['es']);
                })(jQuery);

                \$('#txtReportesFechaInicio').datetimepicker({
                        controlType: 'select',
                        timeFormat: 'HH:mm'
                });

                \$('#txtReportesFechaFin').datetimepicker({
                        controlType: 'select',
                        timeFormat: 'HH:mm'
                });
                
                \$('#txtLogsFechaInicio').datetimepicker({
                        controlType: 'select',
                        timeFormat: 'HH:mm'
                });

                \$('#txtLogsFechaFin').datetimepicker({
                        controlType: 'select',
                        timeFormat: 'HH:mm'
                });

                \$('#txtEquiposFechaCorte').datetimepicker({
                        controlType: 'select',
                        showTimepicker: false
                });
                
                var f = new Date();
                var ano = 2013;
                var mes = 1;

                if(  ((f.getMonth()+1) -1) == 0 )
                {
                        ano = f.getFullYear() - 1;
                        mes = 11;
                }
                else
                {
                        ano = f.getFullYear();
                        mes = f.getMonth() - 1;
                }

                var fechaA = String( f.getFullYear()+ '-' + ((f.getMonth()+1).toString().length > 1 ? (f.getMonth()+1).toString() : '0'+(f.getMonth()+1).toString()) + '-' + (f.getDate().toString().length > 1 ? f.getDate().toString() : '0'+f.getDate().toString())); 

                \$('#txtLogsFechaInicio').val(fechaA+' '+'00:00');
                \$('#txtLogsFechaFin').val(fechaA+' '+'23:59');

                jQuery(function($){
                    \$.datepicker.regional['es'] = {
                    closeText: 'Cerrar',
                    prevText: '<Ant',
                    nextText: 'Sig>',
                    currentText: 'Hoy',
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'MiÈrcoles', 'Jueves', 'Viernes', 'S·bado'],
                    dayNamesShort: ['Dom','Lun','Mar','MiÈ','Juv','Vie','S·b'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S·'],
                    weekHeader: 'Sm',
                    dateFormat: 'yy-mm-dd',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    minDate: new Date(ano,mes,1),
                    maxDate: 'today',
                    defaultDate: 'today',
                    yearSuffix: ''};
                    \$.datepicker.setDefaults($.datepicker.regional['es']);
                    });                                                                         
                });
                
                setTimeout(function(){

                        $.post('SingleSession',
                                        function(data){
                                            if(data == 'SIN SESION')
                                                window.location.reload()
                                        }
                        );        

                },30000);
            </script>
        


"; 
        
        
        $arrVariables['SCRIPTSFOOTER']="<script type='text/javascript' src='../../../../js/Home/Equipos/adminEquipos.js'></script>";
        
        //EQUIPO
        //INSTANCIAS
        //$baseHandSet   = new Base_Dat_Handset();
        $catNetwork    = new Base_Cat_UserNetwork();
        $catEstatus    = new Base_Cat_Estatus();
        $baseCliente   = new Base_Dat_Cliente();
        $baseActivityLog = new Base_Dat_ActivityLog();
        $catUserType = new Base_Cat_UserType();
        $catTipoContrato = new Base_Cat_TipoContrato();
            
        $baseActivityLog->setPk(0);
        $baseActivityLog->c_activitylog_desc = 'Modulo Equipo';
        $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
        $baseActivityLog->n_activity_id = 15;
        $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
        $baseActivityLog->save() ? TRUE: FALSE;                
        
        //NAVEGADOR
        $arrVariables['TABLELOGS']='';
        $arrVariables['TABLEREPORTES']='';
        $arrVariables['scripts']='';
        switch($_SESSION['tipoUsuario'])
        {
            case '1':
            $arrayBuscar = array('{CLASSHTML}','{IDHTML}');
            $arrVariables['MNAVEGADOR'] = '<div class="cssLineNav">
                                                    <div class="cssLNLink evtSeccionModulo active" id="dvModEquipos"><a>Equipos</a></div>
                                                <div class="cssLNDiv"></div>

                                                <div class="cssLNLink evtSeccionModulo" id="dvModUsuarios"><a>Usuarios</a></div>
                                                <div class="cssLNDiv"></div>

                                                <div class="cssLNLink evtSeccionModulo" id="dvModLogs"><a>Logs</a></div>
                                                <div class="cssLNDiv"></div>
                                                <div class="cssLNLink evtSeccionModulo" id="dvModReportes"><a>Reportes</a></div>
                                                <div class="cssLNDiv"></div>


                                                <div title="Cerrar sesiÛn" id="dvModSesion" class="cssLNCerrar"></div>
                                            </div>';
            //USUARIO
            //INSTANCIAS
            $baseUsuario   = new Base_Dat_Usuario();
            $catTipoUsuario = new Base_Cat_TipoUsuarioAplicacion();

            //COMBO TIPO USUARIO
            $comboTipoUsuario = $catTipoUsuario->getComboTipoUsuario();
            $arrayReemplazar = array('cssTxtAddUser','cboTipoUser');
            $arrVariables['COMBOUSUARIOGUARDAR'] = str_replace($arrayBuscar, $arrayReemplazar, $comboTipoUsuario);        

            //TABLA DE RESULTADOS USUARIO
            $arrVariables['TABLEUSUARIO'] = $baseUsuario->getTableUsuario();


            //LOGS
            //INSTANCIAS
            $baseActivityLog = new Base_Dat_ActivityLog();
            $catActivity = new Base_Cat_Activity();
            //$baseUsuario = new Base_Dat_Usuario();

            $tmp = $arrayBuscar;
            array_push($tmp, '{ADDOPTION}');
            
            //COMBO ACTIVIDADES
            $comboActivity = $catActivity->getComboActivity();
            $arrayTmpReemplazar = array('cssTxtAddUser','selActividadesLogs','<option value = "0" selected>Todas las actividades</option><option value = "ing">Ingreso</option>');
            $arrVariables['COMBOACTIVIDADCONSULTAR'] = str_replace($tmp, $arrayTmpReemplazar, $comboActivity);                

            //COMBO USUARIOS
            $comboUsuario = $baseUsuario->getComboUsuarioLogin();
            $arrayTmpReemplazar = array('cssTxtAddUser cssSelLog001','selTipoUsuarioLogs','<option value = "0" selected>Todos los Usuarios Activos</option>');
            $arrVariables['COMBOUSUARIOLOGINCONSULTAR'] = str_replace($tmp, $arrayTmpReemplazar, $comboUsuario);                        


            //TABLA DE RESULTADOS LOGS
            //$arrVariables['TABLELOGS'] = $baseActivityLog->getTableActivityLog();                
            break;
        
            case '2':
            $arrVariables['TABLEUSUARIO'] = '';
            $arrVariables['COMBOUSUARIOGUARDAR'] = '';
            $arrVariables['COMBOUSUARIOLOGINCONSULTAR'] = '';
            $arrVariables['COMBOACTIVIDADCONSULTAR'] = '';
            $arrayBuscar = array('{CLASSHTML}','{IDHTML}');
            $arrVariables['MNAVEGADOR'] = '<div class="cssLineNav">
                                                    <div class="cssLNLink evtSeccionModulo" id="dvModEquipos"><a>Equipos</a></div>
                                                <div class="cssLNDiv"></div>

                                                <div title="Cerrar sesiÛn" id="dvModSesion" class="cssLNCerrar"></div>
                                            </div>';
            break;
        }
        
        
        //COMBO RED
        $comoUserNetwork = $catNetwork->getComboUserNetwork();
        
        $arrayBuscar = array('{CLASSHTML}','{IDHTML}');
        
        $tmp2 = $arrayBuscar;
        array_push($tmp2, '{ADDOPTION}');
        
        $arrayReemplazar = array('cssSelEq001','selEqu001','<option value="0" disabled selected>Seleccionar Red</option><option value = "Todos">Todos</option>');
        $arrVariables['COMBONETWORKCONSULTAR'] = str_replace($tmp2, $arrayReemplazar, $comoUserNetwork);
        
        $arrayReemplazar = array('cssSel100','selLRed','<option value="0" disabled selected>Seleccionar Red</option>');
        $arrVariables['COMBONETWORKGUARDAR'] = str_replace($tmp2, $arrayReemplazar, $comoUserNetwork);                
        
        //COMBO USERTYPE
        $comboUserType = $catUserType->getComboUserType();
        
        $tmp3 = $arrayBuscar;
        array_push($tmp3, '{ADDOPTION}');
        
        $arrayReemplazar = array('cssSelEq001','selEqu003','<option value="0" disabled selected>Seleccionar Plan</option><option value = "Todos">Todos</option>');
        $arrVariables['COMBONETPLANCONSULTAR'] = str_replace($tmp3, $arrayReemplazar, $comboUserType);
        
        $arrayReemplazar = array('cssSel100','selEqu004','<option value="0" disabled selected>Seleccionar Plan</option>');
        $arrVariables['COMBONETPLANGUARDAR'] = str_replace($tmp3, $arrayReemplazar, $comboUserType);
        
        //COMBO TIPOCONTRATO
        $comboTipoContrato = $catTipoContrato->getComboTipoContrato();
        
        $tmp4 = $arrayBuscar;
        array_push($tmp4, '{ADDOPTION}');
        
        $arrayReemplazar = array('cssSelEq001','selEqu005','<option value="0" disabled selected>Seleccionar Servicio</option><option value = "Todos">Todos</option>');
        $arrVariables['COMBOSERVICIOCONSULTAR'] = str_replace($tmp4, $arrayReemplazar, $comboTipoContrato);
        
        $arrayReemplazar = array('cssSel100','selEqu006','<option value="0" disabled selected>Seleccionar Servicio</option>');
        $arrVariables['COMBOSERVICIOGUARDAR'] = str_replace($tmp4, $arrayReemplazar, $comboTipoContrato);                
        
        //COMBO ESTATUS
        $comboEstatus = $catEstatus->getComboEstatus('3,6');
        
        $arrayReemplazar = array('cssSelEq001','selEqu002','<option value="0" disabled selected>Seleccionar Estatus</option><option value = "Todos">Todos</option>');
        $arrVariables['COMBOESTATUSCONSULTAR'] = str_replace($tmp2, $arrayReemplazar, $comboEstatus);
        
        //COMBO CLIENTE
        $comboCliente = $baseCliente->getComboCliente();
        
        $arrayReemplazar = array('cssSel100','selLEmpresa');
        $arrVariables['COMBOCLIENTEGUARDAR'] = str_replace($arrayBuscar, $arrayReemplazar, $comboCliente);
        
        //COMBO CUENTA
        $arrVariables['COMBOCUENTAGUARDAR'] = '<select class="cssSel100" id="selLCuenta"><option value="0" disabled selected>Seleccionar Cuenta</option></select>';
        
        $arrVariables['TABLEHANDSET']='';
                
	//--------------- XHTML VIEW ------------------//
	$XHTML = new Plantilla('Home/home.html');
	$XHTML->asignaVariables($arrVariables);
	$XHTML->construyeVista();
	$XHTML->getVista(true);
	//---------------------------------------------// 

?>
