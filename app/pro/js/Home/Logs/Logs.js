$(document).ready(function() {
    $('#dvBtnLConsultar').on("click", obtenerActivity);
//    $('#chkLogsEnablDisabl').on("click", getLogsUsrsEnablDisabl);
    $('#dvLinkLogs01').on("click", getLogsUsrsEnablDisabl);
    $('#chkLogsEnablDisabl').on("click", logs_cambiarEstatus);
    $("#chkLogsEnablDisabl").prop("title", "Activos");
});

//globales
var logs_FLAGENDIS = true;

function Logs_cargarDataPicker()
{
    var timestamp = new Date();
    var cdia = timestamp.getDate() + "";
    var dia = (cdia.length == 1) ? "0" + cdia : cdia;
    var cmes = timestamp.getMonth() + 1 + "";
    var mes = (cmes.length == 1) ? "0" + cmes : cmes;
    var anio = timestamp.getFullYear();
    var fecha = anio + "-" + mes + "-" + dia;
    $("#txtLogsFechaFin").val(fecha + " 23:59");
    $("#txtLogsFechaInicio").val(fecha + " 00:00");
}


function limpiarDivLogs()
{
    $("#dvResultLogs, #dvLogsValidaciones").html("");
    $("#dvBtnLLimpiar").removeClass("cssLogsBtnLeft").addClass("cssLogsBtnBigDisabled");
    $('#dvBtnLLimpiar').off("click");
    
}

function obtenerActivity()
{
    if (!logs_compareFecha()) {
        $("#dvLogsValidaciones").html("La fecha inicial debe ser menor a la fecha final").addClass("cssValidation_Red");
    }
    else {
        var enablDisablUs;
        if (logs_FLAGENDIS) {
            enablDisablUs = 0;
        }
        else {
            enablDisablUs = 1;
        }
        limpiarDivLogs();
        $("#dvResultLogs").addClass('cssLoading01');
        var nombreUsuario = $("#selTipoUsuarioLogs option:selected").html();
        var nombreActivity = $("#selActividadesLogs option:selected").html();
        var param = {fechaIni: $('#txtLogsFechaInicio').val(), fechaFin: $('#txtLogsFechaFin').val(), actividad: $('#selActividadesLogs option:selected').val(), usuario: $('#selTipoUsuarioLogs option:selected').val(), enablDisablUs: enablDisablUs, nombreUsuario: nombreUsuario, nombreActivity: nombreActivity};
        $.post("ConsultarLog", param, respObtenerActivity);
    }
}

function respObtenerActivity(data) {
    $("#dvResultLogs").removeClass('cssLoading01');
    if (data == 0) {
        alert("Error al consultar informaci\xf3n, contacte al administrador");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        $('#dvResultLogs').html(data);
        admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumLogs").val();
        adminEquipos_inicializarTableSort("tblLogs", "dvPagerLogs", "cboPagNumLogs", "dvResultLogs");
        $("#dvBtnLLimpiar").removeClass("cssLogsBtnBigDisabled").addClass("cssLogsBtnLeft");
    $('#dvBtnLLimpiar').on("click", _limpiarLogs);
    }
}

/***
 *   @description:    Funcion para validar los horarios del reporte de logs
 *   @return:         val (bol) .- Resultado de la validacion  
 *   @updated:        LM
 *   @updated_date:   12/10/2011
 ***/
var logs_compareFecha = function() {

    var fechaFin = $("#txtLogsFechaFin").val();
    var fechaIni = $("#txtLogsFechaInicio").val();

    var arrFechaIniSep = fechaIni.split(" ");
    var arrFechaFinSep = fechaFin.split(" ");

    var arrFechaIni = arrFechaIniSep[0].split("-", 3);
    var arrFechaFin = arrFechaFinSep[0].split("-", 3);
    var arrHoraIni = arrFechaIniSep[1].split(":");
    var arrHoraFin = arrFechaFinSep[1].split(":");
    var val = false;

    if (arrFechaFin[0] > arrFechaIni[0]) {
        val = true;
    }
    else if (arrFechaFin[0] == arrFechaIni[0]) {
        if (arrFechaFin[0] == arrFechaIni[0]) {
            if (arrFechaFin[1] > arrFechaIni[1]) {
                val = true;
            }
            else if (arrFechaFin[1] == arrFechaIni[1]) {
                if (arrFechaFin[1] == arrFechaIni[1]) {
                    if (arrFechaFin[2] > arrFechaIni[2]) {
                        val = true;
                    }
                    else if (arrFechaFin[2] == arrFechaIni[2]) {
                        if (arrHoraFin[0] > arrHoraIni[0]) {
                            val = true;
                        }
                    }
                }
            }
        }
    }
    return val;
};


function _limpiarLogs()
{
    $("#selActividadesLogs option[value='0']").prop("selected", true);
    $("#chkLogsEnablDisabl").prop("checked", false);
    $("#chkLogsEnablDisabl").prop("title", "Activos");
    $("#dvResultLogs").html("");
    $("#dvLogsValidaciones").html("").removeClass("cssValidation_Red cssValidation_Green cssValidation_Yellow");
    $("#dvBtnLLimpiar").removeClass("cssLogsBtnLeft").addClass("cssLogsBtnBigDisabled");
    $('#dvBtnLLimpiar').off("click");
            $("#dvPagerLogs").html("");
    Logs_cargarDataPicker();
}


function getLogsUsrsEnablDisabl() {
    var param;
    if (logs_FLAGENDIS) {
        logs_FLAGENDIS = false;
        $("#dvLinkLogs01").html("Cambiar a Activos");
        param = {enablDisablUs: 1};
    }
    else {
        logs_FLAGENDIS = true;
        $("#dvLinkLogs01").html("Cambiar a Inactivos");
        param = {enablDisablUs: 0};
    }
    $.post("ActDesUsuario", param, respgetLogsUsrsEnablDisabl);
}

function respgetLogsUsrsEnablDisabl(data) {
    $("#selTipoUsuarioLogs").html(data);
}

function logs_cambiarEstatus() {
    var id = this.id;
    if ($("#" + id).is(':checked'))
        $("#" + id).prop("title", "Inactivos");
    else
        $("#" + id).prop("title", "Activos");
}