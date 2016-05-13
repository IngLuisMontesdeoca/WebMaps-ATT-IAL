<?php

    /************************************************************
    *                                                           *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   *
    *   @version:       1.0                                     *
    *   @created:       12/01/2008                              *
    *   @copiright:     Copyright (c) 2008, SkyTel              *
    *   @link:          http://localhost/skyWeb/servErr/  Go    *
    *   @description    Construye una Vista para el error 404   *
    *                                                           *
    ************************************************************/
    
    //---- REQUIRE ----//
        //Config
            require_once 'config.php';

    $VistaDinamica = new Plantilla('Error/error.html');
    $VistaDinamica->asignaVariables( array(
                                        'titulo'=>'de Servidor 400',
                                        'ERROR'=>'No se encuentra el archivo solicitado en el Servidor'
                                    ) 
                                );
                                                            
    $VistaDinamica->construyeVista();
    $VistaDinamica->getVista(true);

?>