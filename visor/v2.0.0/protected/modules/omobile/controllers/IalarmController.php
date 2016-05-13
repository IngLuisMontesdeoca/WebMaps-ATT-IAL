<?php
//pendiente:requerido: cachar cuando las coordenadas no son validas en las url...
	/********************************************************************************
	 *																				*
     *	@autor:			CÃ©sar I. G. PÃ©rez <cesar.gonzalez@webmaps.com.mx>			*
	 *	@alias:			CIGP														*
     *	@version:		1.0															*
     *	@created:		28/05/2013													*
     *	@copiright:		Copyright (c) 2010, WebMaps									*
     *	@description:	Controlador ialarm: gestiona los metodos de ialarm			*
     *																				*
     ********************************************************************************/
	 
	 /*
	  * #zonadeweb
	  * #zonadefunciones
	  * #zonadedatos
	  */

class IalarmController extends Controller
{
	
	/*******************************************************zona de acceso web #zonadeweb********************************************************/

	public function actionIndex()
	{	
			
		$dispositivo = fDispositivo::isMobile();
		if($dispositivo != 'omobile')
			$this->redirect(array('../Im.php/'.$dispositivo.'?e='.$_GET['e']));
			
		if(!isset($_REQUEST['id']))
			$id=0;
		else
			$id=$_REQUEST['id'];		
			
		$button = isset($_REQUEST['button']);
		
		$hide='none';
		if(isset($_REQUEST['map']))
			$hide='block';
			
		if(!isset($_REQUEST['address']))
			$address = isset($_REQUEST['address']);
		else
			$address = $_REQUEST['address'];
		
                if(isset($_GET['zoom']))
                    $zoom = $_GET['zoom'];
                else
                    $zoom = 15;
                
		if(!(isset($_GET['e'])))
                {
                    if(isset($_GET['l'],$_GET['n']))
                    {
                        $coordenadas = Array();
                        $coordenadas[0]['latitude']=$_GET['l'];
                        $coordenadas[0]['longitude']=$_GET['n'];      
                    }
                    else
			$this->redirect ("error");
                }
                else
                    $coordenadas = $this->local($_GET['e']);
                
                if($coordenadas == 0)
                    $this->redirect ("error");
                else
                    $this->oMobile((float)$coordenadas[0]['longitude'],(float)$coordenadas[0]['latitude'],$zoom,$id,$button,$hide,$address,'Mobile 3.1.0');

	}

	/*******************************************************zona de funciones #zonadefunciones********************************************************/
	
	protected function oMobile($n,$l,$zoom,$id,$button,$hide,$address,$ver)
	{
		//descomentar para produccion
			
		if($zoom<0)
			$zoom=0;
				
		if($id==1)
		{
			
			$this->imagenMapa($l,$n,$zoom);
	
		}
		else
		{

			$vistaFinal = $this->generarVistaMapa($l,$n,$zoom,$address,$button,$hide,$ver);

		}
		
		if(!isset($vistaFinal[2]))
			exit();
			
		if(!isset($vistaFinal[1]))
			exit();
			
		if(!isset($vistaFinal[0]))
			exit();		
		
		if(!isset($vistaFinal[3]))
			exit();					
			
		if($vistaFinal[0]=='')
			$this->renderPartial('mapa',array('imgmap'=>$vistaFinal[1],'vista'=>$vistaFinal[0],'titulo'=>$vistaFinal[2],'leyenda'=>$vistaFinal[3]));
		else
			$this->renderPartial('mapa',array('imgmap'=>$vistaFinal[1],'vista'=>$vistaFinal[0],'titulo'=>$vistaFinal[2],'leyenda'=>$vistaFinal[3]));

	}

	private function imagenMapa($l,$n,$zoom)
	{
	
		$url = 'http://maps.googleapis.com/maps/api/staticmap?center='.urlencode(trim($l.','.$n)).'&zoom='.urlencode(trim($zoom)).'&size=200x200&maptype=roadmap&markers=color:orange|'.urlencode(trim($l.','.$n)).'&sensor=false';
		$x = array_change_key_case(get_headers($url, 1),CASE_LOWER);
		header('Content-Type: '.$x['content-type']);
		header('Content-Length: '.$x['content-length']);
		echo file_get_contents($url);		

	
	}
		
	private function generarVistaMapa($l,$n,$zoom,$address,$button,$hide,$ver)
	{

		if(!$address){
			//pendiente: hacer un helper para construir la url de busqueda por coordenadas
			$search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($l.",".$n))."&mode=retrieveAddresses&maxresults=1";
			$xml = simplexml_load_string(file_get_contents($search));

			if(is_null($xml->Response->View[0]))
				$address = 'No hay información de la dirección';
			else
			{
					if((count($xml->Response->View[0]) != 2))
						exit('Datos no válidos');
					else
						$address = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
				
			}
		}
		
		$titulo='';
		$vista='';
		$leyenda='';
		if(!($button))
		{
			$datoVariables=array($l,$n,$zoom,iconv ( "UTF-8", "ISO-8859-1", $address));
			$etiquetaVariables = array('{lt}','{ln}','{z}','{address}');
				
			$htmlVista = file_get_contents('protected/modules/omobile/views/ialarm/mapa/mapa.html');
			$vista = str_replace($etiquetaVariables, $datoVariables, $htmlVista);
			$titulo = '<div id="myDivTitulo" style=" display: '.$hide.';">
			<div id="myDivTitulo" style="margin:0 auto 0 auto; width:200px;"><h2>Ubicación</h2></div>
			</div>';
			$leyenda = '<div id="myDivLeyenda" style=" display: '.$hide.';">
			<div>'.$ver.'</div>
			</div>';				
		}

		$imgmap = '
		<div id="myDivMap" style="display: '.!$hide.'">
			<div id="myDivLoad" style="display: '.$hide.'">
				<img style="display:block;margin:0 auto 0 auto;" SRC="../protected/modules/omobile/themes/ialarm/images/iAlarmMobile/loading.gif">
			</div>
			<div id="myDiv" style="display: '.$hide.'">
				<img onload = "preloaderMap();" style="display:block;margin:0 auto 0 auto;" SRC="omobile?l='.$l.'&n='.$n.'&id=1&zoom='.$zoom.'&address='.$address.'">
			</div>
		<div></div>
		</div>';	
	
		return array($vista,$imgmap,$titulo,$leyenda);
	}	

	/*******************************************************zona para base de datos #zonadedatos************************************************/
        protected function local($idAlarma)
        {
            
            $tipoAlarma = substr($idAlarma, -1);
            
            if($tipoAlarma == "c")
            {
                $coo = Array();
                $hashCoo = explode("|", AES::aes256Base64($idAlarma));
                if(count($hashCoo)<2)
                    return 1;
                else
                {
                    $coo[0]["longitude"]=$hashCoo[0];
                    $coo[0]["latitude"]=$hashCoo[1];
                    return $coo;
                }
            }
                 
            if($tipoAlarma == "e")
            {
                $modelAlarma = new DatAlarma();
                $modelAlarma->hash = substr(rawurldecode($idAlarma), 0, -1);
                
                if($modelAlarma->isHashAlarma())
                    return $modelAlarma->locationAlert();
                else 
                    return 1;
            }
            
            if(($tipoAlarma != 'c') && ($tipoAlarma != 'e'))
                return 1;
        }
	
}