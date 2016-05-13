<?php

class IalarmController extends Controller
{
	public function init()
	{
		Yii::app()->themeManager->baseUrl = Yii::app()->baseUrl.'/protected/modules/web/themes';
		Yii::app()->themeManager->basePath = Yii::app()->basePath.'/modules/web/themes';
		Yii::app()->theme = 'ialarm';
	}
	
	public function actionIndex()
	{
		if(!(isset($_GET['e'])))
			$this->redirect ("error");
			
		$dispositivo = fDispositivo::isMobile();
		if($dispositivo != 'web')
			$this->redirect(array('../Im.php/'.$dispositivo.'?e='.$_GET['e']));
			
		$this->render('mapa');
	}
	
	public function actionBarFrame()
	{
		$barZoomSlide = file_get_contents(Yii::app()->basePath.'/modules/web/themes/ialarm/views/layouts/html/bar.html');
		echo $barZoomSlide;
	}
	
	public function actionTooltip()
	{

		if($_POST['tipo'] == 'HANDLER') $index = 'indexHandler';
		if($_POST['tipo'] == 'LOCATION') $index = 'indexLocation';
		
		$crash = '';
		$toolCondition = 1;
		if($_POST['tipo'] != 'ALERT')
		{
			
			$crash = '<a title="Borrar punto del mapa" onclick="pointMarkerBubbleRemove('.$index.',\''.$_POST['tipo'].'\');"><div class="cssIconFoot" style="width:25px; height:20px; float:right; 
					background-image:url(../protected/modules/web/themes/ialarm/images/iAlarmDesktop/tooltips/iconCrash.png);
					background-repeat:no-repeat;"></div></a>';
			$toolCondition = 2;
		}
		
		$datoVariables=array(iconv ( "UTF-8", "ISO-8859-1", $_POST['direccion']), $crash, $_POST['tipo']);
		$etiquetaVariables = array('{direccion}','{crash}','{nameMarker}');
	
		$toolTip = file_get_contents(Yii::app()->basePath.'/modules/web/themes/ialarm/views/layouts/html/tooltips/tooltip.html');
		echo $vistaToolTip = str_replace($etiquetaVariables, $datoVariables, $toolTip);
		
	}
	
        public function actionLocal()
        {
            //iconv("UTF-8", "ISO-8859-1", "RecuperaciÃ³n de contraseÃ±a");
            //$arrInfoPoint[$indexArrInfoPoint] = array_map("utf8_encode",convertirDatosOBD($arrPoint[0],$arrInfoProtocol,$arrInfoOBDCodes,$arrInfoUnidad));

            // error 0 no existe registro
            // error 1 no es valido el hashAlarma
            // error 2 no llego el parametro hashalarma
            
            $tipoAlarma = substr($_POST['e'], -1);
            
            if($tipoAlarma == "c")
            {
                $coo = Array();
                $hashCoo = explode("|", AES::aes256Base64($_POST['e']));
                if(count($hashCoo)<2)
                    echo json_encode(1);
                else
                {
                    $coo[0]["longitude"]=(float)$hashCoo[0];
                    $coo[0]["latitude"]=(float)$hashCoo[1];
                    $data = Array();
                    $data['coo']=$coo;
                    $data['his']=0;
					
					$search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($data["coo"][0]["latitude"].",".$data["coo"][0]["longitude"]))."&mode=retrieveAddresses&maxresults=1";
					$xml = simplexml_load_string(file_get_contents($search));
					
					if(is_null($xml->Response->View[0]))
						$data["coo"][0]["dir"] = 'No hay información de la dirección';
					else
					{
						if((count($xml->Response->View[0]) != 2))
							$data["coo"][0]["dir"] = 'No hay información de la dirección';
						else
							$data["coo"][0]["dir"] = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
					}
					
                    echo json_encode($data);
                }
            }
                 
            if($tipoAlarma == "e")
            {
                $modelAlarma = new DatAlarma();
                $modelAlarma->hash = substr(rawurldecode($_POST['e']), 0, -1);
                
                if($modelAlarma->isHashAlarma())
                {
                    $data = Array();
                    $data['coo']=$modelAlarma->locationAlert();
                    if($data['coo'] == 0)
                    {
                        echo json_encode(0);
                        exit();
                    }
                    $modelHistorico = new DatHistorico();
                    $modelHistorico->hash = $modelAlarma->hash;
                    $data["his"]=$modelHistorico->locationHistory();
					
					$search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($data["coo"][0]["latitude"].",".$data["coo"][0]["longitude"]))."&mode=retrieveAddresses&maxresults=1";
					$xml = simplexml_load_string(file_get_contents($search));
					
					if(is_null($xml->Response->View[0]))
						$data["coo"][0]["dir"] = 'No hay información de la dirección';
					else
					{
						if((count($xml->Response->View[0]) != 2))
							$data["coo"][0]["dir"] = 'No hay información de la dirección';
						else
							$data["coo"][0]["dir"] = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
					}
					
					for($i=0, $size=count($data["his"]); $i<$size; $i++)
					{
						$search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($data["his"][$i]['latitude'].",".$data["his"][$i]['longitude']))."&mode=retrieveAddresses&maxresults=1";
						$xml = simplexml_load_string(file_get_contents($search));

						if(is_null($xml->Response->View[0]))
							$data["his"][$i]["dir"] = 'No hay información de la dirección';
						else
						{
								if((count($xml->Response->View[0]) != 2))
									$data["his"][$i]["dir"] = 'No hay información de la dirección';
								else
									$data["his"][$i]["dir"] = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
							
						}
					}
					

                    echo json_encode($data);
                }
                else 
                    echo json_encode(1);
            }
            
            if(($tipoAlarma != 'c') && ($tipoAlarma != 'e'))
                echo json_encode(1);
        }
		
		public function actionImgSVG()
		{
			if(isset($_GET['n']))
			{
				header('Content-type: image/svg+xml');
				echo '<?xml version="1.0" standalone="no"?>
						<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" 
						"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
				<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
  width="27px" height="27px" viewBox="0 0 27 27" enable-background="new 0 0 27 27" xml:space="preserve"> 
 <g transform="translate(0, 0)">
  <path fill="#C12C2E" stroke="#FFFFFF" stroke-miterlimit="10" d="M13.5,0.56c-5.845,0-10.584,4.739-10.584,10.584
 c0,5.936,5.146,9.851,10.584,15.295c5.438-5.444,10.584-9.359,10.584-15.295C24.084,5.299,19.345,0.56,13.5,0.56z"/>
       <text x="13" y="15" font-size="7pt" font-family="arial" font-weight="bold" text-anchor="middle" fill="#FFF" textContent="'.$_GET['n'].'">'.$_GET['n'].'</text>
 </g>
</svg>';
			}
			
		}		
        
		public function actionGDir()
		{
		
			$data = Array();
			$search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($_POST["latitude"].",".$_POST["longitude"]))."&mode=retrieveAddresses&maxresults=1";
			$xml = simplexml_load_string(file_get_contents($search));
					
			if(is_null($xml->Response->View[0]))
				$data["dir"] = 'No hay información de la dirección';
			else
			{
				if((count($xml->Response->View[0]) != 2))
					$data["dir"] = 'No hay información de la dirección';
				else
					$data["dir"] = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
			}
				
			echo json_encode($data);		
		}
}