//http://ilocator.nlp.webmaps.mx/Desktop6.0/css/Busqueda/images/right.gif
function secondsToTime(secs)  {
    var hours = Math.floor(secs / (60 * 60));   
    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);
    return "" + hours + ":" + minutes;
}

// Assume distances will be metric. i.e. scale bar in kilometers.
var metricMeasurements = ("m" != "k");
var baseUrl;

baseUrl = document.location.href;
baseUrl = baseUrl.substring(0, baseUrl.indexOf('Im.php?'));
	
//
//  The API returns all distances in meters (yards). This should be altered to kilometers (miles) for longer distances.
// Like the Map Image API we can use sb=k or sb=m here.
//
function calculateDistance(distance,flag){
		if (metricMeasurements){
				if ((distance < 1000) && (flag == 1)){
						return "" + maneuver.length + " m.";
				} else {
						return "" + Math.floor(distance/100)/10 + " km.";
				}
		} else {
				if (distance < 1610){
						return "" + Math.floor(distance/1.0936) + " yards";
				} else {
						return "" + Math.floor(distance/160.934)/10 + " miles";
				}
		}
}	

//
// Simple zoom to function taking the location of a maneuver and zooming to it.
//
function zoomTolink(lat, lng, instructions){
   return '<a onclick="centerMarker('+parseFloat(lat)+','+parseFloat(lng)+');" style = "cursor: pointer">'+ instructions +'<\/a>&nbsp;';
}

function descriptionRoute(route){

	    var instructions = "",
			directions = "";
		var zebra = "";
		
	    var details = '<table class="cssContRuteo"><tbody>';
		
	
		position = route.routes[0].legs[0].steps[0].start_location;
		instructions = route.routes[0].legs[0].start_address;
                  	 
		details = details + '<tr class="cssZebra0'+zebra+'"><td class="cssCR01"><div><img class="cssIFImg" src="'+ baseUrl +'../protected/modules/web/themes/ialarm/images/iAlarmDesktop/globe'+iconStart+'.png"></div><\/td><td class="cssCR02">';
		details = details +  zoomTolink(position.lat(), position.lng(), instructions);
		details = details + '<\/td><\/tr>';		
		
	    for (var i = 0, sizeStep = route.routes[0].legs[0].steps.length;  i < sizeStep; i++)
		{
			if(i%2 == 0)
				zebra = "1";
			else
				zebra = "2";
				 
			position = route.routes[0].legs[0].steps[i].start_point;
			instructions = route.routes[0].legs[0].steps[i].instructions;
			directions = route.routes[0].legs[0].steps[i].maneuver;
                  	 
			details = details + '<tr class="cssZebra0'+zebra+'"><td class="cssCR01"><div><img src="'+ baseUrl +'../protected/modules/web/themes/ialarm/images/iAlarmDesktop/ruteo/'+ directions +'.gif"></div><\/td><td class="cssCR02">';
					
			details = details +  zoomTolink(position.lat(), position.lng(), instructions);
              	  
			details = details + '<\/td><\/tr>';
	    }
		
		position = route.routes[0].legs[0].steps[sizeStep-1].end_location;
		instructions = route.routes[0].legs[0].end_address;
                  	 
		details = details + '<tr class="cssZebra0'+zebra+'"><td class="cssCR01"><div><img class="cssIFImg" src="'+ baseUrl +'../protected/modules/web/themes/ialarm/images/iAlarmDesktop/globe'+iconEnd+'.png"></div><\/td><td class="cssCR02">';
		details = details +  zoomTolink(position.lat(), position.lng(), instructions);
		details = details + '<\/td><\/tr>';
		
	    details =  details + '</tbody></table>';	   	    
		
		$("#dvContTable").removeClass("cssLoading02");	
	    document.getElementById("dvContTable").innerHTML = details;			

}
