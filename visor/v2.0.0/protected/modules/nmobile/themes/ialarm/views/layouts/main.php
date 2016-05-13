<?php

	$vistaPrincipal = file_get_contents(Yii::app()->basePath.'/modules/nmobile/themes/ialarm/views/layouts/html/index.html');
	
	echo $vistaPrincipal;

	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/nmobile/themes/ialarm/js/mapa/mobile/mapa.js',CClientScript::POS_END);	
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/nmobile/themes/ialarm/js/mapa/mobile/viewMapa.js',CClientScript::POS_END);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/nmobile/themes/ialarm/js/mapa/mobile/trafficMapa.js',CClientScript::POS_END);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/protected/modules/nmobile/themes/ialarm/js/mapa/mobile/paramUrlMapa.js',CClientScript::POS_END);
	
?>
