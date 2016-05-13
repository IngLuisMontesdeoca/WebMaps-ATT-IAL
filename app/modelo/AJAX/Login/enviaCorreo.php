<?php

error_reporting(5);
/* * ******************************************************************************
 *   @autor:         Cesar Gonz·lez <cesar.gonzalez@webmaps.com.mx>              *
 *   @version:       1.0                                     					*
 *   @created:       07/02/2014                              					*
 *   @copiright:     Copyright (c) 2010, WebMaps              					*
 *   @description    envia correo para recuperar contraseÒa
 * ****************************************************************************** */

//---- CONFIG ----//
require_once '../../config.php';
require_once '../../../lib/Config/config.php';
//require_once '../../../lib/phpmailer/class.phpmailer.php';
//---- PAQUETES ----//
//---- BD ----//
Package::usePackage('BD');
//---- Tables ----//
Package::import('tables');
//---- Classes ----//
Package::usePackage('Classes');
//---- Utilities----//
Package::import('Utilities');


if (!(Debug::ajaxRequest())) {
    if (is_null(Session::goLogin()))
        header("Location: ../../Home");
    exit();
}

//---- POST ----//

if (isset($_POST['correo']))
    $loginUsuario = $_POST['correo'];
else
    echo utf8_encode('0');

$dominio = 'http://' . $_SERVER["SERVER_NAME"];

$fechaActual = time();
$semilla = $_CONFIG['SEMILLAEMAIL'];
$token = base64_encode(base64_encode($semilla) . "314159265358979323846" . base64_encode($fechaActual));

//---- Instancias ----//
$baseUsuario = new Base_Dat_Usuario();
$baseRecoveryPassword = new Base_Dat_RecoveryPassword();

$infoUsuarioEmail = $baseUsuario->getInfoUsuarioEmail($loginUsuario);

if ($infoUsuarioEmail == '0') {
    echo utf8_encode('0');
    exit();
}

if ($infoUsuarioEmail == '2') {
    echo utf8_encode('2');
    exit();
}



$fechaHoy = date('Y-m-d H:i:s');
$baseRecoveryPassword->setPk(0);
$baseRecoveryPassword->c_recovery_code = $token;
$baseRecoveryPassword->n_usuario_id = (int) $infoUsuarioEmail['id'];
$baseRecoveryPassword->d_recovery_fechacreacion = $fechaHoy;
$baseRecoveryPassword->d_recovery_fechaexpiracion = date('Y-m-d H:i:s', strtotime($fechaHoy) + 86400);

$isSave = $baseRecoveryPassword->save() ? TRUE : FALSE;

if (!($isSave)) {
    echo utf8_encode('0');
    exit();
}


/* * ********Env√≠o de correo para activaci√≥n de la cuenta************ */

/*
$Mail = new PHPMailer();
$bodyMail = file_get_contents('../../../vistas/XHTML/Login/RecoveryPassword.html');
$bodyMail = str_replace("{LINK}", "<a style='color:#E05414' href='{$dominio}/Password?usr=" . md5($semilla . $infoUsuarioEmail['nombre']) . "&token={$token}'>aqu&iacute;</a>.", $bodyMail);
$bodyMail = str_replace("{NOMBRE}", $infoUsuarioEmail['nombre'], $bodyMail);
$bodyMail = str_replace("{LOGIN}", "<a style='color:#1155CC'>" . $infoUsuarioEmail['login'] . "</a>", $bodyMail);
$Mail->Host = "smtp-relay.gmail.com";
$Mail->Mailer = "smtp";
$Mail->SMTPAuth = FALSE;
$Mail->From = 'info@nextel.com.mx';
$Mail->FromName = "Nextel de MÈxico";
$Mail->Subject = "RecuperaciÛn de contraseÒa";*/
//$Mail->IsHTML(true);
//$Mail->AddEmbeddedImage('../../../css/Home/images/imgBanner01.png', 'imgBanner01.png','../../../css/Home/images/imgBanner01.png','base64','imageimage/png');
//$Mail->AddBCC("cesar.gonzalez@webmaps.com.mx");
//$Mail->MsgHTML($bodyMail);
/*
$Mail->AddAddress($infoUsuarioEmail['email']);
 * */
//$Mail->Send();

$correo = new Correo();
$arrVar = array();
$arrVar["{LINK}"] = "<a style='color:#E05414' href='{$dominio}/Password?usr=" . md5($semilla . $infoUsuarioEmail['nombre']) . "&token={$token}'>aqu&iacute;</a>.";
$arrVar["{NOMBRE}"] = $infoUsuarioEmail['nombre'];
$arrVar["{LOGIN}"] = "<a style='color:#1155CC'>" . $infoUsuarioEmail['login'] . "</a>";
$correo->makeBodyFromFile('Login/RecoveryPassword.html', $arrVar);
$arrTo = array();
$arrTo[] = array('mail' => $infoUsuarioEmail['email'], 'name' => $infoUsuarioEmail['nombre']);
$arrVar['arrTo'] = $arrTo;
$arrVar['From'] = array('mail' => "info@nextel.com.mx", 'name' => "AT&T de MÈxico");
$arrVar['Titulo'] = "RecuperaciÛn de ContraseÒa";


if ($correo->sendMail($arrVar)) {
//if($Mail->Send()){
    $baseUsuario->setPk((int) $infoUsuarioEmail['id']);
    $baseUsuario->c_usuario_password = '';
    $isSave = $baseUsuario->save() ? TRUE : FALSE;

//INTENTAR UNA VEZ MAS SALVAR EN CASO DE ERROR
    if (!($isSave)) {
        $baseUsuario->c_usuario_password = '';
        $isSave = $baseUsuario->save() ? TRUE : FALSE;

        if (!($isSave)) {
            echo utf8_encode('0');
        } else {
            echo $stat = utf8_encode('1');
            //$Mail->ClearAddresses();
        }
    } else {
        echo $stat = utf8_encode('1');
        //$Mail->ClearAddresses();
    }
} else {
    echo $stat = utf8_encode('0');
    //$Mail->ClearAddresses();
}
?>