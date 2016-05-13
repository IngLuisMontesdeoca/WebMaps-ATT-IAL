// Binding of DOM elements to several variables so we can install event handlers.
var dvIconMap01 = document.getElementById("dvMVistaMap"),
	dvIconMap02 = document.getElementById("dvMVistaTerreno"),
	dvIconMap03 = document.getElementById("dvMVistaSatellite"),
	dvIconTransp = document.getElementById("dvCtrlPTt"),
	enableTransp = true,
	typeView;
dvIconMap01.onclick = function () {	

	removeHomeFlag = false;
	
	enableTransp = true;
	mapView(0);
	$("#dvMVistaTerreno").removeClass("cssTMActive");
	$("#dvMVistaSatellite").removeClass("cssTMActive");
	$("#dvMVistaMap").addClass("cssTMActive");
};

dvIconMap02.onclick = function () {

	removeHomeFlag = false;

	enableTransp = true;
	mapView(3);
	$("#dvMVistaMap").removeClass("cssTMActive");	
	$("#dvMVistaSatellite").removeClass("cssTMActive");
	$("#dvMVistaTerreno").addClass("cssTMActive");
};

dvIconMap03.onclick = function () {

	removeHomeFlag = false;

	enableTransp = true;
	mapView(2);
	$("#dvMVistaMap").removeClass("cssTMActive");
	$("#dvMVistaTerreno").removeClass("cssTMActive");
	$("#dvMVistaSatellite").addClass("cssTMActive");
};

dvIconTransp.onclick = function () {
	
	removeHomeFlag = false;	
	
	if(enableTransp)
	{
		enableTransp = false;
		miMapa.mostrarTransporte();
		$("#dvCtrlPTt").addClass("cssCB06Active ");
	}
	else
	{
		enableTransp = true;
		miMapa.ocultarTransporte();
		$("#dvCtrlPTt").removeClass("cssCB06Active ");
	}

};

mapView = function(typeView){
		
	miMapa.fijarTipoMapa(typeView);

};