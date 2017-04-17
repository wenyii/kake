<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@root', dirname(dirname(dirname(__DIR__))));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');

Yii::setAlias('@rsa', dirname(dirname(__DIR__)) . '/mixed/rsa');
Yii::setAlias('@wechat', dirname(dirname(__DIR__)) . '/mixed/wechat');
Yii::setAlias('@alipay', dirname(dirname(__DIR__)) . '/mixed/alipay');
Yii::setAlias('@thrift', dirname(dirname(__DIR__)) . '/mixed/thrift');
Yii::setAlias('@script', dirname(dirname(__DIR__)) . '/mixed/script');

define('TIME', $_SERVER['REQUEST_TIME']);
define('DS', DIRECTORY_SEPARATOR);
define('DOMAIN', 'kakehotels.com');

define('DB_KAKE', 'kake');
define('DB_SERVICE', 'service');

define('MINUTE', 60);
define('HOUR', MINUTE * 60);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);
define('MONTH', DAY * 30);
define('YEAR', MONTH * 12);