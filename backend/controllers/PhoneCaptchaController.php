<?php

namespace backend\controllers;

/**
 * 短信验证码管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class PhoneCaptchaController extends GeneralController
{
    // 模型
    public static $modelName = 'PhoneCaptcha';

    // 模型描述
    public static $modelInfo = '短信验证码';

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
            'phone' => 'input',
            'type' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'update_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ],
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
            'update_time'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'phone',
            'captcha' => 'code',
            'type' => [
                'code',
                'info'
            ],
            'update_time',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ],
        ];
    }
}
