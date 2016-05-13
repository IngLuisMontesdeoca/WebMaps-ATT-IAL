/*******************************************************************
*																   *	
*@autor:       (AC) Alberto Cepeda  <jorge.cepeda@webmps.com.mx>   *
*@version:		1.0												   *
*@created:		10/02/2014										   *
*@descripcion:	Gestion de usuarios								   *
*@notes:														   *
*																   *	
********************************************************************/

//variabes globales
var admUusarios_FLAGCHECKUS = true;
var admUsuarios_CONTADORUSUARIOS = 0;


$(document).ready(function(){

	$("#dvUserSearch").on("click", adminUsuarios_getInfoFilter);		
	$("#dvUserBtnAll").on("click", function(){ adminUsuarios_getInfoUsuarios("hideValidacion") });	
	$("#dvSaveUserNew").on("click", adminUsuarios_saveNewUsuario);
	$("#dvUsEliminar01").on("click", function(){admUsuarios_DeleteAll("usersPrevious");});
	$("#dvUsEliminar02").on("click", function(){admUsuarios_DeleteAll("usersNew");});	
	$("#dvUserBtnShowAdd").on("click", adminUsuarios_showAgregarUsuarios);
	$(".evtEdicionUser").on("click", adminUsuarios_enableEditUser);
	$(".evtSaveUser").on("click", adminUsuarios_saveEditUser);
	$("[name=chkAdmUsr]").on("click", adminUsuario_activaRegistro);
	$("#dvAddUClose").on("click", function(){  $("#dvModUsuarios").click();	});
	
});


/***
*@descripcion:  Metodo para obtener todos los usuarios
*@param:        (string) data: DOM con la informacion de los equipos
*@return:		
*@update:       (AC) Jorge A. Cepeda
*@update_date:   10/02/2014
***/	
function adminUsuarios_getInfoUsuarios(flagValidaciones){
	$("#txtEUsBuscador").val("");
	$("#dvContTableUsuarios").html("");
	$("#dvContTableUsuarios").addClass("cssLoading01");	
	if(flagValidaciones == "hideValidacion"){
		$("#dvValidUser").html("").removeClass("cssValidation_Yellow cssValidation_Green cssValidation_Red");				
	}				
    $.post("ConsultarUsuario", {todos:1}, adminUusuarios_respGetInfoUsuarios);	
}


function adminUsuarios_getInfoFilter(){
    var valPatron = jQuery.trim($("#txtEUsBuscador").val());	
	var criterio = $("input[name='radioUsuarios']:checked").val();	

	if(valPatron.length == 0){
		$("#dvValidUser").html("Se debe ingresar un criterio de b\xfasqueda").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red");
		return;
	}
	if(valPatron.length > 0 && valPatron != "Ingrese criterio"){
		$("#dvContTableUsuarios").html("");
	   	$("#dvContTableUsuarios").addClass("cssLoading01");				
    	var param = { keyPatron: valPatron, criterio:criterio };			
    	$.post("ConsultarUsuario", param, adminUusuarios_respGetInfoUsuarios);	
	}	
}


function adminUusuarios_respGetInfoUsuarios(data){
	//$("#dvValidUser").html("");
    $("#dvContTableUsuarios").removeClass("cssLoading01");	
	if(data == 0){
		alert("Error al consultar informaci\xf3n, contacte al administrador");
	}
	else if(data == "SIN SESION"){
		alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href="Login";
	}	
    else{
		$("#dvContTableUsuarios").html(data);   
	}
	admEquipos_NUMEROREGISTROSVISIBLES = $("#cboPagNumUsuarios").val();		
	adminEquipos_inicializarTableSort("tblser", "dvPagerUsuarios", "cboPagNumUsuarios");	
	$("#chkAdmUserAll").on("click", admUsuarios_selectAll);	
	$("[name=chkAdmUsr]").on("click", adminUsuario_activaRegistro);	
	$(".evtEdicionUser").on("click", adminUsuarios_enableEditUser);
	$(".evtSaveUser").on("click", adminUsuarios_saveEditUser);	
}


function adminUsuarios_showAgregarUsuarios(){
	admUsuarios_CONTADORUSUARIOS = 0;
	adminUsuario_cleanModulo();
	 admUusarios_FLAGCHECKUS = false;
	 admUsuarios_selectAll();
	
    $("#tblser, #dvContBodyUser").html("");
	$('.evtHide').hide();
	$('#dvModulo04').show();		
}


function adminUsuarios_saveNewUsuario(){
	var nombreUsuario = jQuery.trim($("#txtNombreUser").val());
	var login		  = jQuery.trim($("#txtLoginUser").val());
	var email		  = jQuery.trim($("#emailUser").val());
	var tipoUser	  = jQuery.trim($("#cboTipoUser").val());
	var nombreTipoUser = $("#cboTipoUser option:selected").html();
	var flagValidacion = true;	
	var msg = "";
   	if(nombreUsuario.length==0 || !adm_validateField(nombreUsuario, "nombre") ){ flagValidacion = false; msg+="Nombre. S\xf3lo caracteres alfab\xe9ticos,"; }
	if(login.length==0 || !adm_validateField(login, "login")){ flagValidacion = false; msg+=" Login. S\xf3lo caracteres alfanum\xe9ricos,";}
    if(!adm_validateField(email, "mail") ){ flagValidacion = false; msg += " Correo electr\xf3nico,";}		
    if(  tipoUser == 0 ){ flagValidacion = false; msg += " Tipo de usuario,";}
	msg = msg.substr(0,msg.length-1);	

	if(flagValidacion){
            $("#dvAUValidaciones").html("");
		var param = { nombreUsuario: nombreUsuario, login: login, email: email, tipoUser: tipoUser, nombreTipoUser:nombreTipoUser };
		$("#dvSaveUserNew").off("click");
		$.post("GuardarUsuario", param, admUsuario_respSaveUser);
	}
	else{
		$("#dvAUValidaciones").html(msg).addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");								
	}
}

function admUsuario_respSaveUser(data){
    $("#dvContTblUser").removeClass("cssLoading01");						
    $("#tblAddUser").removeClass("cssOculta");
	$("#dvSaveUserNew").on("click", adminUsuarios_saveNewUsuario);
	if (data == "Existe"){ $("#dvAUValidaciones").html("El login ya fue utilizado, se debe intentar con otro").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red"); return; }
	if (data == "ErrorMail"){ $("#dvAUValidaciones").html("Error al enviar el correo, se debe solicitar una recuperaci\xf3n de contrase\xf1a desde el Login").removeClass("cssValidation_Red cssValidation_Green").addClass("cssValidation_Yellow"); return;}	
    if (data == "0"){ $("#dvAUValidaciones").html("Error al guardar el usuario, por favor consulta al administrador del sistema").removeClass("cssValidation_Yellow cssValidation_Green").addClass("cssValidation_Red"); return;}
	if(data == "SIN SESION"){
		alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href="Login";
	}
		admUsuarios_CONTADORUSUARIOS ++;
		var nombre = $("#txtNombreUser").val();		
		var login = $("#txtLoginUser").val();
		var email = $("#emailUser").val();
		var tipoUser = $("#cboTipoUser option:selected").html();
		var DOM =  "<tr id='regUser_"+data+"' class='evtLineActiveUsuarios'><td>"+admUsuarios_CONTADORUSUARIOS+"</td>";
            DOM += "<td><input id='chkregUser_"+data+"' type='checkbox' name='chkAdmUsr' class='cssChckOne'/></td>";
            DOM += "<td>"+nombre+"</td>";
            DOM += "<td>"+login+"</td>";
            DOM += "<td>"+email+"</td>";
            DOM += "<td>"+tipoUser+"</td>";
            DOM += "</tr>";
		$("#dvContBodyUser").append(DOM);
	
		adminUsuario_cleanModulo();		
		$("#dvAUValidaciones").html("Se env\xedo un correo con una liga, para continuar con el proceso de alta de usuario").removeClass("cssValidation_Red cssValidation_Yellow").addClass("cssValidation_Green");		
		$("[name=chkAdmUsr]").on("click", adminUsuario_activaRegistro);
}

/***
*@descripcion:  Metodo para eliminar varios usuarios
*@param:      
*@return:
*@update:       (AC) Jorge A. Cepeda
*@update_date:   10/02/2014
***/
function admUsuarios_DeleteAll(flagUsuarios){	
	var arrUsers = new Array();
	var usersID = "";
	var contUsurs = 0;
	var arrIDAux = new Array();
	var nombreUsuario = "";
	arrUsers = $('[name=chkAdmUsr]:checked').toArray();
	for (i=0;i<arrUsers.length;i++)
      {
		  arrIDAux = arrUsers[i].id.split("_");
		  if ($('#regUser_'+arrIDAux[1]).is(':visible')){		  
		  	arrAux     = arrIDAux[1];
 		    auxNomUser = arrUsers[i].value;	
					
			usersID += arrAux + "|";
			nombreUsuario += auxNomUser + "|";								 			  			  			
			contUsurs ++;
		  }
      }
	usersID = usersID.substr(0,usersID.length-1);	
	nombreUsuario = nombreUsuario.substr(0,nombreUsuario.length-1);		
	if(usersID.length > 0){
    	if (confirm("Al dar clic en Aceptar, se eliminar\xe1n los "+contUsurs+" usuarios seleccionados")){	
			var param = {ID_User:usersID, nombreUsuario:nombreUsuario}; 	  
			if(flagUsuarios == "usersPrevious"){
    			$.post("EliminarUsuario", param, admUsuario_respDeleteUser);
			}
			else{
    			$.post("EliminarUsuario", param, admUsuario_respDeleteUser);				
			}
		}
	}
	else{alert("Se deben seleccionar los usuarios que se desean eliminar");}
}

/***
*@descripcion:  Metodo de respuesta para eliminar el usuario seleccionado
*@param:        
*@return:
*@update:       (AC) Alberto Cepeda
*@update_date:   10/02/2014
***/
function admUsuario_respDeleteUser(data) {
	var response = data;
    if(response == 1) {
		$('.evtHide, .evtHelpHide').hide();
		$('#dvModulo03').show();		
		admUusarios_FLAGCHECKUS = true;

		if(jQuery.trim($("#txtEUsBuscador").val()).length == 0){
			adminUsuarios_getInfoUsuarios("showValidacion");
		}
		else{
			adminUsuarios_getInfoFilter();
		}
		$("#dvValidUser").html("Los usuarios seleccionados se eliminaron correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Red cssValidation_Yellow");								
	}
	else if(response == "SIN SESION"){
		alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href="Login";
	}	
	else{
	    alert("Error al eliminar los Usuario(s), por favor consulta al administrador");							
	}
}

/***
*@descripcion:  Metodo de respuesta para eliminar el usuario nuevo
*@param:        
*@return:
*@update:       (AC) Alberto Cepeda
*@update_date:   19/02/2014
***/
function admUsuario_respDeleteUserNew(data){
	var response = data;
	var arrUsers = new Array();
	var usersID = "";
	var arrIDAux = new Array();	
    if(response == 1) {
		var arrUsers = $('[name=chkAdmUsr]:checked').toArray();
		for (i=0;i<arrUsers.length;i++){
	//	  arrIDAux = arrUsers[i].id.split("_");
	//	  if ($('#regUser_'+arrIDAux[1]).is(':visible')){		  			
			  Aux = arrUsers[i].id.split("_");
			  $("#regUser_"+Aux).remove();
	//	  }
      	}
//		adminUsuarios_getInfoUsuarios();
		$("#dvValidUser").html("Los usuarios seleccionados se eliminaron correctamente").addClass("cssValidation_Green").removeClass("cssValidation_Red cssValidation_Yellow");								
	}
	else if(response == "SIN SESION"){
		alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href="Login";
	}	
	else{
	    alert("Error al eliminar los Usuario(s), por favor consulta al administrador");							
	}	
}

/***
*@descripcion:  Metodo para seleccionar todos los usuarios 
*@param:      
*@return:
*@update:       (AC) Jorge A. Cepeda
*@update_date:   10/02/2014
***/
function admUsuarios_selectAll(){
	if(admUusarios_FLAGCHECKUS){
		admUusarios_FLAGCHECKUS = false;
		$("[name=chkAdmUsr]").prop('checked',true);
		$("#chkAdmUserAll, .evtChkAdmUserAll").attr('checked','checked');	
		$(".evtLineActiveUsuarios").addClass("cssLineActive");						
	}
	else{
		admUusarios_FLAGCHECKUS = true;
		$("[name=chkAdmUsr]").prop('checked',false);
		$("#chkAdmUserAll, .evtChkAdmUserAll").removeAttr('checked');		
		$(".evtLineActiveUsuarios").removeClass("cssLineActive");							
	}
}


function adminUsuarios_enableEditUser(){
	var idUser = this.id;
	var arrId = idUser.split("_");
	var id = arrId[1]; 
	$(".evtEnableEdit_"+id).removeClass("cssInptTransp").addClass("cssInptEdit01").prop("disabled", false);
	$("#"+idUser).removeClass("cssBtnUEdit evtEdicionUser").addClass("cssBtnUSave evtSaveUser");
	$("#"+idUser).off("click");
	$(".evtSaveUser").on("click", adminUsuarios_saveEditUser);
}

function adminUsuarios_saveEditUser(){
	var idUser = this.id;
	var arrId = idUser.split("_");
	var id = arrId[1]; 
	var nombreUsuario = jQuery.trim($("#txtNombreEditUser_"+id).val());
	var email = jQuery.trim($("#txtEmailEditUser_"+id).val());
	var tipoUsuario = $("#cboTipoUserEdit_"+id).val();	
	var nombreTipoUsuario = $("#cboTipoUserEdit_"+id+" option:selected").html();
	var flagValidacion = true;	
	var msg = "";
   	if(nombreUsuario.length==0 || !adm_validateField(nombreUsuario, "nombre") ){ flagValidacion = false; msg+="Nombre. S\xf3lo caracteres alfab\xe9ticos,"; }
    if(!adm_validateField(email, "mail") ){ flagValidacion = false; msg += " Correo electr\xf3nico,";}		
	$("#dvValidUser").html("");
	msg = msg.substr(0,msg.length-1);	

	if(flagValidacion){
		$(".evtEnableEdit_"+id).removeClass("cssInptEdit01").addClass("cssInptTransp").prop("disabled", true);
		$("#"+idUser).addClass("cssBtnUEdit evtEdicionUser").removeClass("cssBtnUSave evtSaveUser");	
		$("#"+idUser).off("click");
		$(".evtEdicionUser").on("click", adminUsuarios_enableEditUser);	
		
		var param = {nombreUsuario:nombreUsuario, email:email, tipoUsuario:tipoUsuario, id:id, nombreTipoUsuario:nombreTipoUsuario};
		$("#dvNombreEditUser_"+id).text(nombreUsuario);
		$("#dvEmailEditUser_"+id).text(email);
		$("#dvTipoUserEdit_"+id).text($("#cboTipoUserEdit_"+id).text());
		
		$.post("EditarUsuario", param, admUsuario_respSaveEditUser);	
	}
	else{
		$("#dvValidUser").html(msg).addClass("cssValidation_Red").removeClass("cssValidation_Green cssValidation_Yellow");								
	}	
}

function admUsuario_respSaveEditUser(data){
    if (data == "1"){ 
		//var nombreUsuario = $("#txtNombreEditUser_"+id).val();
		//var email = $("#txtEmailEditUser_"+id).val();
		//var tipoUsuario = $("#cboTipoUserEdit_"+id).val();			
		$("#tblser").trigger("update");
		$("#dvValidUser").html("El usuario se edit\xf3 correctamente").removeClass("cssValidation_Red cssValidation_Yellow").addClass("cssValidation_Green");		
	}
    if (data == "0"){ alert("Error al editar el usuario, por favor contacte al administador"); }
	if(data == "SIN SESION"){
		alert("Existe una sesi\xf3n m\xe1s reciente");
        window.location.href="Login";
	}			
}


function adminUsuario_activaRegistro(){
	var id = this.id;
	var arrID = id.split("_");
	if($("#"+id).is(':checked')) {
		$("#regUser_"+arrID[1]).addClass("cssLineActive");
	}
	else{
		$("#regUser_"+arrID[1]).removeClass("cssLineActive");		
	}	
}


function adminUsuario_cleanModulo(){
	$("#txtNombreUser, #txtLoginUser, #emailUser").val("");
    $("#cboTipoUser option[value='0']").prop("selected", true);
	$("#dvAUValidaciones").html("");
}