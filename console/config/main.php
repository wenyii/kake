<?php
return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'params' => array_merge(
        require(__DIR__ . '/../../common/config/params-local.php'),
        require(__DIR__ . '/../../common/config/params.php'),
        require(__DIR__ . '/params-local.php'),
        require(__DIR__ . '/params.php')
    ),
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ],
                ],
            ],
        ],
        /**
         * create user maiqi_kake_write identified by 'maiqi@KAKE2016';
         * create user maiqi_kake_read identified by 'maiqi@KAKE2016';
         * grant all on maiqi_kake.* to maiqi_kake_write;
         * grant SELECT on maiqi_kake.* to maiqi_kake_read;
         */
        'kake' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            // Master
            'masterConfig' => [
                'username' => 'maiqi_kake_write',
                'password' => 'maiqi@KAKE2016',
            ],
            'masters' => [
                'a' => ['dsn' => 'mysql:host=maiqi-kake-external.mysql.rds.aliyuncs.com;dbname=maiqi_kake'],
            ],
            // Slave
            'slaveConfig' => [
                'username' => 'maiqi_kake_read',
                'password' => 'maiqi@KAKE2016',
            ],
            'slaves' => [
                'a' => ['dsn' => 'mysql:host=maiqi-kake-external.mysql.rds.aliyuncs.com;dbname=maiqi_kake'],
            ],
        ],
        /**
         * create user maiqi_service_write identified by 'maiqi@SERVICE2016';
         * create user maiqi_service_read identified by 'maiqi@SERVICE2016';
         * grant all on maiqi_service.* to maiqi_service_write;
         * grant SELECT on maiqi_service.* to maiqi_service_read;
         */
        'service' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            // Master
            'masterConfig' => [
                'username' => 'maiqi_service_w',
                'password' => 'maiqi@SERVICE2016',
            ],
            'masters' => [
                'a' => ['dsn' => 'mysql:host=maiqi-kake-external.mysql.rds.aliyuncs.com;dbname=maiqi_service'],
            ],
            // Slave
            'slaveConfig' => [
                'username' => 'maiqi_service_r',
                'password' => 'maiqi@SERVICE2016',
            ],
            'slaves' => [
                'a' => ['dsn' => 'mysql:host=maiqi-kake-external.mysql.rds.aliyuncs.com;dbname=maiqi_service'],
            ],
        ],
    ],
];