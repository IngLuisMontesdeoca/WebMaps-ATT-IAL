<?php

class Utilities {

    function __construct() {
        
    }

	public function saveToLog($msg = "", $fileName = "log.log"){
		  $file  = fopen($fileName, "a+");
		  fwrite($file, date("d-m-Y H:i:s").":".$msg."\n");
		  fclose($file);
	}
	
    public function generateCode() {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-@?$#%&()=/,.;{}[]>*+";
        $arrChars = str_split($characters);

        $code = "";
        for ($i = 0; $i < 8; $i++) {
            $index = rand(0, count($arrChars) - 1);
            $code .= $arrChars[$index];
        }

        return $code;
    }

    public function getRealIP() {
        if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                $client_ip = (!empty($_SERVER['REMOTE_ADDR']) ) ?
                        $_SERVER['REMOTE_ADDR'] :
                        ( (!empty($_ENV['REMOTE_ADDR']) ) ?
                                $_ENV['REMOTE_ADDR'] :
                                "unknown" );
                $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
                reset($entries);
                while (list(, $entry) = each($entries)) {
                    $entry = trim($entry);
                    if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                        // http://www.faqs.org/rfcs/rfc1918.html
                        $private_ip = array(
                            '/^0\./',
                            '/^127\.0\.0\.1/',
                            '/^192\.168\..*/',
                            '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                            '/^10\..*/');
                        $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
                        if ($client_ip != $found_ip) {
                            $client_ip = $found_ip;
                            break;
                        }
                    }
                }
            }
        } else {
            $client_ip = (!empty($_SERVER['REMOTE_ADDR']) ) ?
                    $_SERVER['REMOTE_ADDR'] :
                    ( (!empty($_ENV['REMOTE_ADDR']) ) ?
                            $_ENV['REMOTE_ADDR'] :
                            "unknown" );
        }

        return $client_ip;
    }

}

?>
