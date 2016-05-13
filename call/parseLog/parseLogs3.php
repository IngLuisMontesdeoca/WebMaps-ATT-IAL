<?php
error_reporting(0);
header("Content-type: application/x-msexcel; charset=UTF-8");
header("Content-Disposition: attachment; filename=nombreexcel.xls");
require_once("/var/www/dev/ial/parseLog/PHP/excel.php");
require_once("/var/www/dev/ial/parseLog/PHP/excel-ext.php");
$data = [];
$lineC = 0;
for ($i = 1; $i <= 31; $i++) {
    $date = '/var/www/dev/ial/SCReception/201507';
    $date .= ($i < 10) ? '0' . $i : $i;
    $file = fopen($date . "/Activity.log", "r");
    while (!feof($file)) {
        $linea = fgets($file);
        if (strpos($linea, 'Correlator')) {
            $info = explode("-", $linea);
            $info2 = explode("|", $info[1]);
            //echo explode(": ", $info2[0])[0] . "//" . explode(":", $info2[1])[1] . "//" . explode(":", $info2[2])[1] . "//" . explode(":", $info2[3])[1] . PHP_EOL;

            $data[$lineC]['Fecha'] = explode(": ", $info2[0])[0];
            $data[$lineC]['Correlator'] = explode(":", $info2[1])[1];
            $data[$lineC]['Message'] = explode(":", $info2[2])[1];
            $data[$lineC]['SenderAddress'] = explode(":", $info2[3])[1];
            $lineC++;
        }
    }


    fclose($file);
}
    /*
      $data[0]['FECHA'] = 'test';
            $data[0]['CORRELATOR'] = 'test';
            $data[0]['MESSAGE'] = 'test';
            $data[0]['SENDER'] = 'test';
            */
    createExcel("nombreexcel.xls", $data);



