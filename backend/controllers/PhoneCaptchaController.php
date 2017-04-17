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
                'value' => 'all'
            ],
            'update_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ],
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
