// Binding of DOM elements to several variables so we can install event handlers.
var trafficView = document.getElementById("dvIconInccTraficc"),
	conditionTraffic = true;

trafficView.onclick = function(){

	if(conditionTraffic)
	{
		$("#dvIconInccTraficc").addClass("cssActiveInccTraffic");
		miMapa.mostrarTrafico();		
		conditionTraffic = false;
	}
	else
	{
		$("#dvIconInccTraficc").removeClass("cssActiveInccTraffic");
		miMapa.ocultarTrafico();
		conditionTraffic = true;
	}

}	