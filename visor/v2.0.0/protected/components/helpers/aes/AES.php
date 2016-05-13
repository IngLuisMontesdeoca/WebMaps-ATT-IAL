<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AES
{
	public static function aes256Base64($data)
	{
            $data = base64_decode(substr(rawurldecode(urlencode($data)), 0, -1));
            $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, Yii::app()->params['kA256B64'], $data, MCRYPT_MODE_CBC, str_repeat("\0", 32));
            
            return $data;	
	}
}