	var zoomIni;
	var zoomAct = -1;
	var delta = 0;
	var zoomAnt = -1;
        try
        {
            var zoomUi = window.parent.miMapa.mapa.getZoom();
        }
        catch(err)
        {}

	 $(function() {
		var slider = $( "#slider" ).slider({
				min: 2,
				max: 20,
				range: "min",
				value: window.parent.miMapa.mapa.getZoom(),
				slide: function( event, ui ) {

                                try
                                {
                                    if((window.parent.miMapa.mapa.getZoom()-ui.value) < 0)
                                    {
											window.parent.miMapa.mapa.setZoom(ui.value);
                                            zoomUi = window.parent.miMapa.mapa.getZoom();
                                    }

                                    if((window.parent.miMapa.mapa.getZoom()-ui.value) > 0)
                                    {
											window.parent.miMapa.mapa.setZoom(ui.value);
                                            zoomUi = window.parent.miMapa.mapa.getZoom();	
                                    }
                                }
                                catch(err)
                                {}
			}
		});


		zoomIni = function(zoomUi){
			slider.slider( "value", zoomUi);
		};
		
		$(document).ready(function(){
		
		//window.parent.map.addListener("mapviewchangeend", function (evt) {
		
                        try
                        {
                            zoomIni(window.parent.miMapa.mapa.getZoom());
                        }
                        catch(err)
                        {}
                        if(window.parent.removeHomeFlag)
                                window.parent.cssRemoveHome();
                        else
                                window.parent.removeHomeFlag = true;
			
			$(".ui-slider-handle").removeClass("ui-state-active");
	
		//});
		});
		
});
	 	 
$(document).ready(function(){
	
	$("#dvCtrlZoomIn").click(function(){
	
		if (zoomUi != 20)
		{
                        try
                        {
                            zoomUi = window.parent.miMapa.mapa.getZoom() + 1;
                        }
                        catch(err)
                        {}
			zoomIni(zoomUi);
			window.parent.miMapa.mapa.setZoom(zoomUi);
		}	
	
	});
});

$(document).ready(function(){

	$("#dvCtrlZoomOut").click(function(){
	
		if (zoomUi != 2)
		{
                        try
                        {
                            zoomUi = window.parent.miMapa.mapa.getZoom() - 1;
                        }
                        catch(err)
                        {}
			zoomIni(zoomUi);
			window.parent.miMapa.mapa.setZoom(zoomUi);
		}	
	
	});
});


		//window.parent.map.addListener("mapviewchangestart", function (evt) {
		
			$(".ui-slider-handle").addClass("ui-state-active");
	
		//});

	window.parent.google.maps.event.addListener(window.parent.miMapa.mapa, 'zoom_changed', function() {
	
		if(window.parent.flagClickHome)
			window.parent.cssRemoveHome();
		
		//console.log('zoom_changed');
		//console.log(window.parent.miMapa.mapa.getZoom());
		zoomAct = window.parent.miMapa.mapa.getZoom();
		
		if(zoomAct < 2)
			window.parent.miMapa.mapa.setZoom(2);
			
		if(zoomAct > 20)
			window.parent.miMapa.mapa.setZoom(20);		
//console.log(zoomAct);
//console.log(zoomAnt);
//console.log(delta);
		if((delta == 0) && ( (zoomAct<=20) && (zoomAct>=2)))
		{
			zoomAnt = zoomAct;
			delta = 1;
		}
		else
		{
			if((zoomAct<=20) && (zoomAct>=2))
			{
				delta = zoomAct - zoomAnt;
				
				//if(delta > 0)
				//{
					try
					{
						zoomUi = zoomAct;
						zoomAnt = zoomAct;
                                                zoomIni(zoomUi);
					}
					catch(err)
					{}			
				//}
					
				/*if(delta < 0)
				{
					try
					{
						zoomUi = zoomAct;
						zoomAnt = zoomAct;
					}
					catch(err)
					{}
					zoomIni(zoomUi);			
				}*/
			}
		}
		
	});				
		
/*window.parent.map.addListener("mousewheel", function (evt) {

if (evt.wheelDelta == 1)
{
	if (zoomUi != 20)
	{
                try
                {
                    zoomUi = window.parent.miMapa.mapa.getZoom() + 1;
                }
                catch(err)
                {}
		zoomIni(zoomUi);
		window.parent.map.set("zoomLevel", zoomUi);
	}
}
else
{
	if (window.parent.miMapa.mapa.getZoom() > 2)
	{
                try
                {
                    zoomUi = window.parent.miMapa.mapa.getZoom() - 1;
                }
                catch(err)
                {}
		zoomIni(zoomUi);
		window.parent.map.set("zoomLevel", zoomUi);
	}	
}

});*/