<?php

/* * ******************************************************************************
 *                                                           					*
 *   @autor:         (JR) Jorge Rodríguez <jorge.rodriguez@webmaps.com.mx>    	*
 *   @updater:                                                                   *       
 *   @version:       1.0                                                         *
 *   @created:       25/10/2010                              					*
 *   @copiright:     Copyright (c) 2010, WebMaps              					*
 *   @description:	Clase para envio de correo				   					*
 *   @notes:         										 					*
 * ****************************************************************************** */

require_once ROOT_PHPMAILER;

class Correo {//---------------------------------------------------------------------------------->>> Class Menu
    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    private $Mail;
    private $bodyMail;

    function __construct() {//<<------------------------------------------------------------ construct()
        $this->Mail = new PHPMailer();
        $this->Mail->Host = "smtp-relay.gmail.com";
        $this->Mail->Mailer = "smtp";
        $this->Mail->SMTPAuth = FALSE;
    }

//<<-------------------------------------------------------- End construct()

    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    public function makeBodyFromFile($file = "", $arrVariables = array()) {//<<------------------------------------------------------------ makeBodyFromFile()
        $archivo = HTMLCorreo . $file;
        if (!file_exists($archivo)) {
            echo "NO EXISTE ARCHIVO => $archivo";
        } else {
            $this->bodyMail = file_get_contents($archivo);
            foreach ($arrVariables as $key => $value) {
                $this->bodyMail = str_replace("{" . $key . "}", $value, $this->bodyMail);
            }
        }
    }

//<<-------------------------------------------------------- End makeBodyFromFile()

    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    public function makeBody($body = "", $arrVariables = array()) {//<<------------------------------------------------------------ makeBody()
        $this->bodyMail = $body;
        foreach ($arrVariables as $key => $value) {
            $this->bodyMail = str_replace("{" . $key . "}", $value, $this->bodyMail);
        }
    }

//<<-------------------------------------------------------- End makeBody()

    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    public function showBody() {//<<------------------------------------------------------------ makeBody()		
        return $this->bodyMail;
    }

//<<-------------------------------------------------------- End makeBody()

    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    public function sendMail($arrVariables = array(), $file = '') {//<<------------------------------------------------------------ sendMail()
        if (count($arrVariables) != 0) {
            $flag = true;
            $this->Mail->From = $arrVariables['From']['mail'];
            $this->Mail->FromName = (isset($arrVariables['From']['name'])) ? $arrVariables['From']['name'] : $arrVariables['From']['mail'];
            $this->Mail->Subject = $arrVariables['Titulo'];
            $this->Mail->MsgHTML($this->bodyMail);

            $arrTo = $arrVariables['arrTo'];
            foreach ($arrTo as $key => $value) {
                $name = (isset($value['name'])) ? $value['name'] : $value['mail'];
                $this->Mail->AddAddress($value['mail'], $name);
            }
            if ($file != '') {
                $this->Mail->AddAttachment($file);
            }

            /* Si falla el envío, Se hacen 2 intentos más */
            if (!$this->Mail->Send()) {
                if (!$this->Mail->Send()) {
                    if (!$this->Mail->Send()) {
                        $flag = false;
                        //exit("Ha ocurrido un error, por favor contácte con el administrador del sistema (9)");   
                    }
                }
            }
            return $flag;
        }
    }

//<<------------------------------------------------------------ End sendMail()

    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    public function sendMailReports($valores) {
        $respuesta = true;
        $this->Mail->From = $valores['remit'];
        $this->Mail->FromName = $valores['nombre'];
        $this->Mail->Subject = $valores['subject'];
        $this->Mail->MsgHTML($valores['body']);
        $this->Mail->AddAddress($valores['correo'], $valores['contacto']);
        $this->Mail->AltBody = $valores['alt_body'];
        if (!$this->Mail->Send()) {
            if (!$this->Mail->Send()) {
                if (!$this->Mail->Send()) {
                    $respuesta = false;
                    //exit("Ha ocurrido un error, por favor contácte con el administrador del sistema (9)");   
                }
            }
        }
        return $respuesta;
    }

    /*     * *
     *   @description:  
     *   @param:        
     *   @return:       
     *   @updater:      
     *   @updated_date: 
     * * */

    public function sendMailAttachfile($valores, $file) {
        $this->Mail->From = $valores['remit'];
        $this->Mail->FromName = $valores['nombre'];
        $this->Mail->Subject = $valores['subject'];
        $this->Mail->MsgHTML($valores['body']);
        $this->Mail->AddAddress($valores['correo'], $valores['contacto']);
        $this->Mail->AltBody = $valores['alt_body'];
        $this->Mail->AddAttachment($file);
        if ($this->Mail->Send()) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }
        return $respuesta;
    }

}

//---------------------------------------------------------------------------------->>> End Class Menu
?>
