<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'i-Alarm',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.helpers.ip.*',
		'application.components.helpers.mobile.*',
                'application.components.helpers.aes.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'web',
		'nmobile',
		'omobile',
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'yonosoyyo',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
                        'showScriptName'=>false,
			'rules'=>array(
                                'web'=>'web/ialarm/index',
                                'nmobile'=>'nmobile/ialarm/index',
                                'omobile'=>'omobile/ialarm/index',
                                'error'=>'ialarm/error',
                                'wiLocal'=>'web/ialarm/local',
                                'nmiLocal'=>'nmobile/ialarm/local',
                                'fDispositivo'=>'ialarm/servicio',
                                'barFrame'=>'web/ialarm/barFrame',
                                'toolTip'=>'web/ialarm/tooltip',
				'GDir'=>'web/ialarm/GDir',
                                'imgSVG'=>'web/ialarm/imgSVG',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=192.168.4.123;dbname=NXT_IALARM',
			'emulatePrepare' => true,
			'username' => 'usrAppIlrVsr',
			'password' => 'U5rApp1c4t10nV150r',
			'charset' => 'latin1',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
                'semillaAlarma'=>"\$N3xt31-/1A14rm\$",
                'kA256B64'=>"06e7c76d560404c662bc9261096b6111"
	),
);