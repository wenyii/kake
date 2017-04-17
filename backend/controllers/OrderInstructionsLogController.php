<?php

namespace backend\controllers;

use Yii;
use common\models\Main;

/**
 * 订单操作日志
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class OrderInstructionsLogController extends GeneralController
{
    // 模型
    public static $modelName = 'OrderInstructionsLog';

    // 模型描述
    public static $modelInfo = '订单操作记录';

    /**
     * @inheritDoc
     */
    public function pageDocument()
    {
        return [
            'ajax-modal-refuse' => [
                'modal' => true,
                'title_info' => '拒绝说明',
                'button_info' => '确定',
                'action' => '$.handleModalForm($(this), $.instructions);'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'username' => [
                'table' => 'user',
                'title' => '操作人',
                'elem' => 'input'
            ],
            'order_number' => [
                'table' => 'order',
                'elem' => 'input',
                'equal' => true
            ],
            'type' => [
                'value' => 'all'
            ],
            'remark' => 'input'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'id' => 'code',
            'username' => [
                'table' => 'user',
                'title' => '操作人'
            ],
            'order_number' => [
                'code',
                'table' => 'order'
            ],
            'type' => [
                'code',
                'info',
                'color' => [
                    0 => 'success',
                    1 => 'warning',
                    2 => 'success',
                    3 => 'warning',
                ]
            ],
            'remark',
            'add_time'
        ];
    }

    /**
     * 预约操作表单
     */
    public static function ajaxModalRefuseAssist()
    {
        return [
            'order_sub_id' => [
                'hidden' => true,
                'value' => Yii::$app->request->get('order_sub_id')
            ],
            'remark' => [
                'elem' => 'textarea',
                'placeholder' => '不少于10个字的备注',
                'label' => 8
            ],
            'type' => [
                'hidden' => true,
                'value' => Yii::$app->request->get('type')
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'join' => [
                [
                    'table' => 'user',
                    'left_on_field' => 'admin_user_id',
                ],
                ['table' => 'order_sub'],
                [
                    'left_table' => 'order_sub',
                    'table' => 'order'
                ]
            ],
            'select' => [
                'user.username',
                'order_instructions_log.*',
                'order.order_number'
            ],
            'order' => 'order_instructions_log.id DESC'
        ];
    }

    /**
     * 预约操作
     *
     * @auth-pass-all
     */
    public function actionAjaxModalRefuse()
    {
        $this->showForm('ajax-modal-refuse');
    }
}
