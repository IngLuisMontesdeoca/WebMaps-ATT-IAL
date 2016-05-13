<?php

    /************************************************************
    *                                                           *
    *   @autor:         Julio Mora <julio.mora@skytel.com.mx>   *
    *   @version:       1.0                                     *
    *   @created:       12/01/2008                              *
    *   @copiright:     Copyright (c) 2008, SkyTel              *
    *   @link:          http://localhost/skyWeb/servErr/  Go    *
    *   @description    Construye la Vista del error 403        *
    *                                                           *
    ************************************************************/
    
    //---- REQUIRE ----//
        //Config
            require_once 'config.php';

    $VistaDinamica = new Plantilla('Error/error.html');
    $VistaDinamica->asignaVariables( array(
                                        'titulo'=>'de Servidor 403',
                                        'ERROR'=>'No tiene acceso al archivo especificado'
                                    ) 
                                );
                                                            
    $VistaDinamica->construyeVista();
    $VistaDinamica->getVista(true);

?>