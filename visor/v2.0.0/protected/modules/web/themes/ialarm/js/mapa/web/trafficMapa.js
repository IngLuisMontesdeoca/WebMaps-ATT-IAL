// Binding of DOM elements to several variables so we can install event handlers.
var trafficActive = true;
var trafficView = document.getElementById("dvCtrlTrafico"),
	conditionTraffic = true;

trafficView.onclick = function(){
	
	if(trafficActive)
	{
		$("#dvCtrlTrafico").addClass("cssCB05Active");
		trafficActive = false;
	}
	else
	{
		$("#dvCtrlTrafico").removeClass("cssCB05Active");
		trafficActive = true;
	}
	
	if(conditionTraffic)
	{
		miMapa.mostrarTrafico();		
		conditionTraffic = false;
	}
	else
	{
		miMapa.ocultarTrafico();	
		conditionTraffic = true;
	
	}

}	