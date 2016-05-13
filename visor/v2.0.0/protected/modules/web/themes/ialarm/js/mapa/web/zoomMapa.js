// Create a nokia.maps.search.Manager to perform our geocoding tasks
//var removeHomeFlag = true;
var activeHomeFlag = false;
var flagClickHome = false;
var cssRemoveHome = function (){
$("#dvCtrlHome").removeClass("cssCB01Active");
//removeHomeFlag = true;
activeHomeFlag = true;
flagClickHome = false;
};

// Hook events to the UI buttons
document.getElementById("dvCtrlHome").onclick = function () {

	$("#dvCtrlHome").addClass("cssCB01Active");
	typeMarker = 'HOME';
		
	if(activeHomeFlag)
	{
		//removeHomeFlag = false;
		activeHomeFlag = false;
		new google.maps.LatLng(19.412818494606924, -99.13204990246524);
		miMapa.mapa.setCenter(new google.maps.LatLng(19.412818494606924, -99.13204990246524));
		miMapa.mapa.setZoom(5);				
		flagClickHome = true;
	}
	else
	{
		$("#dvCtrlHome").removeClass("cssCB01Active");
		activeHomeFlag = true;
		flagClickHome = false;
		coordOrigen
		miMapa.mapa.setCenter(coordOrigen);
		miMapa.mapa.setZoom(15);				
	}
	
};