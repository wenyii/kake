<?php

namespace backend\controllers;

/**
 * 登录日志管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class LoginLogController extends GeneralController
{
    // 模型
    public static $modelName = 'LoginLog';

    // 模型描述
    public static $modelInfo = '登录日志';

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'user_id' => [
                'elem' => 'input',
                'equal' => true
            ],
            'username' => [
                'table' => 'user',
                'elem' => 'input'
            ],
            'type' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'add_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ],
            'ip' => 'input',
            'state' => [
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexSorter()
    {
        return [
            'user_id' => [
                'table' => 'user',
                'field' => 'id'
            ],
            'username' => [
                'table' => 'user'
            ],
            'type',
            'add_time',
            'state'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'user_id' => [
                'code',
                'color' => 'default'
            ],
            'username' => [
                'table' => 'user'
            ],
            'type' => [
                'code',
                'color' => 'primary',
                'info'
            ],
            'ip' => 'code',
            'add_time',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition($as = null)
    {
        return array_merge(parent::indexCondition(), [
            'join' => [
                ['table' => 'user']
            ],
            'select' => [
                'user.username',
                'login_log.*'
            ]
        ]);
    }
}
