<?php
	/********************************************************************************
	*   @autor:         (JR) Jorge RodrÃ­guez <jorge.rodriguez@webmaps.com.mx>    	*
	*   @updater:       (LM) Luis Montes <luis.montes@webmaps.com.mx>               *       
	*   @version:       1.0                                                         *
	*   @created:       25/10/2010                              			*
	*   @copiright:     Copyright (c) 2010, WebMaps              			*
	*   @description:   Clase para envio de correo                                  *
	*   @notes:         								*
	********************************************************************************/
	    
  
	require_once ROOT_PHPMAILER;
    
	class Correo
	{//---------------------------------------------------------------------------------->>> Class Menu
		/***
		*   @description:  
		*   @param:        
		*   @return:       
		*   @updater:      
		*   @updated_date: 
		***/
		private $Mail;
		public $bodyMail;
                public  $error = '';
		
		
		function __construct(  )
		{//<<------------------------------------------------------------ construct()
			$this->Mail = new PHPMailer();
                        $this->Mail->Host     = "fleetrackers.com.mx";
                        $this->Mail->Mailer   = "smtp";
                        $this->Mail->SMTPAuth = TRUE;
                        $this->Mail->Username = "soporte@fleetrackers.com.mx";  // a valid email here
                        $this->Mail->Password = "+Mexico01";
		}//<<-------------------------------------------------------- End construct()
		
		/***
		*   @description:  
		*   @param:        
		*   @return:       
		*   @updater:      
		*   @updated_date: 
		***/
		public function makeBodyFromFile( $file="", $arrVariables=array() )
		{//<<------------------------------------------------------------ makeBodyFromFile()
			if(!file_exists($file)){ 
				echo "NO EXISTE ARCHIVO => $file";
			}
			else{			
				$this->bodyMail = file_get_contents($file);
				foreach ($arrVariables as $key => $value) {
					$this->bodyMail = str_replace("{".$key."}", $value,$this->bodyMail);			
				}
			}
			
		}//<<-------------------------------------------------------- End makeBodyFromFile()
		
		/***
		*   @description:  
		*   @param:        
		*   @return:       
		*   @updater:      
		*   @updated_date: 
		***/
		public function makeBody( $body="", $arrVariables=array() )
		{//<<------------------------------------------------------------ makeBody()
			$this->bodyMail = $body;
			foreach ($arrVariables as $key => $value) {
				$this->bodyMail = str_replace("{".$key."}", $value,$this->bodyMail);			
			}
		}//<<-------------------------------------------------------- End makeBody()
		
		/***
		*   @description:  
		*   @param:        
		*   @return:       
		*   @updater:      
		*   @updated_date: 
		***/
		public function showBody( )
		{//<<------------------------------------------------------------ makeBody()		
			return $this->bodyMail;
		}//<<-------------------------------------------------------- End makeBody()
			
		/***
		 *   @description:  
		 *   @param:        
		 *   @return:       
		 *   @updater:      
		 *   @updated_date: 
		 ***/        
		public function sendMail( $arrVariables= array() ){//<<------------------------------------------------------------ sendMail()
                    try{
			if(count($arrVariables)!=0){
				$flag=true;
				$this->Mail->From     =$arrVariables['From']['mail'];
				$this->Mail->FromName = (isset( $arrVariables['From']['name']))? $arrVariables['From']['name']: $arrVariables['From']['mail'];				
				$this->Mail->Subject  = $arrVariables['Titulo'];	
                                $this->Mail->IsHTML(true);
                                if( $arrVariables['embeddedImage'])
                                    $this->Mail->AddEmbeddedImage("/var/www/nxt/ial/srv/parlayx/v1.0.1/css/images/{$arrVariables['embeddedImage']}", $arrVariables['embeddedImage'],"/var/www/nxt/ial/srv/parlayx/v1.0.1/css/Home/images/{$arrVariables['embeddedImage']}",'base64','imageimage/png');
                                $this->Mail->MsgHTML($this->bodyMail);

				$arrTo=$arrVariables['arrTo'];
				foreach($arrTo as $key => $value){
					$name=(isset($value['name']))? $value['name']:$value['mail'];
					$this->Mail->AddAddress($value['mail'], $name);
				}
				
				/*Si falla el envÃ­o, Se hacen 2 intentos mÃ¡s*/
				if(!$this->Mail->Send()){
					if(!$this->Mail->Send()){
						if(!$this->Mail->Send()){
							$flag=false;
							//exit("Ha ocurrido un error, por favor contÃ¡cte con el administrador del sistema (9)");   
						}
					}
				}
				return $flag;
			}
                    }  catch (Exception $e){
                        $this->error = $e;
                    }
		}//<<------------------------------------------------------------ End sendMail()

/***
		 *   @description:  
		 *   @param:        
		 *   @return:       
		 *   @updater:      
		 *   @updated_date: 
		 ***/     
		public function sendMailReports($valores){
			$this->Mail->From     	=	$valores['remit'];
			$this->Mail->FromName 	= $valores['nombre'];	
			$this->Mail->Subject 	= $valores['subject'];
			$this->Mail->MsgHTML($this->bodyMail);
			$this->Mail->AddAddress($valores['correo'], $valores['contacto']);
			$this->Mail->AltBody 	= $valores['alt_body'];
			if ($this->Mail->send()) {
				$respuesta = true;
			} else {
				$respuesta = false;
			}
			return $respuesta;
		} 
		
		/***
		 *   @description:  
		 *   @param:        
		 *   @return:       
		 *   @updater:      
		 *   @updated_date: 
		 ***/     
		public function sendMailAttachfile($valores,$file){
			$this->Mail->From     	= $valores['remit'];
			$this->Mail->FromName 	= $valores['nombre'];	
			$this->Mail->Subject	= $valores['subject'];
			$this->Mail->MsgHTML($valores['body']);
			$this->Mail->AddAddress($valores['correo'], $valores['contacto']);
			$this->Mail->AltBody 	= $valores['alt_body'];
			$this->Mail->AddAttachment($file);
			if ($this->Mail->Send()) {
				$respuesta = true;
			} else {
				$respuesta = false;
			}
			return $respuesta;
		} 
                
                public function AddAddress($correo,$contacto){
                    $this->Mail->AddAddress($correo, $contacto);
                }
		
	}//---------------------------------------------------------------------------------->>> End Class Menu
  
?>
