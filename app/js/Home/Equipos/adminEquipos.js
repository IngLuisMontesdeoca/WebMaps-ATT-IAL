/*******************************************************************
 *																   *	
 *@autor:       (AC) Alberto Cepeda  <jorge.cepeda@webmps.com.mx>   *
 *@version:		1.0												   *
 *@created:		06/02/2014										   *
 *@descripcion:	Aprovisionamiento Equipos Nextel				   *
 *@notes:														   *
 *																   *	
 ********************************************************************/

//variabes globales
var admEquipos_FLAGCHECKHANDSET = true;
var admEquipos_NOMBREEMPRESA = "";
var admEquipos_NOMBRECUENTA = "";
var admEquipos_CONTADOREMPRESAS = 0;
var admEquipos_NUMEROREGISTROSVISIBLES = 10;
var adminEquipos_FLAGDELETE = 0;
var adminEquipos_PATRONBUSQUEDA = "";
var adminEquipos_CRITERIOBUSQUEDA = "";
var adminEquipos_IDEQUIPO = 0;


$(document).ready(function() {

    adminEquipos_inicializarTableSort("tblEquipos", "dvPagerEquipos", "cboPagNumEquipos");

    $("#chkAdmUserAll, .evtChkAdmUserAll").on("click", admUsuarios_selectAll);
    $("#chkAdmEquiposAll, .evtChkAdmEquiposAll").on("click", selecAllEquipos);
    $("#dvEEBtnSearch").on("click", adminEquipos_getInfoFilter);
    $("#dvEEBtnAll").on("click", function() {
        adminEquipos_getInfoEquipos("hideValidacion");
    });
    $("#dvAddEmpresa").on("click", adminEquipos_addInputEmpresa);
    $("#dvHideEmpresa").on("click", adminEquipos_hideInputEmpresa);

    $("#dvSaveEmpresa").on("click", adminEquipos_saveNewEmpresa);
    $("#dvAddCuenta01").click(adminEquipos_addInputCuenta);

    $("#dvSaveCuenta01").on("click", adminEquipos_saveNewCuenta);
    $(".evtSeccionModulo").on("click", homeModulos);
    $("#selLEmpresa").on("change", adminEquipos_LoadCuentas);
    $("#dvLAdd").on("click", adminEquipos_SaveEquipos);
    $(".evtDeleteEquipos").on("click", adminEquipos_DeleteHandsets);
    $("#dvEEBtnAdd").on("click", admEquipos_respshowAgregarEquipos);
    $("#dvSuspenderEquipo").on("click", adminEquipos_suspenderEquipos);
    $("#dvActivarEquipo").on("click", adminEquipos_activarEquipos);
    $("[name=chkAdmEqPTN]").on("click", adminEquipos_activaRegistro);
    $("#dvAddEClose").on("click", function() {
        $("#dvModEquipos").click();
        $("#spFechaCorte").hide()
    });
    $("#txtRRed, #txtREstatus, #txtRPlan, #txtRServicio").on("click", adminEquipos_EnableCboSearch);
    $("#txtRPTN, #txtRCuenta, #txtREmpresa").on("click", adminEqipos_disabledCboSeach);
    $(".evtOpenConfigHandset").on("click", adminEquipos_openConfiguracionTiempo);
    $("#dvLayerBtn").on("click", adminEquipos_saveConfiguracionTiempo);
    $(".evtSaveEditHandset").on("click", adminEquipos_saveEditHandset);
    $(".evtEditHandset").on("click", adminEquipos_enabledEdicion);
    $(".evtOpenContactosH").on("click", adminEquipos_openContactos);
    $("#dvAddContact").on("click", adminEquipos_saveContacto);
    $("#selAddCTipo").on("change", adminEquipos_changeTipoContacto);
    $("#aBtnCEliminar").on("click", adminEquipos_DeleteContactos);
    $(".evtEditContacto").on("click", adminEquipos_enabledEditContactos);
    adminequipos_cargarDataPicker();
    $("#selEqu006").on("change", sh_calendarEquipos);

    $(".etvValidatePlan").on("change", adminEquipos_validaPlan);
    $(".evtLoadCalendar").on("change", function(){adminEquipos_loadCutDate(this.id);});
    $("#selEqu004").on("change", adminEquipos_validaPlanNew);

});

function adminEquipos_activaRegistroContacto() {
    var id = this.id;
    var arrID = id.split("_");
    if ($("#" + id).is(':checked')) {
        $("#regContacto_" + arrID[1]).addClass("cssLineActive");
    }
    else {
        $("#regContacto_" + arrID[1]).removeClass("cssLineActive");
    }
}

function adminEquipos_changeTipoContacto() {
    var tipoContacto = $("#selAddCTipo").val();
    if (tipoContacto == "1") {
        $("#spAddCNumber").html("N\xfamero");
        $("#txtAddCNumber").val("").prop("maxlength", "12");
    }
    else {
        $("#spAddCNumber").html("Correo");
        $("#txtAddCNumber").val("").prop("maxlength", "50");
    }
}

function adminEquipos_changeTipoContactoEdit() {
    var arrId = this.id.split("_");
    var id = arrId[1];
    var tipoContacto = $("#" + this.id).val();
    if (tipoContacto == "1") {
        //$("#spAddCNumber").html("N\xfamero");
        $("#correoContactoEdit_" + id).prop("maxlength", "15");
    }
    else {
        //$("#spAddCNumber").html("Correo");		
        $("#correoContactoEdit_" + id).prop("maxlength", "50");
    }
}

function adminEqipos_disabledCboSeach() {
    $("#selEqu001, #selEqu002, #selEqu003, #selEqu005").prop("disabled", true);
    $("#txtEEBuscador").removeClass("cssOculta");
    $("#dvUComboRed, #dvUComboEstatus, #dvUComboPlan, #dvUComboServicio").addClass("cssOculta");
}

function adminEquipos_EnableCboSearch() {
    var idRadio = this.id;
    var idCboEnabled = "";
    adminEqipos_disabledCboSeach();
    $("#txtEEBuscador").addClass("cssOculta");
    if (idRadio == "txtRRed") {
        cboEnabled = "selEqu001";
        divcbo = "dvUComboRed";
    }

    if (idRadio == "txtREstatus") {
        cboEnabled = "selEqu002";
        divcbo = "dvUComboEstatus";
    }

    if (idRadio == "txtRPlan") {
        cboEnabled = "selEqu003";
        divcbo = "dvUComboPlan";
    }

    if (idRadio == "txtRServicio") {
        cboEnabled = "selEqu005";
        divcbo = "dvUComboServicio";
    }

    $("#" + cboEnabled + " option[value='0']").prop("selected", true);
    $("#" + cboEnabled).prop("disabled", false);
    $("#" + divcbo).removeClass("cssOculta");
}


/***
 *@descripcion:  Metodo para obtener informacion de equipo por patron de busqueda
 *@param:      
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   06/02/2014
 ***/
function adminEquipos_getInfoFilter() {
    var valPatron = jQuery.trim($("#txtEEBuscador").val());
    var criterio = $("input[name='radioEquipos']:checked").val();
    var cboPatron = "";
    $("#dvValidEq").html("").removeClass("cssValidation_Yellow cssValidation_Green cssValidation_Red");
    if (criterio == "red") {
        valPatron = $("#selEqu001").val();
        cboPatron = "selEqu001";
        if (valPatron == null) {
            $("#dvValidEq").html("Se debe seleccionar tipo de red").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
            return;
        }
    }
    if (criterio == "estatus") {
        valPatron = $("#selEqu002").val();
        cboPatron = "selEqu002";
        if (valPatron == null) {
            $("#dvValidEq").html("Se debe seleccionar un estatus").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
            return;
        }
    }
    if (criterio == "plan") {
        valPatron = $("#selEqu003").val();
        cboPatron = "selEqu003";
        if (valPatron == null) {
            $("#dvValidEq").html("Se debe seleccionar tipo de plan").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
            return;
        }
    }
    if (criterio == "servicio") {
        valPatron = $("#selEqu005").val();
        cboPatron = "selEqu005";
        if (valPatron == null) {
            $("#dvValidEq").html("Se debe seleccionar tipo de servicio").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
            return;
        }
    }

    if (valPatron.length == 0) {
        $("#dvValidEq").html("Se debe ingresar un criterio de b\xfasqueda").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
        return;
    }

    var msg = "";
    if (valPatron != null && valPatron.length > 0 && valPatron != "Ingrese criterio") {
        if (adm_validateField(valPatron, "empresa")) {
            $("#dvValidEq").html("Caracteres no v\xe1lidos").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
            return;
        }
        $("#dvContTablaEquipos").html("");
        $("#dvContTablaEquipos").addClass("cssLoading01");
        if (cboPatron.length > 0) {
            valPatron += "|" + $("#" + cboPatron + " option:selected").html();
        }
        var param = {keyPatron: valPatron, criterio: criterio};

		
        //$.post("ConsultarEquipo", param, adminEquipos_respGetInfoAll);
                callBackAjaxTablaEquipos = evtTablaEquipos;
		urlAdminEquipos = 'ConsultarEquipo?{filterList:filter}&{sortList:column}&pag={page}&tam={size}';
		paramAdminEquipos = param;	
		callBackAdminEquipos = adminEquipos_respGetInfoAll;
		admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumEquipos").val();
		adminEquipos_inicializarTableSort("tblEquipos", "dvPagerEquipos", "cboPagNumEquipos");		
		
		
    }
}


/***
 *@descripcion:  Metodo de respuesta para obtener los equipos de base de datos
 *@param:        (string) data: DOM con la informacion de los equipos
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   04/02/2014
 ***/
function adminEquipos_respGetInfoAll(data) {
	var respuesta = false;
	
    $("#tbAppendEmpresas").html("");
    $("#dvContTablaEquipos").removeClass("cssLoading01");
    if (data == 0) {
        alert("Error al consultar informaci\xf3n, contacte al administrador");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        //$("#dvContTablaEquipos").html(data);
		respuesta = true;
    }
    //admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumEquipos").val();
    //adminEquipos_inicializarTableSort("tblEquipos", "dvPagerEquipos", "cboPagNumEquipos");
    $("#chkAdmEquiposAll").on("click", selecAllEquipos);
    $("[name=chkAdmEqPTN]").on("click", adminEquipos_activaRegistro);
    $(".evtOpenConfigHandset").on("click", adminEquipos_openConfiguracionTiempo);
    $(".evtEditHandset").on("click", adminEquipos_enabledEdicion);
    $(".evtOpenContactosH").on("click", adminEquipos_openContactos);
    $(".etvValidatePlan").on("change", adminEquipos_validaPlan);
    $(".evtLoadCalendar").on("change", function(){adminEquipos_loadCutDate(this.id);});
	return respuesta;
}

var evtTablaEquipos = function()
{
    $("#chkAdmEquiposAll").on("click", selecAllEquipos);
    $("[name=chkAdmEqPTN]").on("click", adminEquipos_activaRegistro);
    $(".evtOpenConfigHandset").on("click", adminEquipos_openConfiguracionTiempo);
    $(".evtEditHandset").on("click", adminEquipos_enabledEdicion);
    $(".evtOpenContactosH").on("click", adminEquipos_openContactos);
    $(".etvValidatePlan").on("change", adminEquipos_validaPlan);
    $(".evtLoadCalendar").on("change", function(){adminEquipos_loadCutDate(this.id);});
};


/***
 *@descripcion:  Metodo para obtener todos los equipos
 *@param:        (string) data: DOM con la informacion de los equipos
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   04/02/2014
 ***/
function adminEquipos_getInfoEquipos(flagValidaciones) {
    $("#txtEEBuscador").val("");
    $("#txtRPTN").prop("checked", true);
    adminEqipos_disabledCboSeach();
    $("#dvContTablaEquipos").html("").addClass("cssLoading01");
    if (flagValidaciones == "hideValidacion") {
        $("#dvValidEq").html("").removeClass("cssValidation_Yellow cssValidation_Green cssValidation_Red");
    }
    //$.post("ConsultarEquipo", {todos: 1}, adminEquipos_respgetInfoEquipos);
        callBackAjaxTablaEquipos = evtTablaEquipos;
	urlAdminEquipos = 'ConsultarEquipo?todos=1&{filterList:filter}&{sortList:column}&pag={page}&tam={size}';
	paramAdminEquipos = {};	
	callBackAdminEquipos = adminEquipos_respgetInfoEquipos;
	admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumEquipos").val();
	adminEquipos_inicializarTableSort("tblEquipos", "dvPagerEquipos", "cboPagNumEquipos");
}


function adminEquipos_respgetInfoEquipos(data) {
	var respuesta = false;
	
    $("#tbAppendEmpresas").html("");
    $("#dvContTablaEquipos").removeClass("cssLoading01");
    if (data == 0) {
        alert("Error al consultar informaci\xf3n, contacte al administrador");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        //$("#dvContTablaEquipos").html(data);
		respuesta = true;
    }
    //$("#tblEquipos").trigger("update");
    //admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumEquipos").val();
    //adminEquipos_inicializarTableSort("tblEquipos", "dvPagerEquipos", "cboPagNumEquipos");
    $("#chkAdmEquiposAll").on("click", selecAllEquipos);
    $("[name=chkAdmEqPTN]").on("click", adminEquipos_activaRegistro);
    $(".evtOpenConfigHandset").on("click", adminEquipos_openConfiguracionTiempo);
    $(".evtEditHandset").on("click", adminEquipos_enabledEdicion);
    $(".evtOpenContactosH").on("click", adminEquipos_openContactos);
    $(".etvValidatePlan").on("change", adminEquipos_validaPlan);
	return respuesta;
}


/***
 *@descripcion:  Metodo que sirve para seleccionar o deseleccionar todos los equipos
 *@param:        
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   07/02/2014
 ***/
function selecAllEquipos() {
    if (admEquipos_FLAGCHECKHANDSET) {
        admEquipos_FLAGCHECKHANDSET = false;
        $("[name=chkAdmEqPTN]").prop('checked', true);
        $("#chkAdmEquiposAll, .evtChkAdmEquiposAll").attr('checked', 'checked');
        $(".evtLineActiveEquipos").addClass("cssLineActive");
    }
    else {
        admEquipos_FLAGCHECKHANDSET = true;
        $("[name=chkAdmEqPTN]").prop('checked', false);
        $("#chkAdmEquiposAll, .evtChkAdmEquiposAll").removeAttr('checked');
        $(".evtLineActiveEquipos").removeClass("cssLineActive");
    }
}


/***
 *@descripcion:  Metodo para eliminar varios equipos
 *@param:      
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   07/02/2014
 ***/
function adminEquipos_DeleteHandsets() {
    var arrPtns = new Array();
    var ptns = "";
    var nombrePTNs = "";
    var contadorPTNs = 0;
    arrPtns = $('[name=chkAdmEqPTN]:checked').toArray();
    for (i = 0; i < arrPtns.length; i++)
    {
        if ($('#regPTN_' + arrPtns[i].id).is(':visible')) {
            arrAux = arrPtns[i].id;
            auxNomPTN = arrPtns[i].value;

            ptns += arrAux + "|";
            nombrePTNs += auxNomPTN + "|";
            contadorPTNs++;
        }
    }
    ptns = ptns.substr(0, ptns.length - 1);
    nombrePTNs = nombrePTNs.substr(0, nombrePTNs.length - 1);
    if (ptns.length > 0) {
        if (confirm("Al dar clic en Aceptar, se eliminar\xe1n los " + contadorPTNs + " equipos seleccionados")) {
            var param = {PTNs: ptns, nombrePTNs: nombrePTNs};
            adminEquipos_FLAGDELETE = 1;
            $.post("EliminarEquipo", param, admEquipos_respDelete);
        }
    }
    else {
        alert("Se deben seleccionar los equipos que se desean eliminar");
    }
}

function admEquipos_respDelete(data) {
    if (data == 0) {
        alert("Error al eliminar, consulte al administrador");
    }
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    if (data == 1) {
//		adminEquipos_getInfoEquipos();
//		$("#tbAppendEmpresas").html("");
//		$("#dvAddEClose").click();
        $('.evtHide').hide();
        $('#dvModulo01').show();
        admEquipos_FLAGCHECKHANDSET = true;
        var flagRed = $("#txtRRed").is(':checked');
        var flagEstatus = $("#txtREstatus").is(':checked');
        var flagPlan = $("#txtRPlan").is(':checked');
        var flagServicio = $("#txtRServicio").is(':checked');

        if (jQuery.trim($("#txtEEBuscador").val()).length == 0 && !flagRed && !flagEstatus && !flagPlan && !flagServicio) {
            adminEquipos_getInfoEquipos("showValidacion");
        }
        else {
            adminEquipos_getInfoFilter();
        }
        if (adminEquipos_FLAGDELETE == 1) {
            $("#dvValidEq, #dvValidEqAlt").html("Los equipos seleccionados se eliminaron correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Yellow cssValidation_Red");
        }
        if (adminEquipos_FLAGDELETE == 2) {
            $("#dvValidEq").html("Los equipos seleccionados se suspendieron correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Yellow cssValidation_Red");
        }
        if (adminEquipos_FLAGDELETE == 3) {
            $("#dvValidEq").html("Los equipos seleccionados se reactivaron correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Yellow cssValidation_Red");
        }
        /*		var arrPtns = $('[name=chkAdmEqPTN]:checked').toArray();
         for (i=0;i<arrPtns.length;i++){
         Aux = arrPtns[i].id;
         $("#regPTN_"+Aux).remove();
         
         }*/
    }
}

function adminEquipos_saveNewEmpresa() {
    admEquipos_NOMBREEMPRESA = jQuery.trim($("#txtEmpresa").val());
    if (!adm_validateField(admEquipos_NOMBREEMPRESA, "nombre") || admEquipos_NOMBREEMPRESA.length == 0) {
        $("#dvValidEqAlt").html("Nombre de cliente no v\xe1lido").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
        return;
    }
    if (confirm("Al dar clic en Aceptar, se agregar\xe1 el cliente: " + admEquipos_NOMBREEMPRESA)) {
        $("#dvSaveEmpresa").removeClass("cssLESave").addClass("cssLoadSmall");
        $("#dvSaveEmpresa").off("click");
        $.post("GuardarCliente", {nombreEmpresa: admEquipos_NOMBREEMPRESA}, admEquipos_respSaveEmpresa);
    }
}

function admEquipos_respSaveEmpresa(data) {
    var clienteUpper = admEquipos_NOMBREEMPRESA.toUpperCase();
    if (data == 0) {
        $("#dvValidEqAlt").html("El cliente ya se encuentra en el cat\xe1logo").addClass("cssValidation_Yellow").removeClass("cssValidation_Red cssValidation_Green");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        $("#dvValidEqAlt").html("El cliente se guard\xf3 correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Red cssValidation_Yellow");
        $("#dvEmpresa02").addClass("cssOculta");
        $("#selLEmpresa").append('<option value="' + data + '" selected="selected">' + clienteUpper + '</option>');
        $("#selLCuenta").html("");
        $("#txtEmpresa").val("");
        adminEquipos_hideInputEmpresa();
    }
    $("#dvSaveEmpresa").removeClass("cssLoadSmall").addClass("cssLESave");
    $("#dvSaveEmpresa").on("click", adminEquipos_saveNewEmpresa);
}

function homeModulos() {
    var idModulo = this.id;
    if (idModulo == "dvModEquipos") {
        $("#dvModulo01").removeClass("cssOculta")
    }
    if (idModulo != "dvModReportes") {
        if(_estReportes){
            limpiarReportes();
        }
    }
}

function adminEquipos_addInputEmpresa() {
    $("#txtEmpresa").val("");
    $("#dvEmpresa02").removeClass("cssOculta");
    $("#dvAddEmpresa").removeClass("cssLEAdd").addClass("cssLERemove");
    $("#dvAddEmpresa").attr("id", "dvHideEmpresa");
    $("#selLEmpresa").prop("disabled", true);
    $("#dvAddEmpresa").off("click");
    $("#dvHideEmpresa").on("click", adminEquipos_hideInputEmpresa);
}

function adminEquipos_hideInputEmpresa() {
    $("#dvEmpresa02").addClass("cssOculta");
    $("#dvHideEmpresa").removeClass("cssLERemove").addClass("cssLEAdd");
    $("#dvHideEmpresa").attr("id", "dvAddEmpresa");
    $("#selLEmpresa").prop("disabled", false);
    $("#dvHideEmpresa").off("click");
    $("#dvAddEmpresa").on("click", adminEquipos_addInputEmpresa);
}

function adminEquipos_showAgregarEquipos() {
    $.post("cargarEmpresa", null, admEquipos_respshowAgregarEquipos);
}

function admEquipos_respshowAgregarEquipos() {
    admEquipos_CONTADOREMPRESAS = 0;
    adminEquipos_limpiarFormAlta();
    admEquipos_FLAGCHECKHANDSET = false;
    selecAllEquipos();

    $("#tbAppendEmpresas, #dvContTablaEquipos").html("");
    $('.evtHide').hide();
    $('#dvModulo02').show();
}

function adminEquipos_addInputCuenta() {
    $("#txtCuenta01").val("");
    $("#dvCuenta02").removeClass("cssOculta");
    $("#dvAddCuenta01").removeClass("cssLEAdd").addClass("cssLERemove");
    $("#dvAddCuenta01").attr("id", "dvHideCuenta01");
    $("#selLCuenta").prop("disabled", true);
    $("#dvAddCuenta01").off("click");
    $("#dvHideCuenta01").on("click", adminEquipos_hideInputCuenta);
}

function adminEquipos_hideInputCuenta() {
    $("#dvCuenta02").addClass("cssOculta");
    $("#dvHideCuenta01").removeClass("cssLERemove").addClass("cssLEAdd");
    $("#dvHideCuenta01").attr("id", "dvAddCuenta01");
    $("#selLCuenta").prop("disabled", false);
    $("#dvHideCuenta01").off("click");
    $("#dvAddCuenta01").on("click", adminEquipos_addInputCuenta);
}

function adminEquipos_saveNewCuenta() {
    var idEmpresa = jQuery.trim($("#selLEmpresa").val());
    admEquipos_NOMBRECUENTA = $("#txtCuenta01").val();
    var nombreEmpresa = $("#selLEmpresa option:selected").html();
    if (!adm_validateField(admEquipos_NOMBRECUENTA, "accountNumber") || admEquipos_NOMBRECUENTA.length == 0) {
        $("#dvValidEqAlt").html("Cuenta no v\xe1lida").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
        return;
    }
    if (idEmpresa == null) {
        $("#dvValidEqAlt").html("Se debe seleccionar una empresa, para asignar la cuenta").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
        return;
    }
    param = {nombreCuenta: admEquipos_NOMBRECUENTA, idEmpresa: idEmpresa, nombreEmpresa: nombreEmpresa};
    if (confirm("Al dar clic en Aceptar, se agregar\xe1 la cuenta: " + admEquipos_NOMBRECUENTA)) {
        $("#dvSaveCuenta01").removeClass("cssLESave").addClass("cssLoadSmall");
        $("#dvSaveCuenta01").off("click");
        $.post("GuardarCuenta", param, admEquipos_respSaveCuenta);
    }
}

function admEquipos_respSaveCuenta(data) {
    if (data == 0) {
        $("#dvValidEqAlt").html("La cuenta ya se encuentra asignada a la empresa").addClass("cssValidation_Yellow").removeClass("cssValidation_Red cssValidation_Green");
    }
    else if (data == "Otra") {
        $("#dvValidEqAlt").html("La cuenta ya se encuentra asignada a otra empresa").addClass("cssValidation_Yellow").removeClass("cssValidation_Red cssValidation_Green");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        $("#dvValidEqAlt").html("La cuenta se guard\xf3 correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Red cssValidation_Yellow");
        $("#dvCuenta02").addClass("cssOculta");
        $("#selLCuenta").append('<option value="' + data + '" selected="selected">' + admEquipos_NOMBRECUENTA + '</option>');
        $("#txtCuenta01").val("");
        adminEquipos_hideInputCuenta();
    }
    $("#dvSaveCuenta01").removeClass("cssLoadSmall").addClass("cssLESave");
    $("#dvSaveCuenta01").on("click", adminEquipos_saveNewCuenta);
}

function adminEquipos_LoadCuentas() {
    var idEmpresa = $("#selLEmpresa").val();
    var param = {idEmpresa: idEmpresa};
    $.post("CargarCuenta", param, adminEquipos_respLoadEmpresa);
}

function adminEquipos_respLoadEmpresa(data) {
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    $("#selLCuenta").html(data);
}


function adminEquipos_SaveEquipos() {
    var idEmpresa = $("#selLEmpresa").val();
    var idCuenta = $("#selLCuenta").val();
    var PTN = $("#txtPTN").val();
    var idRed = $("#selLRed").val();
    var idPlan = $("#selEqu004").val();
    var idServicio = $("#selEqu006").val();
    var flagValidacion = true;
    var nombreEmpresa = $("#selLEmpresa option:selected").html();
    var nombreCuenta = $("#selLCuenta option:selected").html();
    var nombreRed = $("#selLRed option:selected").html();
    var nombrePlan = $("#selEqu004 option:selected").html();
    var nombreServicio = $("#selEqu006 option:selected").html();
    var fechaCorte = $("#txtEquiposFechaCorte").val();

    var msg = "";
    if (idEmpresa == null) {
        flagValidacion = false;
        msg += "Seleccionar un cliente,"; /*$("#selLEmpresa").prop("title", "Seleccionar una empresa")*/
    }
    if (idCuenta == null) {
        flagValidacion = false;
        msg += " Seleccionar una cuenta,"; /*$("#selLCuenta").prop("title", "Seleccionar una cuenta")*/
    }
    if (!adm_validateField(PTN, "PTN"))
    {
        flagValidacion = false;
        msg += " PTN se requieren 12 d\xedgitos,"; /*$("#txtPTN").prop("title", "Caracteres no v\xe1lidos en PTN");*/
    }
    if (idRed == null) {
        flagValidacion = false;
        msg += " Seleccionar una red,"; /*$("#selLRed").prop("title", "Seleccionar una red")*/
    }
    if (idPlan == null) {
        flagValidacion = false;
        msg += " Seleccionar un plan,";
    }
    if (idServicio == null) {
        flagValidacion = false;
        msg += " Seleccionar un servicio,";
    }
    msg = msg.substr(0, msg.length - 1);

    if (flagValidacion) {
        var param = {idEmpresa: idEmpresa, idCuenta: idCuenta, PTN: PTN, idRed: idRed, idPlan: idPlan, idServicio: idServicio, nombreEmpresa: nombreEmpresa, nombreCuenta: nombreCuenta, nombreRed: nombreRed, nombrePlan: nombrePlan, nombreServicio: nombreServicio, fechaCorte: fechaCorte};
        $("#dvValidEqAlt").html("").addClass("cssValidation_Green").removeClass("cssValidation_Red cssValidation_Yellow");
        $("#tableAppendEmpresas").addClass("cssOculta");
        $("#dvContTabAppendEmpres").addClass("cssLoading01");        
        $.post("GuardarEquipo", param, adminEquipos_respSaveEquipos);
    }
    else {
        $("#dvValidEqAlt").html(msg).addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");
    }
}


function adminEquipos_respSaveEquipos(data) {
    $("#dvContTabAppendEmpres").removeClass("cssLoading01");
    $("#tableAppendEmpresas").removeClass("cssOculta");

    if (data == "0") {
        alert("Error al guardar, consulte al administrador");
    }
    else if (data == "Existe") {
        $("#dvValidEqAlt").html("El PTN ya se encuentra registrdo").addClass("cssValidation_Yellow").removeClass("cssValidation_Green cssValidation_Red");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        admEquipos_CONTADOREMPRESAS++;
        var idEmpresa = $("#selLEmpresa option:selected").html();
        var idCuenta = $("#selLCuenta option:selected").html();
        var nombrePlan = $("#selEqu004 option:selected").html();
        var nombreServicio = $("#selEqu006 option:selected").html();

        var fechaCorte = $("#txtEquiposFechaCorte").val();

        var PTN = $("#txtPTN").val();
        var idRed = $("#selLRed option:selected").html();
        var DOM = "<tr id='regPTN_" + data + "' class='evtLineActiveEquipos'><td>" + admEquipos_CONTADOREMPRESAS + "</td>";
        DOM += "<td><input id='" + data + "' type='checkbox' name='chkAdmEqPTN' class='cssChckOne'/></td>";
        DOM += "<td>" + idEmpresa + "</td>";
        DOM += "<td>" + idCuenta + "</td>";
        DOM += "<td>" + PTN + "</td>";
        DOM += "<td>" + idRed + "</td>";
        DOM += "<td>" + nombrePlan + "</td>";
        DOM += "<td>" + nombreServicio + "</td>";

        if (nombreServicio == 'Ilimitado') {
            DOM += "<td>" + fechaCorte + "</td>";
        } else {
            DOM += "<td>NA</td>";
        }

        DOM += "</tr>";
        $("#tbAppendEmpresas").append(DOM);
        adminEquipos_limpiarFormAlta();
        $("[name=chkAdmEqPTN]").on("click", adminEquipos_activaRegistro);
        $("#dvValidEqAlt").html("El equipo se guard\xf3 correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Yellow cssValidation_Red");
    }
}

function adminEquipos_suspenderEquipos() {
    var arrPtns = new Array();
    var ptns = "";
    var contadorPTNs = 0;
    var nombrePTNs = "";
    arrPtns = $('[name=chkAdmEqPTN]:checked').toArray();
    for (i = 0; i < arrPtns.length; i++)
    {
        if ($('#regPTN_' + arrPtns[i].id).is(':visible')) {
            arrAux = arrPtns[i].id;
            auxNomPTN = arrPtns[i].value;

            ptns += arrAux + "|";
            nombrePTNs += auxNomPTN + "|";
            contadorPTNs++;
        }
    }
    ptns = ptns.substr(0, ptns.length - 1);
    nombrePTNs = nombrePTNs.substr(0, nombrePTNs.length - 1);
    if (ptns.length > 0) {
        if (confirm("Al dar clic en Aceptar, se suspender\xe1n los " + contadorPTNs + " equipos seleccionados")) {
            var param = {PTNs: ptns, nombrePTNs: nombrePTNs};
            adminEquipos_FLAGDELETE = 2;
            $.post("SuspenderEquipo", param, admEquipos_respDelete);
        }
    }
    else {
        alert("Se deben seleccionar los equipos que se desean suspender");
    }
}



function adminEquipos_activarEquipos() {
    var arrPtns = new Array();
    var ptns = "";
    var contadorPTNs = 0;
    var nombrePTNs = "";
    arrPtns = $('[name=chkAdmEqPTN]:checked').toArray();
    for (i = 0; i < arrPtns.length; i++)
    {
        if ($('#regPTN_' + arrPtns[i].id).is(':visible')) {
            arrAux = arrPtns[i].id;
            auxNomPTN = arrPtns[i].value;

            ptns += arrAux + "|";
            nombrePTNs += auxNomPTN + "|";
            contadorPTNs++;
        }
    }
    ptns = ptns.substr(0, ptns.length - 1);
    nombrePTNs = nombrePTNs.substr(0, nombrePTNs.length - 1);
    if (ptns.length > 0) {
        if (confirm("Al dar clic en Aceptar, se activar\xe1n los " + contadorPTNs + " equipos seleccionados")) {
            var param = {PTNs: ptns, nombrePTNs: nombrePTNs};
            adminEquipos_FLAGDELETE = 3;
            $.post("ReactivarEquipo", param, admEquipos_respDelete);
        }
    }
    else {
        alert("Se deben seleccionar los equipos que se desean activar");
    }
}



function adminEquipos_activaRegistro() {
    var id = this.id;
    if ($("#" + id).is(':checked')) {
        $("#regPTN_" + id).addClass("cssLineActive");
    }
    else {
        $("#regPTN_" + id).removeClass("cssLineActive");
    }
}

function adminEquipos_limpiarFormAlta() {
    $("#selLCuenta option[value='0']").prop("selected", true);
    $("#selLEmpresa option[value='0']").prop("selected", true);
    $("#selLRed option[value='0']").prop("selected", true);
    $("#selEqu004 option[value='0']").prop("selected", true);
    $("#selEqu006 option[value='0']").prop("selected", true);
    $("#txtPTN, #txtEmpresa, #txtCuenta01").val("");
    $("#dvValidEqAlt").html("");
}

/***
 *@descripcion:  Metodo para hacer petición ajax y obtener el contenido de configuracion 
 *@param:       
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   26/02/2014
 ***/
function adminEquipos_openConfiguracionTiempo() {
    var arrID = this.id.split("_");
    var idPTN = arrID[1];
    adminEquipos_IDEQUIPO = idPTN;
    param = {idHandset: adminEquipos_IDEQUIPO};
    $.post("LayerConfiguracion", param, adminEquipos_respOpenConfiguracionTiempo);
}

function adminEquipos_respOpenConfiguracionTiempo(data) {
    var nombrePTN = $("#" + adminEquipos_IDEQUIPO).val();
    if (data == 0) {
        alert("Error al cargar la configuraci\xf3n, consulta al administrador");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        $("#dvLCBody").html(data);
        $("#dvBgLayer").show();
        $("#dvELConf").show();
        $("#dvELContact").hide();
        $("#spTextConfig").html("( PTN: " + nombrePTN + " )").show();
    }
}


function adminEquipos_saveConfiguracionTiempo() {
    var msjEnviados = $("#selMEnviados").val();
    var msjPeriodoEnvio = $("#selPEnvio").val();
    var nombreEnviados = $("#selMEnviados option:selected").html();
    var nombrePeriodoEnvio = $("#selPEnvio option:selected").html();
    var nombreHandset = $("#" + adminEquipos_IDEQUIPO).val();
    var param = {msjEnviados: msjEnviados, msjPeriodoEnvio: msjPeriodoEnvio, idHandset: adminEquipos_IDEQUIPO, nombreEnviados: nombreEnviados, nombrePeriodoEnvio: nombrePeriodoEnvio, nombreHandset: nombreHandset};
    $("#dvValidEq").html("");
    $.post("AgregarConfiguracion", param, adminEquipos_respSaveConfiguracionTiempo);
}

function adminEquipos_respSaveConfiguracionTiempo(data) {
    if (data == 0) {
        alert("Error al guardar la configuraci\xf3n, consulte al administrador");
    }
    if (data == 1) {
        //se cierra el formulario
        $("#dvELConf").hide();
        //se limpia el formulario
        $("#selMEnviados").val("");
        $("#selPEnvio").val("");
        $("#dvValidEq").html("La configuraci\xf3n se guard\xf3 correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Yellow cssValidation_Red");
    }
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
}

var _adminEquiposIdTMPEdit = '';
var _adminEquiposIdTMPEditP = '';
var _adminEquiposIdTMPEditS = '';
var _adminEquiposIdTMPEditFC = '';

function adminEquipos_enabledEdicion() {
   adminEquiposCancelEdicion();
    var idPTN = this.id;
    var arrId = idPTN.split("_");
    var id = arrId[1];
   _adminEquiposIdTMPEdit = id;
   _adminEquiposIdTMPEditP = $("#cboTipoPlanEdit_"+_adminEquiposIdTMPEdit).val();
   _adminEquiposIdTMPEditS = $("#cboTipoServicioEdit_"+_adminEquiposIdTMPEdit).val();
   _adminEquiposIdTMPEditFC = $("#txtEquipoFechaCorte_"+_adminEquiposIdTMPEdit).val();
    if( _adminEquiposIdTMPEditS == "1"){
        adminEquipos_loadCutDate(idPTN);
    }
    $(".evtEnableEditHandset_" + id).removeClass("cssInptTransp").addClass("cssInptEdit01").prop("disabled", false);
    $("#" + idPTN).removeClass("cssEEditConfig evtEditHandset").addClass("cssBtnUSave evtSaveEditHandset");
    $("#" + idPTN).off("click");
    $(".evtSaveEditHandset").on("click", adminEquipos_saveEditHandset);
}

function adminEquipos_saveEditHandset() {
    _adminEquiposIdTMPEdit = "";
    var idPTN = this.id;
    var arrId = idPTN.split("_");
    var id = arrId[1];
    var idPlan = $("#cboTipoPlanEdit_" + id).val();
    var idServicio = $("#cboTipoServicioEdit_" + id).val();
    var nombrePlan = $("#cboTipoPlanEdit_" + id + " option:selected").html();
    var nombreServicio = $("#cboTipoServicioEdit_" + id + " option:selected").html();
    var fechaCorte = $("#txtEquipoFechaCorte_"+id).val();
    var PTN = $("#" + id).val();
    var flagValidacion = true;
    var msg = "";

    $("#dvValidEq").html("");
    msg = msg.substr(0, msg.length - 1);

    if (flagValidacion) {
        $(".evtEnableEditHandset_" + id).removeClass("cssInptEdit01").addClass("cssInptTransp").prop("disabled", true);
        $("#" + idPTN).addClass("cssEEditConfig evtEditHandset").removeClass("cssBtnUSave evtSaveEditHandset");
        $("#" + idPTN).off("click");
        $(".evtEditHandset").on("click", adminEquipos_enabledEdicion);

        var param = {idPlan: idPlan, idPTN: id, PTN: PTN, idServicio: idServicio, nombrePlan: nombrePlan, nombreServicio: nombreServicio, fechaCorte:fechaCorte};
        $("#dvTipoServicioEdit_" + id).text($("#cboTipoServicioEdit_" + id + " option:selected").text());
        $("#dvTipoPlanEdit_" + id).text($("#cboTipoPlanEdit_" + id + " option:selected").text());

        $('#txtEquipoFechaCorte_'+id).attr("disabled",true);
        $(".ui-datepicker-close").click();
        
        if( idServicio == "2"){
            $('#txtEquipoFechaCorte_'+id).val("N/A");
        }
        
        $.post("EditarEquipo", param, adminEquipos_respSaveEditHandset);
    }
    else {
        $("#dvValidEq").html(msg).addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");
    }
}

function adminEquipos_respSaveEditHandset(data) {
    if (data == "1") {
        $("#tblEquipos").trigger("update");
        $("#dvValidEq").html("El equipo se edit\xf3 correctamente").removeClass("cssValidation_Red cssValidation_Yellow").addClass("cssValidation_Green");
    }
    if (data == "0") {
        alert("Error al editar el equipo, por favor contacte al administador");
    }
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
}

function adminEquipos_openContactos() {
    var arrID = this.id.split("_");
    var idPTN = arrID[1];
    var ptn = $("#" + idPTN).val();
    adminEquipos_CleanFormContacto();
    adminEquipos_IDEQUIPO = idPTN;
    $("#spTextContact").html("( PTN: " + ptn + " )");
    param = {idHandset: adminEquipos_IDEQUIPO};
    $.post("LayerContacto", param, adminEquipos_respOpenContactos);
}


function adminEquipos_respOpenContactos(data) {
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    $("#dvValidEq").html("");
    $("#dvAddTableContacts").html(data);
    admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumContacts").val();
    adminEquipos_inicializarTableSort("tableAppContacts", "dvPagAddContacts", "cboPagNumContacts");

    $("#dvBgLayer").show();
    $("#dvELContact").show();
    $("#dvELConf").hide();
    $("#txtAddCNumber").val("").attr("maxlength", "12");
    $(".evtEditContacto").on("click", adminEquipos_enabledEditContactos);
    $("[name=chkAdmEqContacto]").on("click", adminEquipos_activaRegistroContacto);
    $("#chkAdmContactosAll").on("click", adminEquipos_selectAllChkContacts);
    $(".evtTipoContanctoEdit").on("click", adminEquipos_changeTipoContactoEdit);
}
function adminEquipos_saveContacto() {
    var nombreContacto = jQuery.trim($("#txtAddCName").val());
    var tipoContacto = jQuery.trim($("#selAddCTipo").val());
    var numeroCorreo = jQuery.trim($("#txtAddCNumber").val());
    var nombreTipoContacto = $("#selAddCTipo option:selected").html();
    var idHandset = adminEquipos_IDEQUIPO;
    var nombrePtn = $("#" + adminEquipos_IDEQUIPO).val();

    var flagValidacion = true;
    var msg = "";
    $("#dvEqCValidation").html("");
    if (tipoContacto == 1) {
        nombreTipoContacto = nombreTipoContacto.substring(4, 12);
    }
    if (tipoContacto == 2) {
        nombreTipoContacto = nombreTipoContacto.substring(4, 10);
    }

    if (nombreContacto.length == 0 || !adm_validateField(nombreContacto, "nombre")) {
        flagValidacion = false;
        msg += "Nombre. S\xf3lo caracteres alfab\xe9ticos,";
    }
    if (tipoContacto == "1") {
        if (numeroCorreo.length == 0 || !adm_validateField(numeroCorreo, "telefono"))
        {
            flagValidacion = false;
            msg += "  Tel\xe9fono se requieren de  12 a 15 d\xedgitos,";
        }
    }
    else {
        if (!adm_validateField(numeroCorreo, "mail"))
        {
            flagValidacion = false;
            msg += " Correo electr\xf3nico,";
        }
    }
    //if(  tipoContacto == 0 ){ flagValidacion = false; msg += " Tipo de contacto,";}
    msg = msg.substr(0, msg.length - 1);

    if (flagValidacion) {
        var param = {nombreContacto: nombreContacto, tipoContacto: tipoContacto, numeroCorreo: numeroCorreo, nombreTipoContacto: nombreTipoContacto, idHandset: idHandset, nombrePtn: nombrePtn};
        $("#dvAddContact").off("click");
        $.post("AgregarContacto", param, adminEquipos_respSaveContacto);
    }
    else {
        $("#dvEqCValidation").html(msg).addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");
    }
}

function adminEquipos_respSaveContacto(data) {
    $("#dvAddContact").on("click", adminEquipos_saveContacto);
    if (data == "0") {
        $("#dvEqCValidation").html("Error al guardar el contacto, por favor consulta al administrador del sistema").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
        return;
    }
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    if (data == "1") {
        $("#dvEqCValidation").html("El contacto se guard\xf3 correctamente").removeClass("cssValidation_Red cssValidation_Yellow").addClass("cssValidation_Green");
        param = {idHandset: adminEquipos_IDEQUIPO};
        $.post("LayerContacto", param, adminEquipos_respOpenContactos);
    }
}

/***
 *@descripcion:  Metodo para eliminar varios contactos
 *@param:      
 *@return:
 *@update:       (AC) Jorge A. Cepeda
 *@update_date:   27/02/2014
 ***/
function adminEquipos_DeleteContactos() {
    var arrContacts = new Array();
    var idContacto = "";
    var nombreContacto = "";
    var contadorContactos = 0;
    $("#dvEqCValidation").html("");
    arrContacts = $('[name=chkAdmEqContacto]:checked').toArray();
    for (i = 0; i < arrContacts.length; i++)
    {
        arrIDAux = arrContacts[i].id.split("_");
        if ($('#regContacto_' + arrIDAux[1]).is(':visible')) {
            arrAux = arrIDAux[1];
            auxNomContact = $("#nombreContactoEdit_" + arrIDAux[1]).val();

            idContacto += arrAux + "|";
            nombreContacto += auxNomContact + "|";
            contadorContactos++;
        }
    }
    idContacto = idContacto.substr(0, idContacto.length - 1);
    nombreContacto = nombreContacto.substr(0, nombreContacto.length - 1);
    if (idContacto.length > 0) {
        if (confirm("Al dar clic en Aceptar, se eliminar\xe1n los " + contadorContactos + " contactos seleccionados")) {
            var param = {idContacto: idContacto, nombreContacto: nombreContacto};
//			adminEquipos_FLAGDELETE = 1;  
            $.post("EliminarContacto", param, admEquipos_respContacto);
        }
    }
    else {
        alert("Se deben seleccionar los contactos que se desean eliminar");
    }
}

function admEquipos_respContacto(data) {
    if (data == 0) {
        alert("Error al eliminar, consulte al administrador");
    }
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    if (data == 1) {
        $("#dvEqCValidation").html("El contacto se elimin\xf3 correctamente").removeClass("cssValidation_Yellow cssValidation_Red").addClass("cssValidation_Green");
        param = {idHandset: adminEquipos_IDEQUIPO};
        $.post("LayerContacto", param, adminEquipos_respOpenContactos);
    }
}


function adminEquipos_enabledEditContactos() {
    var idUser = this.id;
    var arrId = idUser.split("_");
    var id = arrId[1];
    $(".evtEnableEditContacto_" + id).removeClass("cssInptTransp").addClass("cssInptEdit01").prop("disabled", false);
    $("#" + idUser).removeClass("cssBtnUEdit evtEditContacto").addClass("cssBtnUSave evtSaveEditContacto");
    $("#" + idUser).off("click");
    $(".evtSaveEditContacto").on("click", adminEquipos_editContactos);
}


function adminEquipos_editContactos() {
    var idContacto = this.id;
    var arrId = idContacto.split("_");
    var id = arrId[1];
    var idPtn = adminEquipos_IDEQUIPO;
    var nombrePtn = $("#" + idPtn).val();
    var nombreContacto = jQuery.trim($("#nombreContactoEdit_" + id).val());
    var idTipo = $("#cboTipoContactoEdit_" + id).val();
    var nombreIdtipo = $("#cboTipoContactoEdit_" + id + " option:selected").html();
    var nombreTipo = jQuery.trim($("#correoContactoEdit_" + id).val());
    var flagValidacion = true;
    var msg = "";

    if (idTipo == 1) {
        nombreIdtipo = nombreIdtipo.substring(4, 12);
    }
    if (idTipo == 2) {
        nombreIdtipo = nombreIdtipo.substring(4, 10);
    }

    $("#dvEqCValidation").html("");

    if (nombreContacto.length == 0 || !adm_validateField(nombreContacto, "nombre")) {
        flagValidacion = false;
        msg += "Nombre. S\xf3lo caracteres alfab\xe9ticos,";
    }
    if (idTipo == "1") {
        if (nombreTipo.length == 0 || !adm_validateField(nombreTipo, "telefono"))
        {
            flagValidacion = false;
            msg += " Tel\xe9fono se requieren de  12 a 15 d\xedgitos,";
        }
    }
    else {
        if (!adm_validateField(nombreTipo, "mail"))
        {
            flagValidacion = false;
            msg += " Correo electr\xf3nico,";
        }
    }

    msg = msg.substr(0, msg.length - 1);

    if (flagValidacion) {
        $(".evtEnableEditContacto_" + id).removeClass("cssInptEdit01").addClass("cssInptTransp").prop("disabled", true);
        $("#" + idContacto).addClass("cssEEditConfig evtEditContacto").removeClass("cssBtnUSave evtSaveEditContacto");
        $("#" + idContacto).off("click");
        $(".evtEditContacto").on("click", adminEquipos_enabledEditContactos);

        var param = {idContacto: id, idPtn: idPtn, nombrePtn: nombrePtn, nombreContacto: nombreContacto, idTipo: idTipo, nombreIdtipo: nombreIdtipo, nombreTipo: nombreTipo};
        $("#dvNombreContacotEdit_" + id).text($("#nombreContactoEdit_" + id).val());
        $("#dvCorreoContactoEdit_" + id).text($("#correoContactoEdit_" + id).val());
        $("#dvTipoContactoEdit_" + id).text($("#cboTipoContactoEdit_" + id + " option:selected").text());

        $.post("EditarContacto", param, adminEquipos_respSaveEditContactos);
    }
    else {
        $("#dvEqCValidation").html(msg).addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");
    }
}

function adminEquipos_respSaveEditContactos(data) {
    if (data == "1") {
        $("#tableAppContacts").trigger("update");
        $("#dvEqCValidation").html("El contacto se edit\xf3 correctamente").removeClass("cssValidation_Red cssValidation_Yellow").addClass("cssValidation_Green");
    }
    if (data == "0") {
        $("#dvEqCValidation").html("Error al guardar el contacto, por favor consulta al administrador del sistema").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
    }
    if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
}


function adminEquipos_selectAllChkContacts() {
    if ($("#chkAdmContactosAll").is(':checked')) {
        $("[name=chkAdmEqContacto]").prop('checked', true);
        $(".evtLineActiveContactos").addClass("cssLineActive");
    }
    else {
        $("[name=chkAdmEqContacto]").prop('checked', false);
        $(".evtLineActiveContactos").removeClass("cssLineActive");
    }
}


function adminEquipos_CleanFormContacto() {
    $("#dvEqCValidation").html("");
    $("#txtAddCName, #txtAddCNumber").val("");
    $("#selAddCTipo option[value='1']").prop("selected", true);
    $("#spAddCNumber").html("N\xfamero");
    $("#txtEquiposFechaCorte,#spFechaCorte").hide();
}

function adminequipos_cargarDataPicker()
{
    var timestamp = new Date();
    var cdia = timestamp.getDate() + "";
    var dia = (cdia.length == 1) ? "0" + cdia : cdia;
    var cmes = timestamp.getMonth() + 1 + "";
    var mes = (cmes.length == 1) ? "0" + cmes : cmes;
    var anio = timestamp.getFullYear();
    var fecha = anio + "-" + mes + "-" + dia;
    var _hour = timestamp.getHours();
    var _minute = timestamp.getMinutes();
    $("#txtEquiposFechaCorte").val(fecha);
    $("#txtEquiposFechaCorte").hide();

}

function sh_calendarEquipos() {
    if ($("#selEqu006").val() == "1") {
        $("#txtEquiposFechaCorte,#spFechaCorte").show();
    } else {
        $("#txtEquiposFechaCorte,#spFechaCorte").hide();
    }
}

function adminEquipos_validaPlan() {
    if (this.value == '2') {
        $("#cboTipoServicioEdit_" + this.id.split("_")[1]).val(2);
        $("#cboTipoServicioEdit_" + this.id.split("_")[1]).attr("disabled", true);
        $('#txtEquipoFechaCorte_'+this.id.split("_")[1]).attr("disabled",true);
        $('#txtEquipoFechaCorte_'+this.id.split("_")[1]).val("N/A");
    } else {
        $("#cboTipoServicioEdit_" + this.id.split("_")[1]).attr("disabled", false);
    }
}

function adminEquipos_validaPlanNew(){
    if (this.value == '2') {
        $("#selEqu006").val(2);
        $("#selEqu006").attr("disabled", true);
        $("#txtEquiposFechaCorte,#spFechaCorte").hide();
    }else{
        $("#selEqu006").attr("disabled", false);     
        if($("#selEqu006").val() == "1"){
            $("#txtEquiposFechaCorte,#spFechaCorte").show(); 
        }
    }
}

function adminEquipos_loadCutDate(id) {
    if( $("#cboTipoServicioEdit_"+id.split("_")[1]).val() == '1'){
        $('#txtEquipoFechaCorte_'+id.split("_")[1]).attr("disabled",false);
        $('#txtEquipoFechaCorte_'+id.split("_")[1]).datetimepicker({
            controlType: 'select',
            showTimepicker: false
        });
    }else{
        $('#txtEquipoFechaCorte_'+id.split("_")[1]).attr("disabled",true);
    }
}

function adminEquiposCancelEdicion(){
    if( _adminEquiposIdTMPEdit != ""){
        $(".evtEnableEditHandset_" + _adminEquiposIdTMPEdit).removeClass("cssInptEdit01").addClass("cssInptTransp").prop("disabled", true);
        $("#dvEEditConfig_" + _adminEquiposIdTMPEdit).addClass("cssEEditConfig evtEditHandset").removeClass("cssBtnUSave evtSaveEditHandset");
        $("#dvEEditConfig_" + _adminEquiposIdTMPEdit).off("click");
        $(".evtEditHandset").off("click");    
        $(".evtEditHandset").on("click", adminEquipos_enabledEdicion);        
        $('#txtEquipoFechaCorte_'+_adminEquiposIdTMPEdit).attr("disabled",true);
        $("#cboTipoPlanEdit_"+_adminEquiposIdTMPEdit).val(_adminEquiposIdTMPEditP);
        $("#cboTipoServicioEdit_"+_adminEquiposIdTMPEdit).val(_adminEquiposIdTMPEditS);
        $("#txtEquipoFechaCorte_"+_adminEquiposIdTMPEdit).val(_adminEquiposIdTMPEditFC);        
        $(".ui-datepicker-close").click();
    }
}