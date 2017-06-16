<?php

namespace backend\controllers;

use common\models\Main;
use Yii;
use yii\data\Pagination;
use yii\helpers\Html;

/**
 * 活动抽奖码管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class ActivityLotteryCodeController extends GeneralController
{
    // 模型
    public static $modelName = 'ActivityLotteryCode';

    // 模型描述
    public static $modelInfo = '活动抽奖码';

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'nickname' => 'input',
            'real_name' => 'input',
            'phone' => 'input',
            'company' => [
                'value' => 'all'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'nickname',
            'real_name',
            'phone',
            'company' => 'info',
            'code' => [
                'empty',
                'code'
            ],
            'add_time',
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
    public function indexCondition()
    {
        return [
            'where' => [
                ['state' => 1],
                ['subscribe' => 1]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function editAssist($action = null)
    {
        return [
            'real_name',
            'phone',
            'state' => [
                'elem' => 'select',
                'value' => 'all'
            ]
        ];
    }
}
