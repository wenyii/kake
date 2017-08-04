<?php

namespace backend\controllers;

/**
 * 抽奖码管理
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
                'value' => parent::SELECT_KEY_ALL
            ],
            'subscribe' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'add_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ]
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
            'subscribe' => [
                'code',
                'color' => 'auto',
                'info'
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
    public static function indexSorter()
    {
        return [
            'add_time'
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition($as = null)
    {
        return array_merge(parent::indexCondition(), [
            'where' => [
                ['state' => 1]
            ]
        ]);
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
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }
}
