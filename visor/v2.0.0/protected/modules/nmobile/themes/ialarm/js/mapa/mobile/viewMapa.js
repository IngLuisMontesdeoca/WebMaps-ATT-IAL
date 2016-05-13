// Binding of DOM elements to several variables so we can install event handlers.
var dvIconMap01 = document.getElementById("dvIconMap01"),
	dvIconMap02 = document.getElementById("dvIconMap02"),
	dvIconTransp = document.getElementById("dvIconTransp"),
	conditionView01 = true,
	conditionView02 =true,
	conditionView03 =true,
	typeView;

dvIconMap01.onclick = function () {
		
	if (conditionView01)
	{

		//$("#dvIconTransp").removeClass("cssActiveIconTransp");
		$("#dvIconMap02").removeClass("cssActiveIconMap02");
		
		$("#dvIconMap01").addClass("cssActiveIconMap01");
		mapView(2);
		conditionView01 = false;
		conditionView02 =true;
		//conditionView03 =true;
	}
	else
	{
		$("#dvIconMap01").removeClass("cssActiveIconMap01");
		mapView(0);
		conditionView01 = true;
		conditionView02 =true;
		//conditionView03 =true;		
	}

	
};

dvIconMap02.onclick = function () {
		
	if (conditionView02)
	{

		//$("#dvIconTransp").removeClass("cssActiveIconTransp");
		$("#dvIconMap01").removeClass("cssActiveIconMap01");
		
		$("#dvIconMap02").addClass("cssActiveIconMap02");
		mapView(3);
		conditionView02 = false;
		conditionView01 = true;
		//conditionView03 =true;
	}
	else
	{
		$("#dvIconMap02").removeClass("cssActiveIconMap02");
		mapView(0);
		conditionView02 = true;
		conditionView01 = true;
		//conditionView03 =true;
	}

	
};

dvIconTransp.onclick = function () {
		
	if (conditionView03)
	{

		$("#dvIconMap02").removeClass("cssActiveIconMap02");
		$("#dvIconMap01").removeClass("cssActiveIconMap01");
		
		$("#dvIconTransp").addClass("cssActiveIconTransp");
		miMapa.mostrarTransporte();
		conditionView03 = false;
		//conditionView01 = true;
		//conditionView02 =true;
	}
	else
	{
		$("#dvIconTransp").removeClass("cssActiveIconTransp");
		miMapa.ocultarTransporte();
		conditionView03 = true;
		//conditionView01 = true;
		//conditionView02 =true;
	}

	
};

mapView = function(typeView){

	miMapa.fijarTipoMapa(typeView);

};