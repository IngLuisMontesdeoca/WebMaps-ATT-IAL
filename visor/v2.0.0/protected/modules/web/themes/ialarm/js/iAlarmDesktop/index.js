// JavaScript Document
var flagDivLine01 = false;
var flagDivLine02 = false;

$(document).ready(function(){
	$("#dvIconMinimizar").click (function(){

		if($("#divLine01").is(':visible'))
			flagDivLine01 = true;
		else
			flagDivLine01 = false;
			
		if($("#divLine02").is(':visible'))
			flagDivLine02 = true;
		else
			flagDivLine02 = false;			
			
		$("#dvContRuteo").addClass("cssContRuteo02");
		$("#dvContRuteo").removeClass("cssContRuteo01");				
		$(".evtRuteoHide, #dvIconMinimizar").hide();
		$("#dvIconMaximizar").show();					
	});
	
	$("#dvIconMaximizar").click (function(){				

		if(flagDivLine01)
			$('#divLine01').removeClass('cssOcultaHard');
		else
			$('#divLine01').addClass('cssOcultaHard');
			
		if(flagDivLine02)
			$('#divLine02').removeClass('cssOcultaHard');
		else
			$('#divLine02').addClass('cssOcultaHard');
			
		$("#dvContRuteo").addClass("cssContRuteo01");
		$("#dvContRuteo").removeClass("cssContRuteo02");
		$("#dvIconMaximizar").hide();		
		$(".evtRuteoHide, #dvIconMinimizar").show();			
	});
	
	
	$("#divWPestana01").click (function(){
		$("#divCCenter").addClass("cssCRightLarge");
		$("#divCCenter").removeClass("cssCRight");		
		
		$("#divWest").addClass("styleWestShort");
		$("#divWest").removeClass("styleWest");	
		
		$(".evtPestana01").hide();		
		$(".evtPestana02").show();
		
		$("#divWText").hide();
	}); 
	
	
	
	$("#divWPestana02").click (function(){			
		
		$("#divCCenter").addClass("cssCRight");		
		$("#divCCenter").removeClass("cssCRightLarge");
				
		$("#divWest").addClass("styleWest");
		$("#divWest").removeClass("styleWestShort");
		
		$(".evtPestana02").hide();		
		$(".evtPestana01").show();
		
		$("#divWText").show();
	});
	
	//START Help
	$('.evtHelpMap').click(btnActiveHelpMap);	
	
	var helpActive = true;	
	
	$('#dvViewHelp').click(function(){
	
	if(helpActive)
	{
		$("#dvViewHelp").addClass("cssCB11Active");
		helpActive = false;
	}
	else
	{
		$("#dvViewHelp").removeClass("cssCB11Active");
		helpActive = true;
	}
	
	
		$('#dvHBodyHelp').slideToggle();	
	});
	
	$('#dvPestanaHelp01').click(function(){
		$('#dvBodyHelp02').hide();
		$('#dvBodyHelp01').show();	
	});
	
	$('#dvPestanaHelp02').click(function(){
		$('#dvBodyHelp01').hide();
		$('#dvBodyHelp02').show();	
	});
	//END Help
	
});	

btnActiveHelpMap = function(){
	var id = this.id;
	$(".evtHelpMap").removeClass("cssTBIHLHActive");
	$("#"+this.id).addClass("cssTBIHLHActive");
}



	
	
	
	
	

				   
						   
						   
							  
				