var searchManager = nokia.places.search.manager;

// Function for receiving search results from places search and process them
var processResults = function (data, requestStatus, requestId) {
	var locations;
	this.locations=locations;
	if (requestStatus == "OK") {
		// The function findPlaces() and reverseGeoCode() of  return results in slightly different formats
		locations = data.results ? data.results.items : [data.location];
		// We check that at least one location has been found
	
		if (locations.length > 0) {
		
			var standardMarker = new nokia.maps.map.StandardMarker(
				new nokia.maps.geo.Coordinate(locations[0].position.latitude, locations[0].position.longitude), {
					//make the marker draggable
					draggable: false
				}
			);		
		
			map.objects.add(standardMarker);
			
			var bbox = new nokia.maps.geo.BoundingBox(
					new nokia.maps.geo.Coordinate(locations[0].position.latitude, locations[0].position.longitude)
			);
			
			map.zoomTo(bbox, false, "default");
			map.set("zoomLevel", param['zoom']);
		
			document.getElementById("spTooltip").innerHTML = locations[0].address.text+"<br>Cuadrante: S45 | Nextel: 10907924";
			
			$("#spBodyFoot").removeClass("cssOculta");
			
		} 
		else 
		{		
			alert("No se encontraron resultados!");
		}
	} 
	else 
	{
		alert("La peticion de busqueda fallo!");
	}
};