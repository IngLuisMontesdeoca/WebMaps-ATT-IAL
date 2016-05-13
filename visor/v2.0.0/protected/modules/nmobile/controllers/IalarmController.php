<?php

class IalarmController extends Controller
{
	public function init()
	{
		Yii::app()->themeManager->baseUrl = Yii::app()->baseUrl.'/protected/modules/nmobile/themes';
		Yii::app()->themeManager->basePath = Yii::app()->basePath.'/modules/nmobile/themes';
		Yii::app()->theme = 'ialarm';
	}
	
	public function actionIndex()
	{	
		if(!(isset($_GET['e'])))
			$this->redirect ("error");
			
		$dispositivo = fDispositivo::isMobile();
		if($dispositivo != 'nmobile')
			$this->redirect(array('../Im.php/'.$dispositivo.'?e='.$_GET['e']));
			
		$this->render('mapa');
	}
        
        public function actionLocal()
        {
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
                    
                    $search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($hashCoo[1].",".$hashCoo[0]))."&mode=retrieveAddresses&maxresults=1";
                    $xml = simplexml_load_string(file_get_contents($search));
					
                    if(is_null($xml->Response->View[0]))
			$coo[0]["dir"] = 'No hay información de la dirección';
                    else
                    {
			if((count($xml->Response->View[0]) != 2))
                            $coo[0]["dir"] = 'No hay información de la dirección';
			else
                            $coo[0]["dir"] = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
                    }                    
                    echo json_encode($coo);
                }
            }
                 
            if($tipoAlarma == "e")
            {
                $modelAlarma = new DatAlarma();
                $modelAlarma->hash = substr(rawurldecode($_POST['e']), 0, -1);
                
                if($modelAlarma->isHashAlarma())
                {
                    $data = $modelAlarma->locationAlert();

                    $search = "http://reverse.geocoder.api.here.com/6.2/reversegeocode.xml?token=77iOd2HIzY_kTmTRbDN_1w&app_id=AZNy9VssRS0TZLAEz8M1&languages=en-US&prox=".urlencode(trim($data[0]['latitude'].",".$data[0]['longitude']))."&mode=retrieveAddresses&maxresults=1";
                    $xml = simplexml_load_string(file_get_contents($search));
					
                    if(is_null($xml->Response->View[0]))
			$data[0]["dir"] = 'No hay información de la dirección';
                    else
                    {
			if((count($xml->Response->View[0]) != 2))
                            $data[0]["dir"] = 'No hay información de la dirección';
			else
                            $data[0]["dir"] = (string)$xml->Response->View[0]->Result[0]->Location->Address->Label;
                    }                    
                    
                    
                    echo json_encode($data);
                }
                else 
                    echo json_encode(1);
            }
            
            if(($tipoAlarma != 'c') && ($tipoAlarma != 'e'))
                echo json_encode(1);
        }
}