var flagSE = 0; //quitar
var globalContHistorico = 0;
$('#dvHIconLoad').addClass("cssHIconLoad");
var arrMarkers = [null,null,null,null];
var arrInfoWindows = [null,null,null,null];
var arrMarkersHistory = new Array();
var arrInfoWindowsHistory = new Array();

var isFirst = true;
	objectHandler = 0,
	objectLocation = 0,
	objectAlert = 0,
	idDivH = 0,
	idDivA = 1,
	idDivL = 0;

var divRuteo = function (idDiv,address,lat,lng,nameMarker){

	var indexNameMarker = 0;
	switch(nameMarker)
	{
		case 'ALERT':
			indexNameMarker = 0;
		break;
		
		case 'HANDLER':
			indexNameMarker = 1;
		break;
		
		case 'LOCATION':
			indexNameMarker = 2;
		break;		

		
	}
	document.getElementById("pAdd_0"+idDiv+"").innerHTML = '<a onclick="centerMarker('+parseFloat(lat)+','+parseFloat(lng)+','+indexNameMarker+',false);" style = "cursor: pointer">'+ address +'<\/a>&nbsp;';
	
	var SE = 0
	if(idDiv == 1)
		SE = "End";
	if(idDiv == 2)
		SE = "Start";
		
	if(flagSE == 1)
	{
		if((idDiv == 1) && (!myLocationFlag))
		{
			$("#dvIconMap02").removeClass("cssGlobeRed");
			$("#dvIconMap02").addClass("cssGlobeOrange");
			$("#dvIconMap01").removeClass("cssGlobeOrange");
			$("#dvIconMap01").addClass("cssGlobeGreen");
			iconEnd = "Orange";
			iconStart = "Green";
		}		
		
		if((idDiv == 2) && (!myLocationFlag))
		{
			$("#dvIconMap01").removeClass("cssGlobeGreen");
			$("#dvIconMap01").addClass("cssGlobeOrange");
			$("#dvIconMap02").removeClass("cssGlobeOrange");
			$("#dvIconMap02").addClass("cssGlobeRed");
			iconStart = "Orange";
			iconEnd = "Red";

		}
	}
	if((flagSE == 2) || (myLocationFlag))
	{
		$("#dvIconMap01").removeClass("cssGlobeOrange");
		$("#dvIconMap02").removeClass("cssGlobeOrange");
		$("#dvIconMap01").addClass("cssGlobeGreen");
		$("#dvIconMap02").addClass("cssGlobeRed");
		iconStart = "Green";
		iconEnd = "Red";
	}
		
	$("#dvInconChange0"+idDiv+"").attr("onclick","set"+SE+"('"+nameMarker+"')");
};

var newMarkerHistorico = function(index, color, address, cooHis){

								var url = window.location.origin+window.location.pathname;
								url = url.replace('/web','/imgSVG?n='+(index+1));
                                                                
								var coordHistorico = new google.maps.LatLng(parseFloat(cooHis.latitude), parseFloat(cooHis.longitude));

								arrMarkersHistory[index] = new google.maps.Marker({
																position: coordHistorico,
																map: miMapa.mapa,
																icon: url,
																zIndex: 1
															});

							var myOptions = {
								content: '<div class="cssTolMaplinfo"><b>Dirección:</b> '+address+'\n<b>Fecha:</b> '+cooHis.fecha+'</div>'
								,disableAutoPan: false
								,maxWidth: 0
								,pixelOffset: new google.maps.Size(0, 0)
								,zIndex: null
								,boxStyle: { 
								  background: "#222222"
								  ,opacity: 0.75
								  ,width: "280px"
								  ,padding:"10px"
								 }
								,closeBoxMargin: "6px 10px 2px 10px"
								,closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
								,infoBoxClearance: new google.maps.Size(1, 1)
								,isHidden: false
								,pane: "floatPane"
								,enableEventPropagation: false
							};						
							
							var ib = new InfoBox(myOptions);
							arrInfoWindowsHistory[index] = ib;
								
							google.maps.event.addListener(arrMarkersHistory[index], 'click', function() {
								_flagEvento = false;
							});
								
							google.maps.event.addListener(arrMarkersHistory[index], 'mouseover', function() {
								for(i=0; i<4; i++)
								{
									if(arrInfoWindowsHistory[i] != null)
										arrInfoWindowsHistory[i].close();
								}
								arrInfoWindowsHistory[index].open(miMapa.mapa, arrMarkersHistory[index]);
							});

							google.maps.event.addListener(arrMarkersHistory[index], 'mouseout', function() {
								if(_flagEvento)
									arrInfoWindowsHistory[index].close();
										
								_flagEvento = true;
								
							});								
								
							//miMapa.mapa.setCenter(arrMarkersHistory[index].getPosition());								

							/*if(!isAlertReady)
							{
								if(param['zoom'])
									map.set('maxZoomLevel',parseFloat(param['zoom']));
								else
									miMapa.mapa.setZoom(15);
							}
							else
								miMapa.mapa.setZoom(15);
							
							isAlertReady = true;
							
							miMapa.mapa.fitBounds(miMapa.mapa.getBounds());								*/
								
};

var newMarker = function(routeIcon, nameIcon, extIcon, address){
	
	var fTypeMarker = typeMarker;
	$.post('toolTip',{direccion: address, tipo: typeMarker},
				function(data){
				

							if(data == '0')
							{
								alert('Error al cargar tooltip');
							}
							else
							{
							
							var index = 0;
							switch(fTypeMarker)
							{
								case 'ALERT' : 
									index = 0;
									arrMarkers[index] = new google.maps.Marker({
										position: coordOrigen,
										map: miMapa.mapa,
										icon: routeIcon+nameIcon+extIcon,
										zIndex: 2
									});									
									miMapa.mapa.setCenter(coordOrigen);									
								break;
								
								case 'HANDLER' : 
									index = 1;
									arrMarkers[index] = new google.maps.Marker({
										position: coordHandler,
										map: miMapa.mapa,
										icon: routeIcon+nameIcon+extIcon
									});									
									miMapa.mapa.setCenter(coordHandler);
								break;								
								
								case 'LOCATION' : 
									index = 2;
									arrMarkers[index] = new google.maps.Marker({
										position: coordLocation,
										map: miMapa.mapa,
										icon: routeIcon+nameIcon+extIcon
									});									
									miMapa.mapa.setCenter(coordLocation);
								break;											
							}
							
							var myOptions = {
								content: data
								,disableAutoPan: false
								,maxWidth: 0
								,pixelOffset: new google.maps.Size(0, 0)
								,zIndex: null
								,boxStyle: { 
								  background: "#222222"
								  ,opacity: 0.75
								  ,width: "280px"
								  ,padding:"10px"
								 }
								,closeBoxMargin: "6px 10px 2px 10px"
								,closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
								,infoBoxClearance: new google.maps.Size(1, 1)
								,isHidden: false
								,pane: "floatPane"
								,enableEventPropagation: false
							};						
							
							var ib = new InfoBox(myOptions);
							arrInfoWindows[index] = ib;
								
							google.maps.event.addListener(arrMarkers[index], 'click', function() {
								_flagEvento = false;
							});
								
							google.maps.event.addListener(arrMarkers[index], 'mouseover', function() {
								for(i=0; i<4; i++)
								{
									if(arrInfoWindows[i] != null)
										arrInfoWindows[i].close();
								}
								arrInfoWindows[index].open(miMapa.mapa, arrMarkers[index]);
							});

							google.maps.event.addListener(arrMarkers[index], 'mouseout', function() {
								if(_flagEvento)
									arrInfoWindows[index].close();
										
								_flagEvento = true;
								
							});								
								
							miMapa.mapa.setCenter(arrMarkers[index].getPosition());								

							if(!isAlertReady)
							{
								if(param['zoom'])
									map.set('maxZoomLevel',parseFloat(param['zoom']));
								else
									miMapa.mapa.setZoom(15);
							}
							else
								miMapa.mapa.setZoom(15);
							
							isAlertReady = true;
								
						}
				}
	);	
	
	if(isFirst)
	{
		$('#divLine02').css('display','block');
                //$('#divHeader01').css('display','block');
		$('#btnCalcular01').css('display','block');
                $('#dvCOntHistorico').css('display','block');
                $('#btnLimpiar01').css('display','block');
		isFirst = false;
	}

};
var updateSE = function()
{
			if(flagSE == 1)
			{
			if(idDivL == 1)
				indexStart = indexLocation;
			if(idDivL == 2)
				indexEnd = indexLocation;	
			}
			if(flagSE == 2)
			{
			if(idDivH == 1)
				indexStart = indexHandler;
			if(idDivH == 2)
				indexEnd = indexHandler;
			}
};
var contadorZindex = 0;
function centerMarker(lat,lng, cZIndex, his)
{
	miMapa.mapa.setZoom(20);
	miMapa.mapa.setCenter(new google.maps.LatLng(parseFloat(lat), parseFloat(lng)));
	
	if(his)
		arrMarkersHistory[cZIndex].setOptions({zIndex: (arrMarkersHistory.length + arrMarkers.length+contadorZindex)});
	else
		arrMarkers[cZIndex].setOptions({zIndex: (arrMarkersHistory.length + arrMarkers.length+contadorZindex)});
		
		contadorZindex = contadorZindex + 1;
	
}


var		callBackObtenerDireccion = function (direccion, latlng, estatus) {

					switch (typeMarker)
					{
						case 'LOCATION':

								newMarker(routeIcon, 'Orange', extIcon, direccion);
								indexLocation = 2;

								objectLocation = {address: direccion, 
													coord: coordLocation,
													index: indexLocation};
								updateSE();
							

							break;

						case 'HANDLER':

								newMarker(routeIcon, 'Blue', extIcon, direccion);
								indexHandler = 1;
									
								objectHandler = {address: direccion,
													coord: coordHandler,
													index: indexHandler};
								updateSE();

							break;

						case 'ALERT':
							if (indexAlert == 0)
							{
								indexAlert = 0;
								newMarker(routeIcon, 'Red', extIcon, direccion);
								divRuteo('2', direccion,coordOrigen.lat(),coordOrigen.lng(),'ALERT');
								objectAlert = {address: direccion, 
													coord: coordOrigen, 
													index: indexAlert};
                                                                                            
								document.getElementById("aPrevHistorico").innerHTML = '<a onclick="centerMarker('+coordOrigen.lat()+','+coordOrigen.lng()+','+indexAlert+',false);" style = "cursor: pointer">'+ direccion +'<\/a>&nbsp;';
                                  
								if(parseInt(globlalInfoHistorico) != 0)
								{
									typeMarker = 'HISTORY';
									callBackObtenerDireccion(null,null,null);
								}
								else
									$("#dvCOntHistorico").removeClass("cssLoading03");
							}
							else
							{
								alert('error');
							}
							break;
							
						case 'HOME':

							break;	
                                                    
						case 'HISTORY':

                                                //console.log(globalContHistorico);
                                                //console.log(estatus);
                                                $('#dvHIconLoad').removeClass("cssHIconLoad");
                                                $("#dvHIconLoad").unbind('click');

						globalContHistorico = globalContHistorico + 1;
						var indexGCH = globalContHistorico - 1;
						var sizeGCH = globlalInfoHistorico.length;
						//console.log(indexGCH);
						if(indexGCH < globlalInfoHistorico.length)
						{
							callBackObtenerDireccion(globlalInfoHistorico[indexGCH].dir,new google.maps.LatLng(parseFloat(globlalInfoHistorico[indexGCH].latitude), parseFloat(globlalInfoHistorico[indexGCH].longitude)),"OK");
							globalContHistorico = globalContHistorico + 1;
						}
							//globalContHistorico = globalContHistorico + 1;							

                                                var indexH = sizeGCH - indexGCH;
												
                                                if(indexH < sizeGCH)
                                                {
                                    
                                                    newMarkerHistorico(indexH,'#E2001A',direccion,globlalInfoHistorico[indexH]);
                                                    var zebra = "";
                                                    
                                                    if(indexH%2)
                                                        zebra = "1";
                                                    else
                                                        zebra = "2";
                                                        
                                                    globalBodytableHistorico+='<tr class="cssZebra0'+zebra+'">'+
																					'<td class="cssTdHisto01">'+
																						(sizeGCH - (indexH))+
																					'</td>'+
																					'<td class="cssTdHisto02">'+
																						'<a onclick="centerMarker('+parseFloat(globlalInfoHistorico[indexH].latitude)+','+parseFloat(globlalInfoHistorico[indexH].longitude)+','+(sizeGCH - (indexH+1))+',true);" style = "cursor: pointer">'+ direccion +'<\/a>&nbsp;'+
																					'</td>'+
																					'<td class="cssTdHisto03">'+
																						globlalInfoHistorico[indexH].fecha+
																					'</td>'+
																				'</tr>';                                                                                       

                                                    typeMarker = 'HISTORY';
													
                                                    
                                                    globalContHistorico = globalContHistorico + 1;                                                     

                                                }

                                                if(indexH == sizeGCH)
                                                {    
                                                    $('.cssCTCenter').removeClass('cssLoading03');
                                                    $("#dvCOntHistorico").removeClass("cssLoading03");
                                                    $("#dvCOntHistorico").html("<table class='cssTblHistorico01'>"+
                                                        "<tbody>"+
                                                        globalBodytableHistorico+
                                                        "</tbody>"+
                                                        "</table>"
                                                        );
                                                    globalContHistorico = 0;
                                                    
                                                    $('#dvHIconLoad').addClass("cssHIconLoad");
                                                    $('#dvHIconLoad').click(function(){

                                                        dvHIconLoad();

                                                    });   
                                                    miMapa.mapa.fitBounds(miMapa.mapa.getBounds());
                                                }
                                                
                                                break;

						default:
							alert("El tipo de marca no es valida");
						break;
					}
		};