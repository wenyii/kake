<?php

namespace backend\controllers;

/**
 * 项目环境管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class EnvironmentController extends GeneralController
{
    /**
     * 项目环境预览
     *
     * @auth-pass-all
     */
    public function actionIndex()
    {
        $env = [
            [
                'name' => '协议版本',
                'value' => $_SERVER['SERVER_PROTOCOL']
            ],
            [
                'name' => '网关版本',
                'value' => $_SERVER['GATEWAY_INTERFACE']
            ],
            [
                'name' => 'Web服务器',
                'value' => $_SERVER['SERVER_SOFTWARE']
            ],
            [
                'name' => '服务器IP',
                'value' => $_SERVER['SERVER_ADDR']
            ],
            [
                'name' => '服务器端口',
                'value' => $_SERVER['SERVER_PORT']
            ],
            [
                'name' => 'PHP版本',
                'value' => PHP_VERSION
            ],
            [
                'name' => 'Zend版本',
                'value' => Zend_Version()
            ],
            [
                'name' => 'MySQL版本',
                'value' => `mysql --version`
            ],
            [
                'name' => '服务器信息',
                'value' => php_uname()
            ],
            [
                'name' => '用户信息',
                'value' => $_SERVER['HTTP_USER_AGENT']
            ],
        ];

        return $this->display('index', [
            'env' => $env
        ]);
    }
}
