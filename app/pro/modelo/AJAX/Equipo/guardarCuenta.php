<?php
error_reporting (5); 

    /********************************************************************************
    *   @autor:         Cesar González <cesar.gonzalez@webmaps.com.mx>              *
    *   @version:       1.0                                     					*
    *   @created:       07/02/2014                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description    Guarda Cuenta			                                *
    ********************************************************************************/

   //---- CONFIG ----//
   require_once '../../config.php';
   
   //---- PAQUETES ----//
	   //---- BD ----//
	   Package::usePackage('BD');
	      //---- Tables ----//
	      Package::import('tables');
           //---- Classes ----//
	   Package::usePackage('Classes');
	      //---- Debug ----//
	      Package::import('Debug');
            
	//---Validar inicio de sesión       
         if(!(Debug::ajaxRequest()))
         {
                 if(is_null(Session::goLogin()))
                     header("Location: ../../Home");
                 
                 exit();
         }
         else
         {
             if(!(isset($_SESSION['idUsuario'])))
             {
                echo utf8_encode('SIN SESION');
                exit();
             }
         }
              
            //ASIGNAR VARIABLES
            $zero = utf8_encode('0');
            $idCliente = '';
            $nombreCuenta = '';
            $baseCuenta = NULL;
            $relCuentaCliente = NULL;
            $isCuenta = FALSE;
            $isSave = FALSE;
            $idCuenta = '';
            
            
            if(isset($_POST['idEmpresa']))
                $idCliente = $_POST['idEmpresa'];
            else
            {
                echo $zero;
                exit();
            }
            
            if(isset($_POST['nombreEmpresa']))
                $nombreCliente = $_POST['nombreEmpresa'];
            else
            {
                echo $zero;
                exit();
            }            
            
            if(isset($_POST['nombreCuenta']))
                $nombreCuenta = $_POST['nombreCuenta'];
            else
            {
                echo $zero;
                exit();
            }
            
            //INSTANCIAS
            $baseCuenta = new Base_Dat_Cuenta();
            $relCuentaCliente = new Base_Rel_CuentaCliente();
            $baseActivityLog = new Base_Dat_ActivityLog();
            
            $baseActivityLog->setPk(0);
            $baseActivityLog->d_activitylog_date = date('Y-m-d H:i:s');
            $baseActivityLog->n_activity_id = 5;
            $baseActivityLog->n_accesslog_id = (int)$_SESSION['idLog'];
            
//            if($isSave)
//            {
                $isCuenta =$baseCuenta->isCuenta($idCliente, $nombreCuenta);
                
                if($isCuenta == '2')
                {
                        $baseActivityLog->c_activitylog_desc = 'Agregar Cuenta: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Nombre Cuenta='.$nombreCuenta.' Nota=La cuenta ya existe en otra cliente, no se guardo';
                        $baseActivityLog->save();                    
                        echo utf8_encode('Otra');
                        exit();
                }

                if($isCuenta == '1')
                {
                    $baseActivityLog->c_activitylog_desc = 'Agregar Cuenta: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Nombre Cuenta='.$nombreCuenta.' Nota=La cuenta ya existe en esta cliente, no se guardo';
                    $baseActivityLog->save();                    
                    echo $zero;
                    exit();
                }
                else
                {

                    $baseCuenta->setPk(0);
                    $baseCuenta->c_cuenta_cuenta = $nombreCuenta;
                    $baseCuenta->n_estatus_id = 3;

                    $isSave = $baseCuenta->save() ? TRUE: FALSE;

                    if($isSave)
                    {
                        $idCuenta = utf8_decode($baseCuenta->getPk());
                        $isSave = FALSE;
                        $relCuentaCliente->setPk(0);
                        $relCuentaCliente->n_cliente_id =  $idCliente;
                        $relCuentaCliente->n_cuenta_id = $idCuenta;
                        $relCuentaCliente->n_estatus_id = 3;
                        $isSave = $relCuentaCliente->save() ? TRUE:FALSE;
                        if($isSave)
                        {
                            $baseActivityLog->c_activitylog_desc = 'Agregar Cuenta: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Identificador Cuenta='.$idCuenta.' Nombre Cuenta='.$nombreCuenta.' Nota=La cuenta se guardo';
                            $baseActivityLog->save();
                            echo $idCuenta;
                        }
                        else
                        {
                            $baseCuenta->setPk($idCuenta);
                            $baseCuenta->n_estatus_id = 5;
                            $baseCuenta->save();
                            $baseActivityLog->c_activitylog_desc = 'Agregar Cuenta: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Identificador Cuenta='.$idCuenta.' Nombre Cuenta='.$nombreCuenta.' Nota=La cuenta no se guardo';
                            $baseActivityLog->save();                         
                            echo $zero;
                        }
                    }
                    else
                    {
                        $baseActivityLog->c_activitylog_desc = 'Agregar Cuenta: Identificador Cliente='.$idCliente.' Nombre Cliente='.$nombreCliente.' Nombre Cuenta='.$nombreCuenta.' Nota=La cuenta no se guardo';
                        $baseActivityLog->save();                         
                        echo $zero;                        
                    }
                }
//            }
//            else
//                echo $zero;
            
?>