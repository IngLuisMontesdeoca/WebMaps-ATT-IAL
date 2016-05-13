    var urlAdminEquipos = null;
    var paramAdminEquipos = null;
    var callBackAdminEquipos = null;
$(document).ready(function() {
    _adminEquiposIdTMPEdit = "";
    $("#dvModSesion").on("click", home_closeSecion);
    $('#dvModEquipos').click(function() {
        admEquipos_FLAGCHECKHANDSET = false;
        selecAllEquipos();
        //$.post("ModuloEquipo", null, home_respLoadModEquipos);	
        $('.evtHide').hide();
        $('#dvModulo01').show();
        adminEquipos_getInfoEquipos("hideValidacion");
    });

    $('#dvModAddEquipos').click(function() {
        $('.evtHide, .evtHelpHide').hide();
        $('#dvModulo02').show();
    });

    $('#dvModUsuarios').click(function() {
        admUusarios_FLAGCHECKUS = false;
        admUsuarios_selectAll();
        $('.evtHide, .evtHelpHide').hide();
        $('#dvModulo03').show();
        adminUsuarios_getInfoUsuarios("hideValidacion");
//		home_respLoadModUsuarios();
//	    $.post("ModuloUsuario", null, home_respLoadModUsuarios);			
    });

    $("#dvModReportes").click(function() {
        $('.evtHide, .evtHelpHide').hide();
        $('#dvModulo06').show();
        Reportes_cargarDataPicker();
    });

    /*START Ayuda*/

    $('.evtIconAyuda').click(function() {
        $('.evtContAyuda').slideToggle();
    });

    $('.evtIconAyudaLayer').click(function() {
        $('.evtContAyudaLayer').slideToggle();
    });
    /*END Ayuda*/


    /*START Layers Configuración y Contacto - Usuarios*/

    $('#dvContactClose, #dvBgLayer').click(function() {
        var flagRed = $("#txtRRed").is(':checked');
        var flagEstatus = $("#txtREstatus").is(':checked');
        var flagPlan = $("#txtRPlan").is(':checked');
        var flagServicio = $("#txtRServicio").is(':checked');
        if (!$("#dvELConf").is(":visible")) {
            if (jQuery.trim($("#txtEEBuscador").val()).length == 0 && !flagRed && !flagEstatus && !flagPlan && !flagServicio) {
                adminEquipos_getInfoEquipos("showValidacion");
            }
            else {
                adminEquipos_getInfoFilter();
            }
        }
        $('#dvELContact, #dvBgLayer').hide();
    });

    $('#dvConfigClose, #dvBgLayer, .evtLClose').click(function() {
        $('#dvELConf, #dvBgLayer').hide();
    });

    /*END Layers Configuración y Contacto - Usuarios*/

    $('#dvModAddUsuarios').click(function() {
        $('.evtHide, .evtHelpHide').hide();
        $('#dvModulo04').show();
    });

    $('#dvModLogs').click(function() {
        $(".evtHelpHide").hide();
        $.post("ModuloLog", null, home_respLoadModLogs);
    });

    $('.evtSeccionModulo').click(botonActive01);

    $('#dvAddEClose, #dvEEBtnAdd, #dvUserBtnShowAdd, #dvModEquipos').click(function() {
        $(".evtHelpHide").hide();
    });

    $('.evHCLose').click(function() {
        $(".evtContAyuda").slideToggle();
    });

    $('.evHCLoseLayer').click(function() {
        $(".evtContAyudaLayer").slideToggle();
    });

    $('#dvModEquipos').click();

});


botonActive01 = function() {
    var id = this.id;
    $(".evtSeccionModulo").removeClass("active");
    $("#" + this.id).addClass("active");
}


function home_respLoadModEquipos(data) {
    if (data == "1") {
        $('.evtHide').hide();
        $('#dvModulo01').show();
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else
        alert("No se pudo cargar el modulo");
}

function home_respLoadModUsuarios() {
    //if(data == "1"){
    $('.evtHide').hide();
    $('#dvModulo03').show();
    /*	}
     else
     alert("No se pudo cargar el modulo");*/
}

function home_respLoadModLogs(data) {
    if (data == "1") {
        $('.evtHide').hide();
        $('#dvModulo05').show();
    }
    else if (data == "SIN SESION") {
        alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href = "Login";
    }
    else
        alert("No se pudo cargar el modulo");
}

// Cerrar sesion
function home_closeSecion() {
    $.post("CerrarSesion", null, home_respCloseSecion);
}

function home_respCloseSecion(data) {
    if (data == 1)
        window.location.href = "Login?destoy=1";
    else
        alert("No se pudo cerrar la sesi\xf3n");
}


	