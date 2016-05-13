<?php

function createTxt($filename, $arrydata, $tipoReporte) {
    
    $txtfile = "php://temp/";
    $fp = fopen($txtfile, "r+");
    if (!is_resource($fp)) {
        die("Error al crear $txtfile");
    }
    
    if ($tipoReporte == 'Message') {
        foreach ($arrydata as $key => $value) {
            fwrite($fp, $value['PTN'] . "|" . $value['FECHA'] . "|" . $value['NUMERO'] . "|" . $value['MENSAJE'] . "|" . $value['ESTATUS'] . "\n");
        }
    } else {
        foreach ($arrydata as $key => $value) {
            fwrite($fp, $value['PTN'] . "|" . $value['FECHA'] . "|" . $value['TIPO'] . "|" . $value['CONTRATO'] . "|" . $value['MONTO'] . "|" . $value['ESTATUS']  . "|" . $value['TRANSACTIONID'] ."\n");
        }
    }

    
    //header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    //header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    //header("Cache-Control: no-cache, must-revalidate");
    //header("Pragma: no-cache");
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
	header ("Content-type: text/plain;charset=utf-8");  
    //header("Content-Length: ".filesize($txtfile));  
    
    rewind($fp);
    echo stream_get_contents($fp);
    fclose($fp);
    
    /*
    if ($tipoReporte == 'Message') {
        foreach ($arrydata as $key => $value) {
            echo ($value['PTN'] . "|" . $value['FECHA'] . "|" . $value['NUMERO'] . "|" . $value['MENSAJE'] . "|" . $value['ESTATUS'] . "\n\r");
        }
    } else {
        foreach ($arrydata as $key => $value) {
            echo ( $value['PTN'] . "|" . $value['FECHA'] . "|" . $value['TIPO'] . "|" . $value['CONTRATO'] . "|" . $value['MONTO'] . "|" . $value['ESTATUS']  . "\n\r");
        }
    }
     *      */
    
}

?>
