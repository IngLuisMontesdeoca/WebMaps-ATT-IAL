$('.cssCTCenter').addClass('cssLoading03');
var param = new Array();
var query = window.location.search.substring(1);
var infoAlarma = null;
var parms = query.split('&');
for (var i=0; i<parms.length; i++) {
	var pos = parms[i].indexOf('=');
	if (pos > 0) {
		var key = parms[i].substring(0,pos);
		var val = parms[i].substring(pos+1);
		param[key] = decodeURI(val);
	}
}
var globalContHistorico = 0;
var globlalInfoHistorico = "";
var globalBodytableHistorico = "";

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
                                infoAlarma = JSON.parse(data);
                                
                                param['n']=parseFloat(infoAlarma["coo"][0].longitude.toString());
                                param['l']=parseFloat(infoAlarma["coo"][0].latitude.toString());

                                globlalInfoHistorico = infoAlarma["his"];
                                pointMarkerBubbleAlert();

                            break;
                        }

                    }
	);