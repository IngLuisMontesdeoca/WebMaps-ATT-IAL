
var	directionsDisplay = null;
	routeDescriptionRemove = function(){
	if (miMapa.servicioDireccion != null) miMapa.servicioDireccion.setMap(null);
	if (directionsDisplay != null) directionsDisplay.setMap(null);
	},
	flagSE = 0,
	inverseRoute = false,
	calculateRoute = document.getElementById("btnCalcular01"),
	// Get start addresses and then send geocode requests
	feedbackTxt = "",
	//mapRoute,
	searchResults = 0,
	// Create routing mode
	modes = [{
			type: "fastest",
			transportModes: ["car"],
			trafficMode: "enabled"
	}];

var callBackObtenerRuta = function(respuesta)
{
	var optionDirectionRender = {
		suppressMarkers: true,
		polylineOptions: {strokeColor:'#E05206'},
		map: miMapa.mapa
	};
	
	directionsDisplay = new google.maps.DirectionsRenderer(optionDirectionRender);
	directionsDisplay.setMap(miMapa.mapa);
	directionsDisplay.setDirections(respuesta);
	descriptionRoute(respuesta);
	
}
	
// onclick event handler for checkbox traffic-based routing
calculateRoute.onclick = function () {

	if(indexStart == -1)
	{
		alert("Origen no definido");
		return;
	}
	if(indexEnd == -1)
	{
		alert("Destino no definido");
		return;
	}	
	
	$("#dvContTable").html("");	
	$("#dvContTable").addClass("cssLoading02");	
	
	miMapa.obtenerRuta(arrMarkers[indexStart].get("position"), arrMarkers[indexEnd].get("position"),callBackObtenerRuta);
};

function setStart(nameMarker){

	routeDescriptionRemove();
	$("#dvContTable").html("");

	switch(nameMarker)
	{
	case 'LOCATION':
		flagSE = 1;

                if(idDivL == 0)
                {
                    $('#divLine01').removeClass("cssOcultaHard");
                    $('#divLine02').removeClass("cssOcultaHard");                                
                }
                else
                    $('#divLine0'+idDivL+'').removeClass("cssOcultaHard");
		
//		if(idDivA = 1)
//		{alert("1");
			idDivL = 1;
			idDivA = 2;
//		}
//		else
//		{alert("2");
//			idDivL = 2;
//			idDivA = 1;
//		}
		
		indexStart = indexLocation;
		indexEnd = indexAlert;
		pointMarkerBubbleIcon(routeIcon, 'Orange', extIcon, indexStart);
		pointMarkerBubbleIcon(routeIcon, 'Red', extIcon, indexEnd);
		
		if(indexHandler != 0)
		{
		
			pointMarkerBubbleIcon(routeIcon, 'Blue', extIcon, indexHandler);
		
			if(idDivH != 0)
			{
		//		$('#divLine0'+idDivH+'').css('display','none');
				//indexEnd = - 1;
				idDivH = 0;
			}		
		
		}
		
		divRuteo(idDivL, objectLocation.address, objectLocation.coord.lat(), objectLocation.coord.lng(),'LOCATION');
		divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
		
		$('#divLine0'+idDivA+'').css('display','block');
		$('#divLine0'+idDivL+'').css('display','block');		
	
	
	
	
		break;
		
	case 'HANDLER':
		flagSE = 2;
                
                if(idDivH == 0)
                {
                    $('#divLine01').removeClass("cssOcultaHard");
                    $('#divLine02').removeClass("cssOcultaHard");                                
                }
                else
                    $('#divLine0'+idDivH+'').removeClass("cssOcultaHard");                
		
//		if(idDivA = 1)
//		{
			idDivH = 1;
			idDivA = 2;
//		}
//		else
//		{
//			idDivH = 2;
//			idDivA = 1;
//		}
		

		indexStart = indexHandler;
		indexEnd = indexAlert;
		pointMarkerBubbleIcon(routeIcon, 'Green', extIcon, indexStart);
		pointMarkerBubbleIcon(routeIcon, 'Red', extIcon, indexEnd);
		
		divRuteo(idDivH, objectHandler.address, objectHandler.coord.lat(), objectHandler.coord.lng(),'HANDLER');
		divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
		
		$('#divLine0'+idDivA+'').css('display','block');
		$('#divLine0'+idDivH+'').css('display','block');
		
		
		break;
		
	case 'ALERT':

		indexStart = indexAlert;
		pointMarkerBubbleIcon(routeIcon, 'Green', extIcon, indexStart);
		if(flagSE == 2)
		{
			if(idDivH != 0)
			{	
			
				if(idDivA = 1)
				{
					idDivH = 2;
					idDivA = 1;
				}
				else
				{
					idDivH = 1;
					idDivA = 2;
				}
		
				indexEnd = indexHandler;
				pointMarkerBubbleIcon(routeIcon, 'Red', extIcon, indexEnd);
				divRuteo(idDivH, objectHandler.address, objectHandler.coord.lat(), objectHandler.coord.lng(),'HANDLER');
				idDivH = 2;
			}
			else
			{
				$('#divLine02').css('display','none');
				idDivA = 1;	
			}
			
			divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
			$('#divLine0'+idDivA+'').css('display','block');
		}
		
		if(flagSE == 1)
		{
			if(idDivL != 0)
			{	
			
				if(idDivA = 1)
				{
					idDivL = 2;
					idDivA = 1;
				}
				else
				{
					idDivL = 1;
					idDivA = 2;
				}
		
				indexEnd = indexLocation;
				pointMarkerBubbleIcon(routeIcon, 'Orange', extIcon, indexEnd);
				divRuteo(idDivL, objectLocation.address, objectLocation.coord.lat(), objectLocation.coord.lng(),'LOCATION');
				idDivL = 2;
			}
			else
			{
				$('#divLine02').css('display','none');
				idDivA = 1;	
			}
			
		}	

		if(flagSE == 0)
		{
			$('#divLine02').css('display','none');
			indexEnd = -1;
			idDivA = 1;	
		}
                
                if(($('#divLine01').hasClass("cssOcultaHard")) || ($('#divLine02').hasClass("cssOcultaHard")))
                {
                    $('#divLine01').removeClass("cssOcultaHard")
                    $('#divLine02').addClass("cssOcultaHard")                        
                }
                //console.log($('#divLine01').hasClass("cssOcultaHard"));
                //console.log($('#divLine02').hasClass("cssOcultaHard"));                               
		
		divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
		$('#divLine0'+idDivA+'').css('display','block');		
		

		break;

	default:
		break

	}

};

function setEnd(nameMarker){

	routeDescriptionRemove();
	$("#dvContTable").html("");

	switch(nameMarker)
	{
	case 'LOCATION':
		flagSE= 1;
                
                if(idDivL == 0)
                {
                    $('#divLine01').removeClass("cssOcultaHard");
                    $('#divLine02').removeClass("cssOcultaHard");                                
                }
                else
                    $('#divLine0'+idDivL+'').removeClass("cssOcultaHard");

	
//		if(idDivA = 2)
//		{
			idDivL = 2;
			idDivA = 1;
//		}
//		else
//		{
//			idDivL = 1;
//			idDivA = 2;
//		}
		

		indexEnd = indexLocation;
		indexStart = indexAlert;
		pointMarkerBubbleIcon(routeIcon, 'Green', extIcon, indexStart);
		pointMarkerBubbleIcon(routeIcon, 'Orange', extIcon, indexEnd);
		
		if(indexHandler != 0)
		{
		
			pointMarkerBubbleIcon(routeIcon, 'Blue', extIcon, indexHandler);
		
			if(idDivH != 0)
			{
			//	$('#divLine0'+idDivH+'').css('display','none');
				//indexEnd = - 1;
				idDivH = 0;
			}		
		
		}		
		
		divRuteo(idDivL, objectLocation.address, objectLocation.coord.lat(), objectLocation.coord.lng(),'LOCATION');
		divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
		
		$('#divLine0'+idDivA+'').css('display','block');
		$('#divLine0'+idDivL+'').css('display','block');

		



		
	
		break;
		
	case 'HANDLER':
		flagSE = 2;
                
                if(idDivH == 0)
                {
                    $('#divLine01').removeClass("cssOcultaHard");
                    $('#divLine02').removeClass("cssOcultaHard");                                
                }
                else
                    $('#divLine0'+idDivH+'').removeClass("cssOcultaHard");                
	
//		if(idDivA = 2)
//		{
			idDivH = 2;
			idDivA = 1;
//		}
//		else
//		{
//			idDivH = 1;
//			idDivA = 2;
//		}
		
		indexEnd = indexHandler;
		indexStart = indexAlert;
		pointMarkerBubbleIcon(routeIcon, 'Green', extIcon, indexStart);
		pointMarkerBubbleIcon(routeIcon, 'Red', extIcon, indexEnd);
		
		divRuteo(idDivH, objectHandler.address, objectHandler.coord.lat(), objectHandler.coord.lng(),'HANDLER');
		divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
		
		$('#divLine0'+idDivA+'').css('display','block');
		$('#divLine0'+idDivH+'').css('display','block');
			
	
	
	
	
		break;
		
	case 'ALERT':

		indexEnd = indexAlert;
		pointMarkerBubbleIcon(routeIcon, 'Red', extIcon, indexEnd);
		
		if(flagSE == 2)
		{
			if(idDivH != 0)
			{	
			
//				if(idDivA = 2)
//				{
					idDivH = 1;
					idDivA = 2;
//				}
//				else
//				{
//					idDivH = 2;
//					idDivA = 1;
//				}
		
				indexStart = indexHandler;
				pointMarkerBubbleIcon(routeIcon, 'Green', extIcon, indexStart);
				divRuteo(idDivH, objectHandler.address, objectHandler.coord.lat(), objectHandler.coord.lng(),'HANDLER');
				idDivH = 1;
			}
			else
			{
				$('#divLine01').css('display','none');
				idDivA = 2;	
			}
			
		}
		
		if(flagSE == 1)
		{
			if(idDivL != 0)
			{	
			
//				if(idDivA = 2)
//				{
					idDivL = 1;
					idDivA = 2;
//				}
//				else
//				{
//					idDivL = 2;
//					idDivA = 1;
//				}
		
				indexStart = indexLocation;
				pointMarkerBubbleIcon(routeIcon, 'Orange', extIcon, indexStart);
				divRuteo(idDivL, objectLocation.address, objectLocation.coord.lat(), objectLocation.coord.lng(),'LOCATION');
				idDivL = 1;
			}
			else
			{
				$('#divLine01').css('display','none');
				idDivA = 2;	
			}
			
		}	

		if(flagSE == 0)
		{
			$('#divLine01').css('display','none');
			indexStart = -1;
			idDivA = 2;	
		}		

                if(($('#divLine01').hasClass("cssOcultaHard")) || ($('#divLine02').hasClass("cssOcultaHard")))
                {
                    $('#divLine01').addClass("cssOcultaHard")
                    $('#divLine02').removeClass("cssOcultaHard")                        
                }
                //console.log($('#divLine01').hasClass("cssOcultaHard"));
                //console.log($('#divLine02').hasClass("cssOcultaHard"));                               

		divRuteo(idDivA, objectAlert.address, objectAlert.coord.lat(), objectAlert.coord.lng(),'ALERT');
		$('#divLine0'+idDivA+'').css('display','block');		
		
		break;

	default:
		break

	}

};

$("#btnLimpiar01").click(
        function(){
            routeDescriptionRemove();
            $("#dvContTable").html("");
        }
    );        