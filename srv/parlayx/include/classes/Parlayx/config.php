<?php

$_WS['amountCharging']['target']     = 'https://sdpvas.nexteldata.com.mx:7443/parlayx-payment/services/AmountChargingService';
$_WS['amountCharging']['Username']   = 'payment@P-JTcIRUEbb7Y';
$_WS['amountCharging']['Password']   = 'pay@webmaps';

$_WS['determinePayment']['target']   = 'https://sdpvas.nexteldata.com.mx:8443/SmdbWebService/subscriber';
$_WS['determinePayment']['Username'] = 'WEBMAPS_MX';
$_WS['determinePayment']['Password'] = 'pay@webmaps';

$_WS['determineSubmarket']['target'] = 'https://129.192.129.104:8080/sigservices/lookup';

$_WS['sendSms']['target']            = 'https://sdpvas.nexteldata.com.mx:7443/parlayx-sms-send/services/SendSmsService';
$_WS['sendSms']['Username']          = 'P-JTcIRUEbb7Y';
$_WS['sendSms']['Password']          = 'W3bm1ps';

$_WS['cert']['path'] = '/var/www/dev/ial/srv/parlayx/v1.0.1/include/classes/Parlayx/ssl/client.pem';
$_WS['cert']['passphrase'] = 'pay@web';
$_WS['cert']['login'] = 'WEBMAPS_MX';
$_WS['cert']['password'] = 'pay@Web';

?>