//condicion para eliminar el icono de activo home
var myLocationFlag = true;
var accuracyCircle;
var cityCircle = null;
var direccionLocacion = null;

// Binding of DOM elements to several variables so we can install event Handlers.
var myLocationView = document.getElementById("dvCtrlPositioning");

myLocationView.onclick = function (){
	if(isAlertReady)
	{
		if(myLocationFlag)
		{
			if (navigator.geolocation) {
		
				navigator.geolocation.getCurrentPosition(function(position) {
				var posLatitude = position.coords.latitude;
				var posLongitude = position.coords.longitude;
				
				if(direccionLocacion != null)
					direccionLocacion.abort();
					
				direccionLocacion = $.post('GDir',{latitude: posLatitude, longitude: posLongitude},
				function(data){
					var info = JSON.parse(data);
					
					if(arrMarkers[2] != null)
					{
						arrMarkers[2].setMap(null);
						arrMarkers[2] = null;
						arrInfoWindows[2].setMap(null);
						arrInfoWindows[2] = null;
					}				
					
					$("#dvCtrlPositioning").addClass("cssCB03Active");	
					myLocationFlag = false;
			
					coordLocation = new google.maps.LatLng(posLatitude, posLongitude);	
					typeMarker = 'LOCATION';
				
					var coords = position.coords;
					
					var options = {
					  strokeColor: '#007FFF',
					  strokeOpacity: 0.8,
					  strokeWeight: 2,
					  fillColor: '#007FFF',
					  fillOpacity: 0.35,
					  map: miMapa.mapa,
					  center: coordLocation,
					  radius: coords.accuracy
					};
					// Add the circle for this city to the map.
					cityCircle = new google.maps.Circle(options);
					
					google.maps.event.addListener(cityCircle, 'rightclick', function(evento) {

						addMarkerBubble(evento);

					});									
					callBackObtenerDireccion(info.dir.toString(), new google.maps.LatLng(parseFloat(coordLocation.lat()), parseFloat(coordLocation.lng())),'OK');
				});
				//miMapa.obtenerDireccion(coordLocation.lat(), coordLocation.lng(), callBackObtenerDireccion);	
		
				});
			} 
			else {
			alert("Geolocation API is not supported in your browser.Please upgrade your browser");
			}
		}
		else
		{
			pointMarkerBubbleRemove(indexLocation,'LOCATION');
		}
	}
}