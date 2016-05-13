<?php
  //ini_set('soap.wsdl_cache_enabled', '0');

   require_once('lib/nusoap.php');
   
   $oSoap = new nusoap_client('https://ubuntu.server.com/ws/Server.php?wsdl'); 
   
$err = $oSoap->getError();
if ($err)
{
	echo '<p><b>Error: ' . $err . '</b></p>';
}

$dato = $oSoap->call('HolaMundo',array('nombre' => 'JChavez'),'https://ubuntu.server.com/ws/Server');

if ($oSoap->fault)
{
	echo "Error al llamar el metodo<br/>".$oSoap->getError();
}
else 
{
	echo $dato;
}


?>
