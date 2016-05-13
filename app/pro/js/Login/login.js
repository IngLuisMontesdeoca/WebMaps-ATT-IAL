$(document).ready( function(){		

	/*START Muestra y oculta Requerimientos mínimos*/
	$("#dvReqMinimos").click(function(){
		$("#dvLBtnInfo").hide();
		$("#dvLoginRequ").fadeIn(500);
	});	
	$(".evtLClose").click(function(){
		$("#dvLoginRequ").hide();
		$("#dvLBtnInfo").fadeIn(500);		
	});
	/*END Muestra y oculta Requerimientos mínimos*/
	
	
	/*START Muestra y oculta Requerimientos mínimos*/
	$("#dvRecPass").click(function(){
		$("#dvLBtnInfo").hide();
		$("#dvLoginPass").fadeIn(500);
	});	
	$(".evtLClose").click(function(){
		$("#dvLoginPass").hide();
		$("#dvLBtnInfo").fadeIn(500);		
	});
	/*END Muestra y oculta Requerimientos mínimos*/	
		
	
	$("#dvPassEnviar").on("click", login_envioMail);
	$('#dvlEnter').on("click", accionBotonLogin);
	$('#dvPassLimpiar').click(login_cleanRecuperacion);
	$("#dvLPass").on("keypress", login_ValidaEnter);	
	$("#dvLUser").on("keypress", login_ValidaEnter);	
    $("#dvNameUser").on("keypress", login_ValidaEnter);		
	
});

//variables globales
var startSesion = true;


function accionBotonLogin()
{
	if(startSesion)
		iniciarSesion();
	else
		login_envioMail();
}
/***
* @description:   Función que permite ejecutar las funcionalidades de login y recuperacion de contraseña al pulsar enter con el teclado
* @author:		  AC
* @updated:		  
* @updated_date:  06/02/2014
***/
function login_validaEnter(e){
	if(e.keyCode == 13) {
        if(startSesion)
		   iniciarSesion();
		else
		   login_envioMail();
    }
}


/***
* @description:   Función que ejecuta el ajax para poder loguearse al sistema.
* @author:		  AC
* @updated:		  
* @updated_date:  06/02/2014
***/
iniciarSesion = function(){
   var userName   = jQuery.trim($("#dvLUser").val());
   var password   = jQuery.trim($("#dvLPass").val());   
   $("#dvTxtValidaciones").html(''); 
	if(!adm_validateField(userName, "login")){
		$("#dvTxtValidaciones").html("Caracteres inv&aacute;lidos en usuario").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
	   	return;
	}        	
	if(adm_validateField(password, "pswdNextel")){
		$("#dvTxtValidaciones").html("Caracteres inv&aacute;lidos en contrase\xf1a").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
	   	return;
	}        		
   if((userName.length > 0 && password.length > 0 && userName != "Usuario" && password != "Password" )){
//	   $("#dvlEnter").attr('id','btnInvaEntrar');
            $('#dvlEnter').addClass("cssLLoad01");
	   $('#dvlEnter').off("click");	
       $("#dvLUser").off("keypress");
	   $("#dvLPass").off("keypress");		   	
       var password   = hex_md5(password);	   
	   $.post('ValidaUsr', {login: userName, passwd: password}, resIniciaSesion);
   } else {
        $("#dvTxtValidaciones").text("Por favor verifique su información").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green"); 
		$("#dvLUser").val('');
		$("#dvLPass").val('');
   }   
}


/***
* @description:   Función de respuesta al ajax que permite loguearse al sistema.
* @author:		  AC
* @updated:		  
* @updated_date:  06/02/2014
***/
resIniciaSesion = function(data){
	var arrData = data.split("|");
	var response = arrData[0];
	var intentos = arrData[1];
	if(response == "1"){
       window.location.href="Home";
       $("#dvTxtValidaciones").html("Redireccionando a home...").addClass("cssValidation_Green").removeClass("cssValidation_Red cssValidation_Yellow");	   
	} 
	else if(response == "0"){
       $("#dvTxtValidaciones").html("Nombre de Usuario y/o Contraseña incorrectos").addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");
	   $("#txtPassword").val(''); 
	   login_hablilitarLogin(); 
	}
	else if(response == "2"){
       $("#dvTxtValidaciones").html("Por motivos de seguridad tu contrase&ntilde;a ha sido bloqueada").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
	   login_hablilitarLogin(); 	   
	}
	else if(response == "3"){
       $("#dvTxtValidaciones").html("Verifique su contrase&ntilde;a e intente nuevamente, ha realizado "+intentos+" intentos de 10 permitidos").addClass("cssValidation_Yellow").removeClass("cssValidation_Green cssValidation_Red");
	   login_hablilitarLogin(); 	   
	}	
	else{
       $("#dvTxtValidaciones").html("Error al ingresar, intente nuevamente").addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");		
	   login_hablilitarLogin(); 	   		
	//$("#btnInvaEntrar").attr('id','btnEntrar');	
	}
}

function login_hablilitarLogin(){
	$('#dvlEnter').on("click", accionBotonLogin);
	$("#dvLUser").on("keypress", login_ValidaEnter);	
	$("#dvLPass").on("keypress", login_ValidaEnter);
         $('#dvlEnter').removeClass("cssLLoad01");		
}

/***
* @description:   Función que ejecuta el ajax para envia el correo y recuperar la contraseña 
* @author:		  AC
* @updated:		  
* @updated_date:  06/02/2014
***/
function login_envioMail() {
    var correo=jQuery.trim($('#dvNameUser').val()); 
	var _vM = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
	var flagVal=true;
	$("#dvPassValidaciones").html('');
	$("#dvPassValidaciones").removeClass("cssValidation_Green cssValidation_Yellow cssValidation_Red");		
	
	if(correo.length== 0){
		$("#dvPassValidaciones").html("Usuario vacio").addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");
		flagVal=false;
	}
	if(!adm_validateField(correo, "login") && correo!=""){
		$("#dvPassValidaciones").html("Caracteres inv&aacute;lidos").addClass("cssValidation_Red").removeClass("cssValidation_Yellow cssValidation_Green");
	   	flagVal=false;
	}
	
	if(flagVal){	
	    $("#dvPassEnviar").addClass("cssLLoad02");
//		$("#dvPassEnviar").attr('id','divBtnInvPaso1');
		//$("#divBtnPaso1Null").removeClass("cssOculta");
                
		$("#dvPassEnviar").off("click");
		$("#dvPassEnviar").off("click");
	    $("#dvNameUser").off("keypress");			
		$.post('EnviaCorreo',{correo:correo},login_resenvioMail);	
	}
	else {
	    return false;       
	}
}

/***
* @description:   Función de respuesta al ajax de envio de correo y recuperar contraseña.
* @author:		  AC
* @updated:		  
* @updated_date:  06/04/2014
***/
function login_resenvioMail(data){
	
	if(data=="2"){  $("#dvPassValidaciones").html("Cuenta de usuario no existente").removeClass("cssValidation_Green cssValidation_Yellow").addClass("cssValidation_Red"); startSesion=true;}
	if(data==0){    $("#dvPassValidaciones").html("No se pudo enviar el correo consulte con el administrador").removeClass("cssValidation_Green cssValidation_Yellow").addClass("cssValidation_Red"); startSesion=true;}
	if(data==1){
		startSesion = true;
		$("#dvTxtValidaciones").html('Se le envi&oacute; un correo electr&oacute;nico con un enlace para reiniciar su contrase&ntilde;a.').removeClass("cssValidation_Red cssValidation_Yellow").addClass("cssValidation_Green");
		$("#dvLClose").click();
		login_cleanRecuperacion();
	//	$("#divPaso01").fadeOut(500);								
	//	$("#divPaso01").hide();
	//	$("#divPaso02").show();
	}
	//$("#divBtnInvPaso1").attr('id','dvPassEnviar');
	//$("#divBtnPaso1Null").addClass("cssOculta");
	$("#dvPassEnviar").on("click", login_envioMail);
    $("#dvNameUser").on("keypress", login_ValidaEnter);		
	//$("#dvPassEnviar").removeClass("cssOculta");
        $("#dvPassEnviar").removeClass("cssLLoad02");
}


function login_cleanRecuperacion(){
	$("#dvNameUser").val("");
	$("#dvPassValidaciones").html("");
	$("#dvPassValidaciones").removeClass("cssValidation_Green cssValidation_Yellow cssValidation_Red");	
}

function login_ValidaEnter(e){
	var id = this.id;
    if(e.keyCode == 13){
		accionBotonLogin();	
		if(id == "dvNameUser"){
			login_envioMail();
		}
    }	
}
	