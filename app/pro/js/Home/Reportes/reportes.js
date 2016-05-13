$(document).ready(function() {
    $('#dvBtnLConsultarReportes').on("click", obtenerReporte);
    /*$('#dvBtnLLimpiarReportes').on("click", limpiarReportes);
    $('#dvBtnExportXLS').on("click", exportarReporte);
    $('#dvBtnExportTXT').on("click", exportarReporte);*/
});

function Reportes_cargarDataPicker()
{
    var timestamp = new Date();
    var cdia = timestamp.getDate() + "";
    var dia = (cdia.length == 1) ? "0" + cdia : cdia;
    var cmes = timestamp.getMonth() + 1 + "";
    var mes = (cmes.length == 1) ? "0" + cmes : cmes;
    var anio = timestamp.getFullYear();
    var fecha = anio + "-" + mes + "-" + dia;
    $("#txtReportesFechaInicio").val(fecha + " 00:00");
    $("#txtReportesFechaFin").val(fecha + " 23:59");
}

var _estReportes = false;

var evtTablaReportes = function()
{

}

var obtenerReporte = function() {
    if (!reportes_compareFecha()) {
        $("#dvReportesValidaciones").html("La fecha inicial debe ser menor a la fecha final").addClass("cssValidation_Red");
    }
    else {
        if ($("#selTipoReporte").val() == "0") {
            alert("Seleccione el tipo de reporte");
            return;
        }
            $("#dvBtnExportXLS,#dvBtnExportTXT").removeClass("cssLogsBtnSmall").addClass("cssLogsBtnSmallDisabled");
            $("#dvBtnLLimpiarReportes").removeClass("cssLogsBtnLeft").addClass("cssLogsBtnBigDisabled");
            $('#dvBtnLLimpiarReportes').off("click");
            $('#dvBtnExportXLS').off("click");
            $('#dvBtnExportTXT').off("click");
            _estReportes = false;
        var enablDisablUs;
        if (logs_FLAGENDIS) {
            enablDisablUs = 0;
        }
        else {
            enablDisablUs = 1;
        }
        $("#dvResultReportes").addClass('cssLoading01');
        var tipoReporte = $("#selTipoReporte").val();
        var param = {fechaIni: $('#txtReportesFechaInicio').val(), fechaFin: $('#txtReportesFechaFin').val(), tipoReporte: tipoReporte};
        //$.post("ConsultarCobros", param, respObtenerReporte);
        callBackAjaxTablaEquipos = evtTablaReportes;
        urlAdminEquipos = 'ConsultarCobros?{filterList:filter}&{sortList:column}&pag={page}&tam={size}';
        paramAdminEquipos = param;
        callBackAdminEquipos = respObtenerReporte;
        admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumReporte").val();
        adminEquipos_inicializarTableSort("tblReportes", "dvPagerReportes", "cboPagNumReporte", "dvResultReportes");
    }
};


function respObtenerReporte(data) {
    var respuesta = false;
    $("#dvResultReportes").removeClass('cssLoading01');
    if (data == 0) {
        alert("Error al consultar informaci\xf3n, contacte al administrador");
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else {
        if (parseInt(paramAdminEquipos.tipoReporte) == 1) {
            $("#tBodyTblReportesMensaje").html(tableReportesMensajeIniHtml);
            $("#dvBtnExportXLS,#dvBtnExportTXT").removeClass("cssLogsBtnSmallDisabled").addClass("cssLogsBtnSmall");
            $("#dvBtnLLimpiarReportes").removeClass("cssLogsBtnBigDisabled").addClass("cssLogsBtnLeft");            
            $('#dvBtnLLimpiarReportes').on("click", limpiarReportes);
            $('#dvBtnExportXLS').on("click", exportarReporte);
            $('#dvBtnExportTXT').on("click", exportarReporte);
            _estReportes = true;
        } else if (parseInt(paramAdminEquipos.tipoReporte) == 2) {
            $("#tBodyTblReportesPayment").html(tableReportesPaymentIniHtml);
            $("#dvBtnExportXLS,#dvBtnExportTXT").removeClass("cssLogsBtnSmallDisabled").addClass("cssLogsBtnSmall");
            $("#dvBtnLLimpiarReportes").removeClass("cssLogsBtnBigDisabled").addClass("cssLogsBtnLeft");
            $('#dvBtnLLimpiarReportes').on("click", limpiarReportes);
            $('#dvBtnExportXLS').on("click", exportarReporte);
            $('#dvBtnExportTXT').on("click", exportarReporte);
            _estReportes = true;
        } else
        {
            alert('Selecciona el tipo de reporte');
            return;
        }

        //$('#dvResultReportes').html(data);        
        //admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumReporte").val();
        //adminEquipos_inicializarTableSort("tblReportes", "dvPagerReportes", "cboPagNumReporte","dvResultReportes"); 
        respuesta = true;
    }
    return respuesta;
}

var exportarReporte = function() {
    var tipoReporte = $("#selTipoReporte").val();
    var param = "?fechaIni=" + $('#txtReportesFechaInicio').val() + "&fechaFin=" + $('#txtReportesFechaFin').val() + "&tipoReporte=" + tipoReporte + "&f=" + this.id;
    document.location.href = urlApp + "ExportarCobros" + param;
};

var limpiarReportes = function() {
    $("#selTipoReporte option[value='0']").prop("selected", true);
    $("#dvResultReportes").html("");
    $("#dvReportesValidaciones").html("").removeClass("cssValidation_Red cssValidation_Green cssValidation_Yellow");
            $("#dvBtnExportXLS,#dvBtnExportTXT").removeClass("cssLogsBtnSmall").addClass("cssLogsBtnSmallDisabled");
            $("#dvBtnLLimpiarReportes").removeClass("cssLogsBtnLeft").addClass("cssLogsBtnBigDisabled");
            $('#dvBtnLLimpiarReportes').off("click");
            $('#dvBtnExportXLS').off("click");
            $('#dvBtnExportTXT').off("click");
            $("#dvPagerReportes").html("");
            _estReportes = false;
    Reportes_cargarDataPicker();
};

/***
 *   @description:    Funcion para validar los horarios del reporte de logs
 *   @return:         val (bol) .- Resultado de la validacion  
 *   @updated:        LM
 *   @updated_date:   12/10/2011
 ***/
var reportes_compareFecha = function() {

    var fechaFin = $("#txtReportesFechaFin").val();
    var fechaIni = $("#txtReportesFechaInicio").val();

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

