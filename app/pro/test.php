<?php

require_once('lib/yii/framework/yii.php');
$a = Yii::createWebApplication('test3/protected/config/main.php');
$a->setViewPath('/var/www/dev/nxt/cen/v1.0.0/vistas');
$a->setControllerPath('/var/www/dev/nxt/cen/v1.0.0/modelo/controllers');
$a->runController('site/contact');
?>
