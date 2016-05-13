<?php

    /********************************************************************
    *                                                                   *
    *    @autor:         Julio Mora <julio.mora@skytel.com.mx>          *
    *    @version:       2.0                                            *
    *    @created        14/01/2009                                     *
    *    @modified       23/01/2009 
    *    @copiright:     Copyright (c) 2008, SkyTel                     *
    *    @link:          Go                                             *
    *    @description:   Clase que Escapa caracteres especiales, etique-*
    *                    tas HTML e instrucciones SQL de una cadena por *
    *                    cuestiones de seguridad.                       *
    *                                                                   *
    *    @modifi         Se añaden más caracteres de escape y se reducen*
    *                    líneas de código.                              *
    *                                                                   * 
    *********************************************************************/
        
  abstract class Escapa
  {//---------------------------------------------------------------------------------->>> Class Escapa

  
  /*
  * Escapa una cadena para la correcta inserción en la Base de Datos mediante
  * funciones de los propios motores de datos desde PHP.
  * @acces    static public
  * @param    cadena->Cadena a escapar
  *           objeto->Instancia de la clase Base. Nulo por defecto
  *           dbType->Tipo de Base de Datos a utilizar. Postgres por defecto.
  * @notes    Es necesaria la instancia de la clase Base puesto que estas funciones
  *           necesitan un identificador de conexión para su correcto funcionamiento.                       
  */
    static public function escaparFuncion( $cadena = '', Base $objeto = null )
    {//<<-------------------------------------------------------------- Method escaparFuncion  
        
        if( is_object($objeto) ) {//<<----------------------------- if is_object
            
            switch( $objeto->dbType ) {//<<----------------------------- switch Tipo Base de Datos
                        
                case 'mysql':
                    $cadenaEscapada = mysql_real_escape_string( $cadena );
                break;
            
                case 'postgres':
                    $cadenaEscapada = pg_escape_string( $cadena );
                break;
                
                default:
                    $cadenaEscapada = NULL;
                break;
                    
            }//<<----------------------------- End switch Tipo Base de Datos
            
            return $cadenaEscapada;
            
        } else {//<<----------------------------- else is_object
            return false;
        }//<<----------------------------- End if is_object
        
    }//<<-------------------------------------------------------------- Method escaparFuncion
    
    
  /*
  * Escapa una cadena para la correcta inserción en la Base de Datos
  * @acces    static public
  * @param    cadena->Cadena a escapar
  *           options->Arreglo de opciones a escapar
  * @method   self::busqEscapa()            
  */    
    static public function escapar( $cadena = '', $options = array() )
    {//<<-------------------------------------------------------------- Method escapar            
                
        //Tags HTML <<Evitar Cross Site Scripting>>    
            $cadena = preg_replace('(\<(/?[^\>]+)\>)','',$cadena);  
        /*  
            CARACTERES DE ESPACIO EN BLANCO <<Evitar errores en BD>>
            Caracter null chr(0)
            Tabulador chr(9)
            Retorno de Carro chr(13) 
            Tabulador Vertical chr(11)
        */ 
            $espaces = array( chr(0), chr(9), chr(13), chr(11) );
            $cadena = str_replace($espaces,'',$cadena);      
        /*
            SIGNOS DE PUNTUACIÓN
            Comillas dobles "
            Comillas simples '
            Diéresis ¨
            Acento ´
            Acento convexo `    
            Acento concavo ^
            Tilde ~
            Apertura Interrogación <<español>> ¿
            Apertura Exclamación <<español>> ¡
        */
            $signs = array('"',"'",'¨','´','`','^','~','¿','¡');
            $cadena = str_replace($signs,'',$cadena);      
        /*
            SIGNOS ARITMÉTICOS Y DE COMPARACIÓN
            Asterisco *    
            Igual =;
            Mas +
            Menos -
            Modulo %        
            Menor que <
            Mayor que >
            Slash /
         */
            $math = array('*','=','+','-','%','<','>','/');
            $cadena = str_replace($math,'',$cadena);  
         /*
            CARACTERES ESPECIALES    
            Sharp #
            Dolares $
            Apertura Paréntesis (
            Cierre Paréntesis )
            Apertura Corchetes [
            Cierre Corchetes ]
            Contra Slash \
            Guión bajo _
            Pipe |
            Andperssand &
          */      
            $char = array('#','$','(',')','[',']','\\','_','|','&');
            $cadena = str_replace($char,'',$cadena); 
                
             //Buscamos las opciones específicas de escape en el arreglo options             
             if( !empty($options) ) {               
                $cadena = self::busqEscapa( $cadena, $options );     
             }
                    
        return $cadena;
    }//<<-------------------------------------------------------------- End Method escapar
    
          
  /*
  * Busca las opciones a escapar en un arreglo y ejecuta el escape correspondiente
  * @acces    static public
  * @param    arreglo->Arreglo que contiene las opciones a escapar            
  */
    static public function busqEscapa( $cadena, $arreglo ) 
    {//<<-------------------------------------------------------------- Method busqArreglo        
        
        if( is_array( $arreglo ) ) {//<<----------------------------- if is_array
           
           //Variable que define si se escaparán instrucciónes SQL comúnes entre sentencias.
           $comunes = false;
           
           foreach( $arreglo as $indice=>$valor ) {//<<----------------------------- recorrido arreglo
                
                switch( $valor ) {//<<----------------------------- switc valores
                    
                    case 'puntosComas':
                    case '.,;':
                        $dotComme = array(chr(46),chr(44),chr(59));
                        $cadena = str_replace($dotComme,'',$cadena);
                    break;
                    
                    case 'exclamaPregunta':
                    case '!?':
                    case '?!':
                        $exQuest = array(chr(33),chr(63));
                        $cadena = str_replace($exQuest,'',$cadena);
                    break;
                    
                    case 'acentos':
                    case 'acents':
                        $cadena= preg_replace('/Á|Â|Ã|Ä|Å/','A',$cadena);
                        $cadena= preg_replace('/à|á|â|ã/','a',$cadena);
                        $cadena= preg_replace('/È|É|Ê|Ë/','E',$cadena);
                        $cadena= preg_replace('/è|é|ê|ë/','e',$cadena);
                        $cadena= preg_replace('/Ì|Í|Î|Ï/','I',$cadena);
                        $cadena= preg_replace('/ì|í|î|ï/','i',$cadena);
                        $cadena= preg_replace('/Ò|Ó|Ô|Õ|Ö/','O',$cadena);
                        $cadena= preg_replace('/ò|ó|ô|õ|ö/','o',$cadena);
                        $cadena= preg_replace('/Ù|Ú|Û|Ü/','U',$cadena);
                        $cadena= preg_replace('/ù|ú|û|ü/','u',$cadena);
                    break;
                    
                    case 'enie':
                    case 'ñ':
                    case 'Ñ':
                        $cadena = str_replace('Ñ','NI',$cadena);
                        $cadena = str_replace('ñ','ni',$cadena);
                    break;
                    
                    case 'arroba':
                    case '@':
                        $cadena = str_replace('@','',$cadena);
                    break;
                    
                    case 'select':
                      //SELECT
                        $cadena = preg_replace('/[S|s][E|e][L|l][E|e][C|c][T|t]/','',$cadena);
                      //FROM
                        $cadena = preg_replace('/[F|f][R|r][O|o][M|m]/','',$cadena);
                      //ORDER            
                        $cadena = preg_replace('/[O|o][R|r][D|d][E|e][R|r]/','',$cadena);
                      //BY    
                        $cadena = preg_replace('/[B|b][Y|y]/','',$cadena);
                      //ASC
                        $cadena = preg_replace('/[A|a][S|s][C|c]/','',$cadena);
                      //DESC
                        $cadena = preg_replace('/[D|d][E|e][S|s][C|c]/','',$cadena);    
                      //LIMIT    
                        $cadena = preg_replace('/[L|l][I|i][M|m][I|i][T|t]/','',$cadena);
                      //OFFSET    
                        $cadena = preg_replace('/[O|o][F|f][F|f][S|s][E|e][T|t]/','',$cadena);
                      //UNION    
                        $cadena = preg_replace('/[U|u][N|n][I|i][O|o][N|n]/','',$cadena);
                      //JOIN    
                        $cadena = preg_replace('/[J|j][O|o][I|i][N|n]/','',$cadena);
                      //WHERE  
                        $comunes = true;
                    break;
                    
                    case 'andOr':
                      //AND    
                        $cadena = preg_replace('/[A|a][N|n][D|d]/','',$cadena);
                      //OR            
                        $cadena = preg_replace('/[O|o][R|r]/','',$cadena);  
                    break;
                    
                    case 'insert':
                      //INSERT    
                        $cadena = preg_replace('/[I|i][N|n][S|s][E|e][R|r][T|t]/','',$cadena);
                      //INTO    
                        $cadena = preg_replace('/[I|i][N|n][T|t][O|o]/','',$cadena);
                      //VALUES    
                        $cadena = preg_replace('/[V|v][A|a][L|l][U|u][E|e][S|s]/','',$cadena);
                    break;
                    
                    case 'update':
                      //UPDATE    
                        $cadena = preg_replace('/[U|u][P|p][D|d][A|a][T|t][E|e]/','',$cadena);
                      //SET    
                        $cadena = preg_replace('/[S|s][E|e][T|t]/','',$cadena);
                      //WHERE  
                        $comunes = true;
                    break;
                    
                    case 'delete':
                      //DELETE    
                        $cadena = preg_replace('/[D|d][E|e][L|l][E|e][T|t][E|e]/','',$cadena);
                      //FROM
                        $cadena = preg_replace('/[F|f][R|r][O|o][M|m]/','',$cadena);
                      //WHERE  
                        $comunes = true;
                    break;
                    
                    case 'admin':
                      //DROP    
                        $cadena = preg_replace('/[D|d][R|r][O|o][P|p]/','',$cadena);
                      //TRUNCATE    
                        $cadena = preg_replace('/[T|t][R|r][U|u][N|n][C|c][A|a][T|t][E|e]/','',$cadena);
                      //CREATE    
                        $cadena = preg_replace('/[C|c][R|r][E|e][A|a][T|t][E|e]/','',$cadena);
                      //ALTER    
                        $cadena = preg_replace('/[A|a][L|l][T|t][E|e][R|r]/','',$cadena);
                      //TABLE    
                        $cadena = preg_replace('/[T|t][A|a][B|b][L|l][E|e]/','',$cadena);
                    break;
                    
                    default:
                        continue;
                    break;
                    
                }//<<----------------------------- End switc valores
                
                if($comunes==true) {//<<----------------------------- if Instrucciones Comunes                
                    
                    //WHERE    
                        $cadena = preg_replace('/[W|h][H|h][E|e][R|r][E|e]/','',$cadena);
                    //LIKE    
                        $cadena = preg_replace('/[L|l][I|i][K|k][E|e]/','',$cadena);                    
                    //BETWEEN    
                        $cadena = preg_replace('/[B|b][E|e][T|t][W|w][E|e][E|e][N|n]/','',$cadena);
                        
                    $comunes = false;    
                }//<<----------------------------- End if Instrucciones Comunes
                
                 
           }//<<----------------------------- End recorrido arreglo 
           
           return $cadena;
           
        } else {//<<----------------------------- else is_array
        
            return false;
            
        }//<<----------------------------- End if is_array
        
    }//<<-------------------------------------------------------------- End Method busqArreglo

 
 
  /*
  * Quita los acentos de una cadena y la convierte en MAYÚSCULAS
  * @acces    static public
  * @param    cadena->Cadena a procesar            
  */
    static public function toUpperNoAccents( $cadena='' )
    {//<<-------------------------------------------------------------- Method ToUpperNoAccents
        
            //Tabla de traducciones de caracteres especiales
            $trans = get_html_translation_table(HTML_ENTITIES);
            
            //Creación de arreglo de caracteres acentuados y del arreglo de caracteres no acentuados
            foreach( $trans as $literal=>$entity ) {//<<----------------------------- foreach
            
               //Descarta caracteres como comillas, fracciones, etc.
                   if( ( ord($literal) ) >=192 ) {//<<----------------------------- if ASCII
                    
                     //Arreglo de caracteres no acentuados
                         $replace[]=substr($entity,1,1); 
                     //Arreglo de caracteres acentuados
                         $search[]=$literal;
                         
                   }//<<----------------------------- End if ASCII
                 
            }//<<----------------------------- End foreach
             
            //Remplaza los caracteres acentuados por los caracteres no acentuados correspondientes
            $cadena = str_replace($search, $replace, $cadena);
            
            //Convierte la cadena en MAYÚSCULAS
            $cadena = strtoupper($cadena);
            
            return $cadena;
            
    }//<<-------------------------------------------------------------- End Method ToUpperNoAccents
           
  
  }//---------------------------------------------------------------------------------->>> End Class Escapa

?>