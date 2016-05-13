<?php

   class Base{
   
      private $conn;
      public $errorMessage;
	  public $connEstatus = FALSE;
   
      function __construct(){
	     $arrDB = parse_ini_file("DB.ini");
		 
		 $this->conn = new mysqli($arrDB["HOST"], $arrDB["USER"], $arrDB["PASSWORD"], $arrDB["DATABASE"]);
		 if ($this->conn->connect_errno) {
            $this->errorMessage = $this->conn->connect_error;
            $this->conn = FALSE;
			$this->connEstatus = FALSE;
         }
		 $this->connEstatus = TRUE;
	  }
	  
	  function existeAlarma($ID = ''){
	     $arrConf = parse_ini_file("Config.ini");
	     $query = "SELECT n_alarma_id FROM dat_alarma WHERE n_estatus_id IN (1, 9) AND SHA1(CONCAT(SHA1(n_handset_id), SHA('".$arrConf["SEMILLA"]."'))) = '".$ID."' LIMIT 1;";
		 $idAlarma = 0;
		 if ($result = $this->conn->query($query)){
		    while($row = $result->fetch_assoc()){
               $idAlarma = $row['n_alarma_id'];
            }
			$result->free();
		 }
		 return $idAlarma;
	  }
	  
	  function decodeHandset($ID = ''){
	     $arrConf = parse_ini_file("Config.ini");
	     $query = "SELECT n_handset_id FROM dat_handset WHERE SHA1(CONCAT(SHA1(n_handset_id), SHA('". $arrConf["SEMILLA"]."'))) = '".$ID."' AND n_estatus_id = 3;";
		 $idHandset = 0;
		 if ($result = $this->conn->query($query)){
		    while($row = $result->fetch_assoc()){
               $idHandset = $row['n_handset_id'];
            }
			$result->free();
		 }
		 return $idHandset;
	  }
	  
	  function radio($tipoLocalizacion = 0){
	     $query = "SELECT n_tipolocalizacion_radio FROM cat_tipolocalizacion WHERE n_tipolocalizacion_id = ".$tipoLocalizacion.";";
		 $radio = 0;
		 if ($result = $this->conn->query($query)){
		    while($row = $result->fetch_assoc()){
               $radio = $row['n_tipolocalizacion_radio'];
            }
			$result->free();
		 }
		 return $radio;
	  }
	  
	  function save($idHandset = 0, $lon = 0, $lat = 0, $radio = 0, $fecha = '0000-00-00 00:00:00'){
	     $query = "INSERT INTO dat_alarma(d_alarma_fechainicio, 
		                                  d_alarma_fechafin, 
										  d_alarma_fechaultimoreporte, 
										  d_alarma_longitude, 
										  d_alarma_latitude, 
										  n_alarma_radio, 
										  n_handset_id, 
										  n_estatus_id) 
								VALUES(NOW(), '0000-00-00 00:00:00', '".$fecha."', ".$lon.", ".$lat.", ".$radio.", ".$idHandset.",9);";
		 
		 return mysqli_query($this->conn, $query);						
	  }
	  
	  function update($idAlarma = 0, $lon = 0, $lat = 0, $radio = 0, $fecha = '0000-00-00 00:00:00'){
	     $query = "UPDATE dat_alarma SET d_alarma_fechaultimoreporte='".$fecha."', 
		                                 d_alarma_longitude=".$lon.",
										 d_alarma_latitude=".$lat.",
										 n_alarma_radio = ".$radio." WHERE n_alarma_id = ".$idAlarma.";";
		 
		 return mysqli_query($this->conn, $query);
	  }
	  
	  function cancelAlarm($idAlarma = 0){
	     $query = "UPDATE dat_alarma SET n_estatus_id = 2 WHERE n_alarma_id = ".$idAlarma.";";
		 return mysqli_query($this->conn, $query);
	  }
	  
	  function cancelarMensajes($idHandset = 0){
	     $query = "UPDATE dat_envioalarma SET n_envioalarma_mensajesenviados = 0, n_estatus_id = 4 WHERE n_handset_id = ".$idHandset.";";
	     $this->errorMessage= $query;
		 return mysqli_query($this->conn, $query);
	  }
	  
	  function addContact($idHandset = 0, $nombre = '', $dato = '', $tipo = 0){
	     $query = "INSERT INTO dat_contacto(c_contacto_nombre,
                                            c_contacto_numerocorreo,
                                            c_contacto_tipocontacto,
                                            d_contacto_fechamodificacion,
                                            n_estatus_id,
                                            n_handset_id)
                                     VALUES('".$nombre."',
                                            '".$dato."',
                                            '".$tipo."',
                                            NOW(),
                                            3,
											".$idHandset.");";
	     return mysqli_query($this->conn, $query);
	  }
	  
	  function disableContacts($idHandset = 0){
	     $query = "UPDATE dat_contacto SET n_estatus_id = 5 WHERE n_handset_id = ".$idHandset.";";
		 return mysqli_query($this->conn, $query);
	  }
	  
	  function getContacts($idHandset = 0){
	     $query = "SELECT c_contacto_nombre, c_contacto_tipocontacto, c_contacto_numerocorreo FROM dat_contacto WHERE n_handset_id = ".$idHandset." AND n_estatus_id = 3;";
	     $arrContactos = array();
		 $i = 0;
		 if ($result = $this->conn->query($query)){
		    while($row = $result->fetch_assoc()){
               $arrContactos[$i]["Contacto"] = $row['c_contacto_nombre'];
			   $arrContactos[$i]["Tipo"]     = $row['c_contacto_tipocontacto'];
			   $arrContactos[$i]["Valor"]    = $row['c_contacto_numerocorreo'];
			   $i++;
            }
			$result->free();
		 }
		 return $arrContactos;
	  }
	  
	  function updateConfigHandset($idHandset = 0, $PIN = '', $silenciosa = 0, $duracion = 0, $asunto = "", $msg = ""){
	     $query = "UPDATE dat_handset SET c_handset_pin = '".$PIN."', 
		                                  c_handset_alertasilenciosa = ".$silenciosa.", 
										  n_handset_duracion = ".$duracion.", 
										  c_handset_asunto = '".$asunto."', 
										  c_handset_mensaje = '".$msg."',
										  n_handset_intervalo = 5
                   WHERE n_handset_id = ".$idHandset." 
				   AND n_estatus_id = 3;";
	     
		 return mysqli_query($this->conn, $query);
	  }
	  
	  function getActivationCode($clientCode = '', $serverCode = ''){
	     $arrConf = parse_ini_file("Config.ini");
		 $code = "";
	     $query = "SELECT SHA1(CONCAT(SHA1(n_handset_id), SHA1('".$arrConf["SEMILLA"]."'))) AS CODE 
                   FROM dat_handset 
                   WHERE c_handset_clientcode = '".$clientCode."' 
                   AND c_handset_servercode = '".$serverCode."' 
                   AND b_handset_flagcode = 0 
                   AND n_estatus_id = 3 LIMIT 1;";
		  
		  if ($result = $this->conn->query($query)){
		    while($row = $result->fetch_assoc())
               $code = $row['CODE'];
			$result->free();
		  }
		  
		  return $code;
	   }
	  
	  function getConfigHandset($idHandset = 0){
	     $query = "SELECT c_handset_pin, 
                          c_handset_alertasilenciosa, 
                          n_handset_duracion, 
                          n_handset_intervalo, 
                          c_handset_asunto, 
                          c_handset_mensaje 
                   FROM dat_handset 
                   WHERE n_estatus_id = 3 
                   AND n_handset_id = ".$idHandset." LIMIT 1;";
	     $arrConfig = array();
		 if ($result = $this->conn->query($query)){
		    while($row = $result->fetch_assoc()){
               $arrConfig["PIN"]        = $row['c_handset_pin'];
			   $arrConfig["SILENCIOSA"] = $row['c_handset_alertasilenciosa'];
			   $arrConfig["DURACION"]   = $row['n_handset_duracion'];
			   $arrConfig["INTERVALO"]  = $row['n_handset_intervalo'];
			   $arrConfig["ASUNTO"]     = $row['c_handset_asunto'];
			   $arrConfig["MSG"]        = $row['c_handset_mensaje'];
            }
			$result->free();
		 }
		 return $arrConfig;
	  }
	  
	  function message($idHandset = 0, $hashCoordinates = ""){
	     $query = "INSERT INTO dat_mensajes(n_handset_id, c_mensaje_tipo, c_mensaje_mensaje) VALUES(".$idHandset.", '2', '".$hashCoordinates."');";
		 return mysqli_query($this->conn, $query);
	  }
	  
	  function disableActivationCode($hashHandset = ''){
	     $arrConf = parse_ini_file("Config.ini");
		 $query = "UPDATE dat_handset SET b_handset_flagcode = 1 WHERE SHA1(CONCAT(SHA1(n_handset_id), SHA1('".$arrConf["SEMILLA"]."'))) = '".$hashHandset."';";
	     return mysqli_query($this->conn, $query);
	  }
	  
	  function close(){
	     $this->conn->close();
	  }
	  
   }
   
?>