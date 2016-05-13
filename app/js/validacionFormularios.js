/********************************************************************************
*   @autor:         (TH) Tomas Hernandez <tomas.hernandez@webmaps.com.mx>    	*
*   @updater:       (AC) Jorge.cepedawebmaps.com.mx                             *
*   @version:       1.0                                                         *
*   @created:       15/10/2012                              					*
*   @copiright:     Copyright (c) 2014, WebMaps              					*
*   @description:	Funciones genéricas para módulos de administración          *
********************************************************************************/

/***
*@descripción: Método para validar campos de un formulario 
*@param:       valor .- (String) Cadena de texto a evaluar, tipo .- (String) tipo de dato     
*@return:	   flag.- (boolean) bandera que indica si se cumplio o no con el formato 
*@update:       (AC) Jorge Cepeda 
*@update_date:   16/10/2012
***/			 
function adm_validateField(valor, tipo){
   var bandera = false;
   switch(tipo){
      case "login":   if(/^[A-Za-z0-9_]{0,60}$/.test(valor))
	                     bandera = true;
	                  break;
	  case "nombre": if(/^[A-Za-z\ÑñÁáÉéÍíÓóÚú\s]{1,60}$/.test(valor))
	                     bandera = true;
	                 break;
	  case "direccion": if(/^[A-Za-z0-9_\-#,.:\/()ñáéíóú\s]{1,60}$/.test(valor))
	  	                     bandera = true;
	                 break;
	  case "mail": if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(valor))
	                     bandera = true;
	                break;
	  case "cp" : if(/^\-?[0-9]{4,5}$/.test(valor))
	                 bandera = true;
				  break;
	  case "empresa" : if(/[^A-Za-zñáéíóú0-9_.&\s]/.test(valor))
	                 bandera = true;
				  break;	  
	  case "accountNumber" : if(/^([0-9]+.*[0-9]+)$/.test(valor))
	                 bandera = true;
				  break;	  				  
	  case "PTN" : if(/[0-9]{12}/.test(valor))
	                 bandera = true;
				  break;	  				  				  
      case "modelo":   if(/^[A-Za-z0-9_\-.\/]{1,17}$/.test(valor))
	                     bandera = true;
	                  break;				  
	  case "numero" : if(/^\d*$/.test(valor))
	                 bandera = true;
				  break;	  				  				  					  
	  case "patronLogin" : if(/[^A-Za-zñ0-9]/.test(valor))
	                 bandera = true;
				  break;	  				  				  					  
	  case "pswdNextel" : if(/[^A-Za-zñ0-9]/.test(valor))
	                 bandera = true;
				  break;	  				  				  					  
	  case "telefono" : if(/^(\d{12,15})?$/.test(valor))
	                 bandera = true;
				  break;	  				  				  
				  
	  default: break;
   }
   
   return bandera;
}

/***
*@descripción: Método para validar el password en el formato correcto
*@param:       password .- (String) Cadena de texto    
*@return:	   flag.- (boolean) bandera que indica si se cumplio o no con el formato 
*@update:       (AC) Jorge Cepeda 
*@update_date:   16/10/2012
***/			 
function adm_validatePassword(password){
   password= jQuery.trim(password);
   var flag = false;
   var nNumeros = 0;
   var startLetra = false;
   var formato = false;
   if(password.length > 6){
      if(/^[A-Za-z]{1,60}$/.test(password[0]))
	     startLetra = true;
		 
      for(i=0;i<password.length;i++){
	      if(!isNaN(password[i])){
		     nNumeros++;   
		  }
	  }
	  
	  if(adm_validateField(password, "login")){
	     formato = true;
	  }
	  
	  if((formato && startLetra) && (nNumeros > 1) )
	     flag = true;
   }
   
   return flag;
}

/***
*@descripción: Método para Validar caracteres cadenas de caracteres
*@param:       cadena .- (String) Cadena de texto a evaluar   
*@return:	   (boolean) bandera si cumple o no con la expresion regular
*@update:       (AC) Jorge Cepeda
*@update_date:   16/10/2012
***/			 
function adm_validateChars(cadena){
   if(/^[A-Za-z0-9_\(\)-\[\]ñáéíóú.\s]{1,60}$/.test(cadena))
      return true;
   else
      return false;
}

/***
*@descripción: Método para Validar caracteres crossscripting
*@param:       arg .- (String) Cadena de texto    
*@return:	   aux .- (String) Palabras invalidas
*@update:       (AC) Jorge Cepeda 
*@update_date:   16/10/2012
***/			 
function validateForm_filterXSS(arg){
	var reservedWords = [ "=", "\'", "\"", "/", "\\", "(", ";", ")", "<", ">", "ALERT", "SCRIPT", "HTML", "BODY", "HEAD", "DOCUMENT", "WINDOW" ];
    var valid = true;
    var aux = jQuery.trim(arg);
    aux = aux.toUpperCase();
    for(n=0; n<reservedWords.length; n++){
		if (aux ==  reservedWords[n]){
			aux = aux.replace(reservedWords[n], " ");
            valid = false;
		}
    }
    if (valid)
		return arg;
    else
		return jQuery.trim(aux.toLowerCase());
}
	
/***
*@descripción: Método para Validar si existe solo el caracter _ en una cadena de texto
*@param:       stringPatron .- (String) Cadena de texto a evaluar    
*@return:		flag .- (booleano) bandera
*@update:       (AC) Jorge Cepeda
*@update_date:   16/10/2012
***/		
function validateGuinBajo(stringPatron){
	var flagContainer = false;
	var arrStringPatron = stringPatron.split(""); 
	for(i=0;i<arrStringPatron.length;i++){	
		if(arrStringPatron[i] != "_"){
			flagContainer = false; 
			break;
		}
		else{
			flagContainer = true; 					
		}
	}
	return flagContainer;
}