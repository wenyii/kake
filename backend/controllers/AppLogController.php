<?php

namespace backend\controllers;

use common\components\Helper;
use yii\helpers\Html;

/**
 * 项目运行日志管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class AppLogController extends GeneralController
{
    // 模型
    public static $modelName = 'AppLog';

    // 模型描述
    public static $modelInfo = '项目运行日志';

    /**
     * @var array Hook
     */
    public static $hookDateSection = ['log_time' => 'stamp'];

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
            'level',
            'log_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ],
            'message' => 'input'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'level' => [
                'min-width' => '70px',
                'info',
                'code',
            ],
            'log_time' => [
                'min-width' => '170px',
                'html'
            ],
            'prefix',
            'message' => 'html',
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'order' => 'log_time DESC'
        ];
    }

    /**
     * 将信息处理成带格式显示
     *
     * @access private
     *
     * @param string $message
     *
     * @return string
     */
    private function handleMessageForView($message)
    {
        $message = preg_replace('/#(\d+) /', '[#$1]', $message);
        $message = Html::encode($message);
        $message = preg_replace('/\[#(\d+)\]/', '<br><b>#$1 --> </b>', $message);

        $message = str_replace('Stack trace:', '<br><br><b>Stack trace:</b><br>', $message);
        $message = str_replace('Next exception', '<br><br><b>Next</b>exception', $message);
        $message = str_replace('exception &', '<br><br>exception &', $message);

        $message = preg_replace('/^(\<br\>\<br\>)/', '', $message);
        $message = preg_replace('/\\n/', '<br>', $message);

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if (!empty($record['prefix'])) {
            $record['prefix'] = Helper::cutString($record['prefix'], [
                '[^1',
                ']^0'
            ]);
        }

        if (!empty($record['message'])) {
            $record['message'] = $this->handleMessageForView($record['message']);
        }

        return parent::sufHandleField($record, $action);
    }
}
