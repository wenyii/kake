<?php
return [
    'id' => 'backend',
    'defaultRoute' => 'environment',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'params' => array_merge(
        require(__DIR__ . '/../../common/config/params-local.php'),
        require(__DIR__ . '/../../common/config/params.php'),
        require(__DIR__ . '/params-local.php'),
        require(__DIR__ . '/params.php')
    ),
    'language'=>'zh-CN',
    'components' => [
        'session' => [
            'name' => 'KK_BACKEND_SESS',
            'cookieParams' => [
                'domain' => DOMAIN,
                'lifetime' => 86400,
                'httpOnly' => true,
                'path' => '/',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'general/error',
        ],
    ],
];
