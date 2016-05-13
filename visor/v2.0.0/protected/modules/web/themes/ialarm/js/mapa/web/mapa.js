var objMap = function ()
{
	var instancia = this;
	
	instancia.mapa = null;
	
	instancia.idTag = null;
	
	instancia.trafico = null;
	
	instancia.transporte = null;
	
	instancia.servicioDireccion = null;
	
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
		zoom: 15,
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



	
	/*calcula la ruta a partir de dos geocoordenadas*/
	instancia.obtenerRuta = function (pInicio, pFinal, callBack)
	{
		servicioDireccion = new google.maps.DirectionsService();

		var request = {
			origin: pInicio,
			destination: pFinal,
			travelMode: google.maps.TravelMode.DRIVING,
			optimizeWaypoints: true,
			unitSystem: google.maps.UnitSystem.METRIC
		};
		
		servicioDireccion.route(request, function(response, status) 
		{
			if (status == google.maps.DirectionsStatus.OK) 
			{
				callBack(response);
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

	$('#divLine01').css('display','none');
	$('#divLine02').css('display','none');
	$('#divHeader01').css('display','none');
	$('#dvCOntHistorico').css('display','none');
	$('#dvCOntHistorico').html("");
	$('#btnCalcular01').css('display','none');
	$('#btnLimpiar01').css('display','none');
        $('#dvHIconLoad').removeClass("cssHIconLoad");        

$('#dvHIconLoad').click(function(){
    
    dvHIconLoad();
    
});

function dvHIconLoad()
{
    $('#dvCOntHistorico').html("");
    $("#dvCOntHistorico").addClass("cssLoading03");
    globalBodytableHistorico = "";
    removeMarkersUiEltHistorico();

    $.post('wiLocal',{e: param['e']},
                            function(data){

                            switch(data)
                            {
                                case '0':
                                    alert('No se encontraron registros');
                                break;
                                case '1':
                                    alert('Alarma no valida');
                                break;
                                case '2':
                                    alert('Sin parametro');
                                break;
                                default:

                                    $("#dvCOntHistorico").addClass("cssLoading03");
                                    var infoAlarma = JSON.parse(data);

                                    param['n']=parseFloat(infoAlarma["coo"][0].longitude.toString());
                                    param['l']=parseFloat(infoAlarma["coo"][0].latitude.toString());

                                    globlalInfoHistorico = infoAlarma["his"];
                                    pointMarkerBubbleAlert();							

                                    /*var infoAlarma = JSON.parse(data);
                                    console.log(infoAlarma);
                                    param['n']=infoAlarma["coo"][0].longitude.toString();
                                    param['l']=infoAlarma["coo"][0].latitude.toString();
                                    param['zoom'] = 15;
                                    globlalInfoHistorico = infoAlarma["his"];
                                    pointMarkerBubbleAlert();*/

                                break;
                            }

                        }
            );    
};