<?php

$_WS['payment']['mount'] = '0.01';
$_WS['payment']['itemId'] = '581';
$_WS['payment']['itemName'] = 'iAlarmWM';
$_WS['payment']['idContentProvider'] = '110';
$_WS['payment']['itemType'] = 'Streaming_Audio';
$_WS['sendSms']['senderName'] = '425272';

$_WS['payment']['logPath'] = '/var/log/nxt/ial/crn/payment/';
$_WS['payment']['logDetail'] = TRUE;

$_WS['alarmas']['logPath'] = '/var/log/nxt/ial/srv/alarmas/';
$_WS['alarmas']['logDetail'] = TRUE;
$_WS['alarmas']['urlPage']      = 'http://ialarm.webmaps.mx/lm?e=';
$_WS['alarmas']['seed']         = '$N3xt31-/1A14rm$';
$_WS['alarmas']['msg']      = 'Tu servicio iAlarm se encuentra deshabilitado, por favor comunicate con atencion a clientes Nextel, gracias.';

$_WS['sendSms']['logPath'] = '/var/log/nxt/ial/srv/sendSms/';
$_WS['sendSms']['msgSuspend'] = 'Por motivos de falta a pago, tu servicio iAlarm ha sido suspendido, ya que tienes 3 pagos pendientes, favor de cubrir tus pagos pendientes a la brevedad para poder seguir haciendo uso del servicio, gracias.';
$_WS['sendSms']['msgNotice'] = 'Te recordamos que tienes 2 pagos pendientes, por lo tanto solo puedes hacer uso del servicio una vez mas, ya que al acumular 3 pagos pendientes tu servicio sera suspendido, favor de cubrir tus pagos pendientes a la brevedad para poder seguir haciendo uso del servicio iAlarm, gracias.';
$_WS['sendSms']['msgSuspendMensual'] = 'Por motivos de falta a pago, tu servicio iAlarm ha sido suspendido, ya que tienes un retraso de 8 dias en el pago de tu servicio, favor de cubrir tus pagos pendientes a la brevedad para poder seguir haciendo uso del servicio iAlarm, gracias.';
$_WS['sendSms']['msgNoticeMensual'] = 'Te recordamos que tienes 1 retraso de 7 dias en el pago de tu servicio, si no lo cubres antes del dia de mañana tu servicio iAlarm sera suspendido, favor de cubrir tu pago pendiente a la brevedad para poder seguir haciendo uso del servicio iAlarm, gracias.';
$_WS['sendSms']['msgListaNegra'] = 'Por motivos de falta a pago, tu servicio iAlarm ha sido suspendido por un periodo de 90 dias, ya que tienes un retraso de 30 dias en el pago de tu servicio, favor de llamar al servicio de atencion a clientes para cualquier duda o aclaracion, gracias.';
$_WS['sendSms']['logDetail'] = TRUE;
$_WS['sendSms']['urlPage']      = 'http://ialarm.webmaps.mx/lm?e=';
$_WS['sendSms']['urlValidate']      = 'http://ialarm.webmaps.mx/lm?c=';
$_WS['sendSms']['urlGoogle']      = 'https://www.google.com/maps/search/';
$_WS['sendSms']['seed']         = '$N3xt31-/1A14rm$';
$_WS['sendSms']['noThreads']    = 3;
$_WS['sendSms']['regsByThread'] = 2;

$_WS['retryPayment']['logPath'] = '/var/log/nxt/ial/crn/retryPayment/';
$_WS['retryPayment']['logDetail'] = TRUE;

$_WS['checkDevice']['logPath'] = '/var/log/nxt/ial/srv/checkDevice/';
$_WS['checkDevice']['logDetail'] = TRUE;



