<?php
   /********************************************************************************
    *   @autor:         (JC) Jose Chavez <jose.chavez@webmaps.com.mx>               *
    *   @updater:                                                                   *
    *   @version:       1.0                                                         *
    *   @created:       25/03/2014                                                  *
    *   @copiright:     Copyright (c) 2014, WebMaps                                 *
    *   @description:   Web Services iAlarm                                         *
    *   @notes:         SMS CallBack - Nextel                                       *
    ********************************************************************************/

   require_once('lib/nusoap/nusoap.php');
   
   // Función para el procesamiento de mensajes
   function notifySmsReception($correlator, $message){

	$file=fopen('access.log','a');
	$datos = "Correlator: ". $correlator;
	foreach($message as $msg => $valor){
		$datos.= " | " . $msg . ": " . $valor;
	}
	$datos.="\n";
	fwrite($file,$datos);
	fclose($file);

	return "OK";   

   }

   // Instancia del Server
   $server = new soap_server();
  
   // Definir NS
   $ns="http://call.ialarm.webmaps.mx";
   $server->configureWSDL('wmServices',$ns);
   $server->wsdl->schematargetnamespace=$ns;

   // Definición de parámetros de entrada		
   $server->wsdl->addComplexType(
	'Msg',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'message' => array('name' => 'message', 'type' => 'xsd:string'), 
		'senderAddress'=>array('name' => 'senderAddress', 'type' => 'xsd:string'),
		'smsServiceActivationNumber'=>array('name' => 'smsServiceActivationNumber', 'type' => 'xsd:string')
	)
   );

   $server->wsdl->addComplexType('arrayMsg',
	'complexType',
	'array',
	'', 
	'SOAP-ENC:Array', 
	array(),
	array(
		array('ref' => 'SOAP-ENC:arrayType', 
		'wsdl:arrayType' => 'tns:Msgs[]')
	),
	'tns:Msgs'
   );


   // Registro Método a WS
   $server->register('notifySmsReception',
                            array('correlator'   => 'xsd:string',
                                  'message'      => 'tns:arrayMsg'),
                            array('response' => 'xsd:string'),
                            $ns); 


   //$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
   //$server->service($HTTP_RAW_POST_DATA); 

   if (isset($HTTP_RAW_POST_DATA)){
      $input = $HTTP_RAW_POST_DATA;
   }
   else{
      $input = implode("\r\n", file('php://input'));
   }
   $server->service($input);
   exit;
?>

