<?php
	/********************************************************************************
	 *																				*
     *	@autor:			César I. G. Pérez <cesar.gonzalez@webmaps.com.mx>			*
	 *	@alias:			CIGP														*
     *	@version:		1.0															*
     *	@created:		28/05/2013													*
     *	@copiright:		Copyright (c) 2010, WebMaps									*
     *	@description:	Controlador ialarm: redirecciona al dispositivo             *
     *																				*
     ********************************************************************************/

class IalarmController extends Controller
{
	public function actionServicio()
	{	
		if(!(isset($_GET['e'])))
			$this->redirect ("Im.php?r=ialarm/error");
		
                //n=-99.148268&l=19.346891&zoom=10
		$this->redirect(array('../Im.php/'.fDispositivo::isMobile().'?e='.$_GET['e']));
	}
        
        public function actionError()
        {
            //ptodo: implementar Pagina de error html
            exit('Solicitud no encontrada');
        }	
}