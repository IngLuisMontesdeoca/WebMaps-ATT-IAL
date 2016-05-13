	/********************************************************************************
	 *																				*
     *	@autor:			César I. G. Pérez <cesar.gonzalez@webmaps.com.mx>			*
	 *	@alias:			CIGP														*
     *	@version:		1.0															*
     *	@created:		28/05/2013													*
     *	@copiright:		Copyright (c) 2010, WebMaps									*
     *	@description:	Funciones para el zoom del mapa								*
     *																				*
     ********************************************************************************/

	/**
	 * oculta el boton zin
	 */
	function hideZin(){
		$("#zin").css('display','none');
	}
	
	/**
	 * oculta el boton zin
	 */
	function hideZout(){
		$("#zon").css('display','none');
	}	
	
	/**
	 * muestra el boton zin
	 */
	function showZin(){
		//$("#zin").css('display','block');
		$("#zin").css({'display': 'block', 'display': 'inline'})
		
	}	

	/**
	 * muestra el boton zon.
	 */
	function showZout(){
		$("#zon").css('display','inline');
	}	
	
	/**
	 * muestra titulo del mapa.
	 */
	function showTitulo(){
		$("#myDivTitulo").css('display','block');
	}	

	/**
	 * muestra leyenda.
	 */
	function showLeyenda(){
		$("#myDivLeyenda").css('display','block');
	}		
	
	/**
	 * muestra la imagen del mapa.
	 */
	function showMap(){
		$("#myDiv").css('display','block');
	}		
	
	/**
	 * oculta la imagen del mapa.
	 */	
	function hideMap(){
		$("#myDiv").css('display','none');
	}
	
	/**
	 * muestra carga imagen del mapa.
	 */
	function showLoadingMap(){
		$("#myDivLoad").css('display','block');
	}		
	
	/**
	 * oculta carga imagen del mapa.
	 */	
	function hideLoadingMap(){
		document.getElementById("myDivLoad").style.display = "none";
	}		
	
	/**
	 * muestra ´texto dirección y Ubicación.
	 */
	function showHead(){
		$("#head").css('display','block');
	}		

	/**
	 * incicializa la variable para zoom global
	 */
	var zz='zoomInit';
	
	/**
	 * inicializa la variable de apoyo en las condiciones para zoom
	 */
	var mapa='omobile';	

	/**
	 * realiza en zoom in a la imagen del mapa
	 * @param float $lt parametro que contiene latitud
	 * @param float $ln parametro que contiene longitud
	 * @param string $address dirección del pois
	 */
	function zoomIn(ln,lt,z,address)
	{
		showLoadingMap();
		hideMap();
		
		if(zz=='zoomInit')
			zz=z;

		if((zz-z)<3){
			zz++;
			showZout();
		}
		if((zz-z)==2)
			hideZin();

		$.ajax({
			type: "GET",
			url: mapa+'?n='+ln+'&l='+lt+'&id=0&zoom='+zz+'&button=0'+'&address='+address+'&map=1',
			data: {},
			success: function(data) {
						$('#myDivMap').html(data);
					}
		});
	}

	/**
	 * realiza en zoom out a la imagen del mapa
	 * @param float $lt parametro que contiene latitud
	 * @param float $ln parametro que contiene longitud
	 * @param string $address dirección del pois
	 */	
	
	function zoomOut(ln,lt,z,address)
	{
		showLoadingMap();
		hideMap();
		
		if(zz=='zoomInit')
			zz=z;

		if((zz-z)>-2){
			zz--;
			showZin();
		}
		if(((zz-z)==-2) || (zz<=0))
			hideZout();
		
		$.ajax({
			type: "GET",
			url: mapa+'?n='+ln+'&l='+lt+'&id=0&zoom='+zz+'&button=0'+'&address='+address+'&map=1',
			data: {},
			success: function(data) {
						$('#myDivMap').html(data);
					}
		});
	}

	function loadBody(z)
	{
		if(z>0)
			showZout();
		
		showZin();
		showTitulo();
		showLeyenda();
		hideLoadingMap();
		showMap();
		showHead();
	
	}
	
    function preloader(){
		document.getElementById("loading").style.display = "none";
	}//preloader
	//window.onload = preloader;
	
	function preloaderMap(){
		hideLoadingMap();
	}//preloader