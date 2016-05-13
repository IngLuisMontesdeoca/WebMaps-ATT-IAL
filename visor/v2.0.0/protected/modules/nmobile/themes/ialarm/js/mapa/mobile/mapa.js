$(document).ready(function(){

//	var miMapa = new objMap();
//	miMapa.idTag = "divMapArea";
//	miMapa.crear();
//	miMapa.fijarTipoMapa(0);
//	miMapa.mostrarTrafico();
//	miMapa.ocultarTrafico();
//	miMapa.mostrarTransporte();
//	miMapa.ocultarTransporte();
	
});

var objMap = function ()
{
	var instancia = this;
	
	instancia.mapa = null;
	
	instancia.idTag = null;
	
	instancia.trafico = null;
	
	instancia.transporte = null;
	
	/*variable que contiene los tipos de mapa*/
	instancia.tipoMapa = [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.TERRAIN];
	
	/*
		Metodo que fija el tipo de mapa
		@param idTipoMapa	recibe el id del tipo de mapa
	*/
	instancia.fijarTipoMapa = function(idTipoMapa)
	{
		if((idTipoMapa < 4) && (idTipoMapa >= 0))
			instancia.mapa.setMapTypeId(instancia.tipoMapa[idTipoMapa]);
		else
			instancia.mapa.setMapTypeId(instancia.tipoMapa[0]);
	};
	
	/*variable con las opciones iniciales del mapa default*/
	instancia.opcionesMapa = 
	{
		zoom: 12,
		center: new google.maps.LatLng(19.419444, -99.145556),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		disableDefaultUI: true,
//		panControl: false,
		zoomControl: true,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.SMALL,
			position: google.maps.ControlPosition.RIGHT_CENTER
		}		
//		mapTypeControl: false,
//		scaleControl: false,
//		streetViewControl: false,
//		overviewMapControl: false		
	};
	
	/*muestra la capa de trafico*/
	instancia.mostrarTrafico = function()
	{
		if(instancia.trafico == null)
			instancia.trafico = new google.maps.TrafficLayer();
	
		instancia.trafico.setMap(instancia.mapa);
	};
	
	/*oculta la capa de trafico*/
	instancia.ocultarTrafico = function()
	{
		if(instancia.trafico != null)
			instancia.trafico.setMap(null);
	};	
	
	/*muestra la capa de transporte*/
	instancia.mostrarTransporte = function()
	{
		if(instancia.transporte == null)
			instancia.transporte = new google.maps.TransitLayer();
		
		instancia.transporte.setMap(instancia.mapa);
	};
	
	/*oculta la capa de transporte*/
	instancia.ocultarTransporte = function()
	{
		if(instancia.transporte != null)
			instancia.transporte.setMap(null);
	};


	/*reverseGeocoding obtiene la direccion a partir de las cordenadas*/
	instancia.obtenerDireccion = function (lat, lng, callBack) 
	{

		var geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(lat, lng);
		
		geocoder.geocode({'latLng': latlng}, function(results, status) 
		{
			if (status == google.maps.GeocoderStatus.OK) 
			{
				if (results[1]) 
				{
				
					callBack(results[1].formatted_address, latlng);

				} 
				else 
				{
					alert('No se encontró resultado');
				}
			} 
			else 
			{
				alert('Geocodificación falló debido a: ' + status);
			}
		});
	};
	
	
	
	/*crea el mapa*/
	instancia.crear = function()
	{
			if(instancia.idTag == null)
				alert("Define el identificador donde se muestra el mapa");
			else
				instancia.mapa = new google.maps.Map(document.getElementById(instancia.idTag), instancia.opcionesMapa);
	};

};


	var miMapa = new objMap();
	miMapa.idTag = "divMapArea";
	miMapa.crear();