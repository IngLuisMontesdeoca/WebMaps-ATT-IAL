<?php

	$datoVariables=array($content);
	$etiquetaVariables = array('{content}');
	$vistaPrincipal = file_get_contents(Yii::app()->basePath.'/modules/web/themes/ialarm/views/layouts/html/index.html');
	$vistaFinal = str_replace($etiquetaVariables, $datoVariables, $vistaPrincipal);	
	
	echo $vistaFinal;
	//echo $content;

?>
