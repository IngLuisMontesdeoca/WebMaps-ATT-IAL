var param = new Array();
var query = window.location.search.substring(1);

var parms = query.split('&');
for (var i=0; i<parms.length; i++) {
	var pos = parms[i].indexOf('=');
	if (pos > 0) {
		var key = parms[i].substring(0,pos);
		var val = parms[i].substring(pos+1);
		param[key] = decodeURI(val);
	}
}

var callBackObtenerDireccion = function(direccion, latlng)
{

	miMapa.mapa.setZoom(15);
	var marker = new google.maps.Marker({
		position: latlng,
		map: miMapa.mapa
	});
					
	miMapa.mapa.setCenter(marker.getPosition());
	document.getElementById("spTooltip").innerHTML = direccion+"<br>Cuadrante: S45 | Nextel: 10907924";

	$("#spBodyFoot").removeClass("cssOculta");

	//tablaRelacion.agregarEventos = new Function();
	//tablaRelacion.agregarEventos=tablaEventosContactos;


};

$.post('nmiLocal',{e: param['e']},
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
                                
                                var infoAlarma = JSON.parse(data);
                                param['n']=parseFloat(infoAlarma[0].longitude.toString());
                                param['l']=parseFloat(infoAlarma[0].latitude.toString());
                                
                                callBackObtenerDireccion(infoAlarma[0].dir.toString(), new google.maps.LatLng(param['l'],param['n']));
                                //miMapa.obtenerDireccion(param['l'], param['n'], callBackObtenerDireccion);

                            break;
                        }

                    }
	);