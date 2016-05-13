<?php

/* Configuracion WILAEN*/
//$_WS['Charge']['target'] = 'http://184.106.12.88:7050/Nextel_Mx/BillingWebService';
$_WS['Charge']['target'] = 'http://192.168.160.88:7050/Nextel_Mx/BillingWebService';
$_WS['Charge']['ItemId'] = '581';
$_WS['Charge']['ItemName'] = 'iAlarmWM';
$_WS['Charge']['ItemType'] = 'ClubF';
$_WS['Charge']['BillingCode']['UNLIMITED'] = 'PRUEBA_NEX_MX';
$_WS['Charge']['BillingCode']['EVENT'] = 'PRUEBA_NEX_MX';
//$_WS['Charge']['BillingCode']['UNLIMITED'] = 'UNLIMITED_WEBMAPS_NXMX';
//$_WS['Charge']['BillingCode']['EVENT'] = 'EVENT_WEBMAPS_NXMX';
$_WS['SmsReceiver']['target'] = 'http://184.106.12.88:7040/Global/SendMessageService';
$_WS['cert']['user'] = 'WebMaps';
$_WS['cert']['password'] = 'W36m4ps@2014';
$_WS['cert']['partnerId'] = '';

?>