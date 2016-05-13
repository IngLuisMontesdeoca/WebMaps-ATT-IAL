<?php

  /*
  * __autoload()
  * @description    Complemento de los Paquetes para acceder dinámicamente a
  *                 las clases contenidas dentro de los mismos.
  * @param          String className->Nombre de la clase a requerir
  */
    function __autoload( $className ) 
    {//<<------------------------------------------- __autoload()
        require_once( "{$className}.class.php" );
    }//<<------------------------------------------- End __autoload()


?>