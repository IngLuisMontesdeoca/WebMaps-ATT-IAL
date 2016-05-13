<?php

class DefaultController extends Controller
{
	public function init()
	{
		Yii::app()->themeManager->baseUrl = Yii::app()->baseUrl.'/protected/modules/nmobile/themes';
		Yii::app()->themeManager->basePath = Yii::app()->basePath.'/modules/nmobile/themes';
		Yii::app()->theme = 'ialarm';
	}
	
	public function actionIndex()
	{
		$this->redirect('error');
	}
}