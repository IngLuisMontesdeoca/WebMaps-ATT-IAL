/********************************************************************************
*                                                           					*
*   @autor:         Alberto Cepeda 	<jorge.cepeda@webmaps.com.mx>		  		*
*   @version:       1.0                                     					*
*   @created:       06/02/2014                              					*
*   @copiright:     Copyright (c) 2014, WebMaps              					*
*   @description    Controlador de Activación de Usuarios    					*
*                                                           					*
********************************************************************************/

$(document).ready(function(){
   $('#dvChangePass').on("click", activateCuenta_valida);  
   $("#dvNewPass").on("keypress", activaCuenta_ValidaEnter);	
   $("#dvNewPassAg").on("keypress", activaCuenta_ValidaEnter);		
});


/***
*   @description:  Método que genera el ajax para activar un password
*   @param:             
*   @return:       void
* 	@update: 	   AC
*   @update_date:  06/02/2014
***/
function activateCuenta_valida(){

    var pass1 = jQuery.trim($('#dvNewPass').val()); 
	var pass2 = jQuery.trim($('#dvNewPassAg').val());
		
	var _vM   = /^([A-Za-z]+[0-9]+[A-Za-z]*[0-9]+[A-Za-z0-9]*)$/;
	$("#dvNewPassValidaciones").html('').addClass('cssLoading01').show();
	
	if(pass1.length == 0){
	   
	   msgError="Debe escribir una contrase\xf1a";
	    $("#dvNewPassValidaciones").html("Debe escribir una contrase\xf1a").removeClass('cssLoading01').addClass('cssValidation_Red');
	    $('#dvNewPass').val('');
	    $('#dvNewPass').focus();
	    return;
	}
	if(pass1 != pass2){
	    $("#dvNewPassValidaciones").html("Las contrase\xf1as no co\xednciden").removeClass('cssLoading01').addClass('cssValidation_Red');
	    return;
	}
	if(pass1.length < 7 | pass1.length >15) {  
        $("#dvNewPassValidaciones").html("La contrase\xf1a debe tener entre 7 y 15 caracteres").removeClass('cssLoading01').addClass('cssValidation_Red');
        return; 
    }  
	
	if(!(_vM.test(pass1))){
		$("#dvNewPassValidaciones").html("La contrase\xf1a no cumple con las caracter\xedsticas descritas abajo").removeClass('cssLoading01').addClass('cssValidation_Red');
        return;
	}
	
//	$("#dvChangePass").attr('id','dvBtnInvSave');
	var cod = $("#dvChangePass").attr('name');
    pass1   = hex_md5(pass1);	 
    pass2   = hex_md5(pass2);	
	$('#dvChangePass').off("click");  
    $("#dvNewPass").off("keypress");	
    $("#dvNewPassAg").off("keypress");		  
	$.post('SetPassword',{pass1:pass1, pass2: pass2, cod:cod},activateCuenta_resvalida);	
}

/***
*   @description:  Método de respuesta a la activacion de password
*   @param:        data:true o false    
*   @return:       void
* 	@update: 	   AC
*   @update_date:  06/02/2014
***/
function activateCuenta_resvalida(data){
    if(data == 1){
		$("#dvNewPassValidaciones").html("Contrase\xf1a registrada correctamente").removeClass('cssLoading01 cssValidation_Yellow cssValidation_Red').addClass('cssValidation_Green');
		$("#dvBtnASave, #dvBtnInvSave").hide();
//		alert("Contrase\xf1a registrada correctamente");
	    window.location.href = "Login?destoy=2";	
	} else
		$("#dvNewPassValidaciones").html("Error al intentar activar su cuenta, por favor contacte al administrador del sistema").removeClass('cssLoading01 cssValidation_Yellow cssValidation_Green').addClass('cssValidation_Red');
   $('#dvChangePass').on("click", activateCuenta_valida);  
   $("#dvNewPass").on("keypress", activaCuenta_ValidaEnter);	
   $("#dvNewPassAg").on("keypress", activaCuenta_ValidaEnter);			
}


function activaCuenta_ValidaEnter(e){
    if(e.keyCode == 13){
		activateCuenta_valida();	
    }		
}