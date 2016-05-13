var typeMarker;
var indexLocation = 2, indexAlert = 0, indexHandler = 1, indexStart = indexAlert, indexEnd = - 1;
var coordOrigen; 
var direccionHandler = null;

/* We would like to add event listener on mouse click or finger tap so we check
 * nokia.maps.dom.Page.browser.touch which indicates whether the used browser has a touch interface.
 */
/*var TOUCH = nokia.maps.dom.Page.browser.touch,
	CLICK = TOUCH ? "tap" : "click";*/

var InfoBubbleMarker = function (coord, infoBubbles, bubbleText, props) {
	var container;
	
	if (!((infoBubbles instanceof nokia.maps.map.component.InfoBubbles) &&
		(coord instanceof nokia.maps.geo.Coordinate) && bubbleText)) {
		throw "Invalid arguments given to InfoBubbleMarker constructor";
	}
	
	if (props && props.eventDelegationContainer) {
		container = props.eventDelegationContainer;
		delete props.eventDelegationContainer;
	}
	
	// Call the "super" constructor to initialize properties inherited from StandardMarker
	nokia.maps.map.Marker.call(this, coord, props);
	
	this.init(infoBubbles, bubbleText, container);
};

var _flagEvento = true;
// InfoBubbleMarker constructor function 
InfoBubbleMarker.prototype.init = function (infoBubbles, bubbleText, container) {
	var that = this,
		container,
		// An event callback for the click event on InfoBubbleMarker
		clickHandler = function (evt) {
			/* Using information stored in the event we find out which marker was clicked
			 * and then we trigger that marker's showBubble function
			 */
			var marker = evt.target;
			
			marker.showBubble();
			
			_flagEvento = false;
			
			// Prevent event propagation. This will prevent from additional markers to be created on the same point
			evt.stopImmediatePropagation();
		},
		mouseoverHandler = function (evt) {
			/* Using information stored in the event we find out which marker was clicked
			 * and then we trigger that marker's showBubble function
			 */
			 
			 
			var infoBubbleMarkers = markersContainer.objects.asArray();			 
		
			for(var i= 0; i < infoBubbleMarkers.length; i++)
			{
				if(infoBubbleMarkers[i])
					infoBubbleMarkers[i].closeBubble();
			}
			 
			var marker = evt.target;
			
			marker.showBubble();
			
			
			// Prevent event propagation. This will prevent from additional markers to be created on the same point
			evt.stopImmediatePropagation();
		},
		// Helper function to remove infoBubble of marker
		mouseoutHandler = function (evt) { 
				var marker = evt.target;
				
				var infoBubbleMarkers = markersContainer.objects.asArray();
				
				if(_flagEvento)
				{
				
					for(var i= 0; i < infoBubbleMarkers.length; i++)
					{
						if(infoBubbleMarkers[i])
							infoBubbleMarkers[i].closeBubble();
					}
				}
				
			},

		// Helper function switch mouse cursor icon 
		mouseCursorHandler = function (evt, cursorType) {
			var marker = evt.target;
		
			if (marker instanceof InfoBubbleMarker) {
				document.body.style.cursor = cursorType;
			}
		};
		
	that.infoBubbles = infoBubbles;
	that.bubbleText = bubbleText;
	
	if (container) {
		/* We are using event delegation: one eventlistener on the map object container
		 * instead of event listener per marker
		 */
		
		if (!container.$IS_INFOBUBBLE_MARKER_CONTAINER) {
			container.addListener("click", clickHandler);
			container.addListener("mouseover", mouseoverHandler);
			container.addListener("mouseout", mouseoutHandler);
			//container.addListener("dragstart", dragStartHandler);
			container.$IS_INFOBUBBLE_MARKER_CONTAINER = true;
		}
	} else {
		/* Add all of the needed event listeners in one go using 
		 * EventTarget.addListeners
		 */
		that.addEventListeners({
			"tap": clickHandler,
			"click": clickHandler,
			"mouseover": function (evt) { that.showBubble(); },
			"mouseout": function (evt) { that.hideBubble(); }
			//"drag": function (evt) { that.hideBubble(); }
		});
	}
	
	// We change the options of infoBubbles so we can have multiple open at the same time
	infoBubbles.options.set("autoClose", false);
};

// Helper property to identify instances of InfoBubbleMarker
InfoBubbleMarker.prototype._type = "infoBubbleMarker";

// Overload standard destroy() of StandardMarker so we hide infoBubble on destruction of marker
InfoBubbleMarker.prototype.destroy = function () {
	this.closeBubble();
	InfoBubbleMarker.superprototype.destroy.call(this);
};

// To change text of information bubble
InfoBubbleMarker.prototype.setBubbleText = function (bubbleText) {
	if (this.infoBubble)
		this.infoBubble.update(bubbleText);
};

// Add to marker functions to hide / show the information bubble
InfoBubbleMarker.prototype.showBubble = function () { 
	if (this.infoBubble)
		this.closeBubble();
	
	this.infoBubble = this.infoBubbles.openBubble(this.bubbleText, this.coordinate);
};

InfoBubbleMarker.prototype.closeBubble = function () {
	if (this.infoBubble) {
		this.infoBubble.close();
		this.infoBubble = null;
	}
};

// Get a reference to our ui elements
var removeMarkersUiElt;// = document.getElementById("removeMarkers");

// Define callback function for the reset button
removeMarkersUiElt = function () {
	var infoBubbleMarkers = markersContainer.objects.asArray(),
		i = infoBubbleMarkers.length;
		
	//if (mapRoute1) map.objects.remove(mapRoute1);
	//if (routePolyline) map.objects.remove(routePolyline);

	// Removes the markers from the map by emptying the markersContainer
	//markersContainer.objects.clear();
	markersContainer.objects.remove(infoBubbleMarkers[1]);
	
	// Check if container was used for InfoBubbleMarker event delegation
	if (markersContainer.$IS_INFOBUBBLE_MARKER_CONTAINER) {
		delete markersContainer.$IS_INFOBUBBLE_MARKER_CONTAINER;
		// Remove all event listeners set by InfoBubbleMarker
		markersContainer.removeListener("click", InfoBubbleMarker.clickHandler);
		markersContainer.removeListener("mouseover", InfoBubbleMarker.mouseCursorHandler);
		markersContainer.removeListener("mouseout", InfoBubbleMarker.mouseCursorHandler);
	} 

//  while para borrar 	
//	while (i--) {
		// Destroys every marker
		infoBubbleMarkers[1].destroy();
//	}
	
};


removeMarkersUiEltHistorico = function () 
{

	for (var i = 0, sizeM = arrMarkersHistory.length; i < sizeM; i++) 
	{
		arrMarkersHistory[i].setMap(null);
		arrInfoWindowsHistory[i].setMap(null);
	}
	
	arrMarkersHistory = new Array();
	arrInfoWindowsHistory = new Array();
	
};



/* Attach an event listener to map display
 * push info bubble with coordinate information to map
 */
/*pointMarkerBubble = function (){

	miMapa.obtenerDireccion(param['l'], param['n'], callBackObtenerDireccion);

}*/

pointMarkerBubbleRemove = function (index,nameMarker){

	if(arrMarkers[index] != null)
	{
		arrMarkers[index].setMap(null);
		arrMarkers[index] = null;
		arrInfoWindows[index].setMap(null);
		arrInfoWindows[index] = null;
	}
	
	
	switch(nameMarker)
	{
	case 'LOCATION':
	
		if(cityCircle != null)
		{
			cityCircle.setMap(null);
			cityCircle = null;
		}
		
		if(direccionLocacion != null)
		{
			direccionLocacion.abort();
			direccionLocacion = null;
		}
		
		if(arrMarkers[2] != null)
		{
			arrMarkers[2].setMap(null);
			arrMarkers[2] = null;
			arrInfoWindows[2].setMap(null);
			arrInfoWindows[2] = null;
		}						
			
		
		indexLocation = 2;

		indexHandler = 1;
		
		$("#dvCtrlPositioning").removeClass("cssCB03Active");
		myLocationFlag = true;		
		objectLocation = 0;
		
		if(flagSE == 1)
		{
			if(idDivL == 1)
				indexStart = -1;
			if(idDivL == 2)
				indexEnd = -1;	
			routeDescriptionRemove();
			$("#dvContTable").html("");					
		}
			
		if((idDivL != 0) && (flagSE == 1))
		{
			flagSE = 0;
			$('#divLine0'+idDivL+'').css('display','none');
                        $('#divLine0'+idDivL+'').addClass("cssOcultaHard");
			//indexEnd = - 1;
			idDivL = 0;
		}		
		//flagSE = 2;
		break;
	
	case 'HANDLER':
		indexHandler = 1;
		
		indexLocation = 2;
		
		objectHandler = 0;
		
		if(flagSE == 2)
		{
			if(idDivH == 1)
				indexStart = -1;
			if(idDivH == 2)
				indexEnd = -1;
				
			routeDescriptionRemove();
			$("#dvContTable").html("");
			
		}
		
		if((idDivH != 0) && (flagSE == 2))
		{
			flagSE = 0;
			$('#divLine0'+idDivH+'').css('display','none');
                        $('#divLine0'+idDivH+'').addClass("cssOcultaHard");
			//indexEnd = - 1;
			idDivH = 0;
		}
		
		break;
	
	default:
		break;
	
	}
					
}

//	markerBubble[index].set("visibility", true);
//	map.update(markerBubble[index]);

pointMarkerBubbleIcon = function(routeIcon, nameIcon, extIcon, index){

	if(arrMarkers[index] != null)
		arrMarkers[index].setIcon(routeIcon + nameIcon  + extIcon);
		
}

/* Attach an event listener to map display
 * push info bubble with coordinate information to map
 */
 var isAlertReady = false;
pointMarkerBubbleAlert = function () 
{
	coordOrigen = new google.maps.LatLng(param['l'], param['n']);
	typeMarker = 'ALERT';
	callBackObtenerDireccion(infoAlarma["coo"][0].dir.toString(),coordOrigen,'OK');
	//urltest
	//http://ialarm.webmaps.mx/Im.php/web?e=ed4663389aded395ac5e0393a190e7d41d897ef2e
//http://localhost/iav2/Im.php/web?e=f6f8XMwBZRaMCrqPl3euHRlPfhbbeQ18VAhHb1XA898=c
}

//Insert point alert
//pointMarkerBubbleAlert();

google.maps.event.addListener(miMapa.mapa, 'rightclick', function(evento) {

	addMarkerBubble(evento);

});

function addMarkerBubble(evento) {

	if(isAlertReady)
	{
	
			if(direccionHandler != null)
				direccionHandler.abort();
				
			coordHandler = evento.latLng;
			typeMarker = 'HANDLER';
			
			pointMarkerBubbleRemove(indexHandler,'HANDLER');	
			
			direccionHandler = $.post('GDir',{latitude: coordHandler.lat(), longitude: coordHandler.lng()},
				function(data){
					var info = JSON.parse(data);
					callBackObtenerDireccion(info.dir.toString(),new google.maps.LatLng(parseFloat(coordHandler.lat()),parseFloat(coordHandler.lng())),'OK')
			});
			//miMapa.obtenerDireccion(coordHandler.lat(), coordHandler.lng(), callBackObtenerDireccion);	
	}

}

/*map.addListener("mouseover", function (evt) {

	_flagEvento = true;
	
});*/

var routeIcon = "../protected/modules/web/themes/ialarm/images/iAlarmDesktop/globe",
	extIcon = ".png",
	iconStart = "Green",
	iconEnd = "Red";

/*documentBasePath = document.location.href;
console.log(documentBasePath = documentBasePath.substring(0, documentBasePath.indexOf('index_mapa.php?')));*/