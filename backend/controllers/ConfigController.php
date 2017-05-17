<?php

namespace backend\controllers;

use common\models\Main;
use Yii;
use yii\data\Pagination;
use yii\helpers\Html;

/**
 * 配置管理
 *
 * @auth-inherit-except front
 */
class ConfigController extends GeneralController
{
    // 模型
    public static $modelName = 'Config';

    // 模型描述
    public static $modelInfo = '配置';

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增配置',
                'value' => 'config/add',
                'icon' => 'plus'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'app' => [
                'value' => 'all'
            ],
            'key' => 'input',
            'value' => 'input',
            'remark' => 'input',
            'state' => [
                'value' => 'all'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'app' => [
                'info',
                'code'
            ],
            'key' => 'code',
            'value',
            'remark',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function editAssist($action = null)
    {
        return [
            'app' => [
                'elem' => 'select',
                'value' => 0
            ],
            'key',
            'value',
            'remark' => [
                'elem' => 'textarea',
                'placeholder' => '用一句简单语句描述该配置的作用(重要)'
            ],
            'state' => [
                'elem' => 'select',
                'value' => 1
            ]
        ];
    }

    /**
     * 项目预配置列表
     */
    public function actionFile()
    {
        $model = new Main(self::$modelName);
        $handler = function ($config, $app) use ($model) {

            $_config = [];

            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    continue;
                }

                if (is_bool($value)) {
                    $_config[$key] = $value ? 1 : 0;
                } else {
                    $_config[$key] = is_string($value) ? Html::encode($value) : $value;
                }
            }

            array_walk($_config, function (&$value) use ($app, $model) {
                $value = [
                    'app' => $app,
                    'app_info' => $model->_app[$app],
                    'value' => $value
                ];
            });

            return $_config;
        };
        $config = Yii::$app->params;
        $config = $handler($config, 0);

        $frontendConfig = require Yii::getAlias('@frontend/config/params.php');
        $frontendConfig = $handler($frontendConfig, 1);

        $config = array_merge($config, $frontendConfig);

        // 分页
        $pagination = new Pagination(['totalCount' => count($config)]);
        $pagination->setPageSize(Yii::$app->params['pagenum']);

        $config = array_slice($config, $pagination->offset, $pagination->limit);

        return $this->display('file', [
            'config' => $config,
            'page' => $pagination
        ]);
    }
}
