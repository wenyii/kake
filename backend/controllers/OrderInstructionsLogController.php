<?php

namespace backend\controllers;

use Yii;

/**
 * 订单操作日志
 *
 * @auth-inherit-except add edit front sort
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
        return array_merge(parent::pageDocument(), [
            'ajax-modal-refuse' => [
                'modal' => true,
                'title_info' => '拒绝说明',
                'button_info' => '确定',
                'action' => '$.handleModalForm($(this), $.instructions);'
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return [
            [
                'text' => '子订单',
                'value' => 'order-sub/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => ['order_number']
            ],
        ];
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
            'order_sub_id' => [
                'elem' => 'input',
                'equal' => true
            ],
            'type' => [
                'value' => parent::SELECT_KEY_ALL
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
            'order_sub_id' => 'code',
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
            'remark' => [
                'max-width' => '400px'
            ],
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
    public function indexCondition($as = null)
    {
        return array_merge(parent::indexCondition(), [
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
            ]
        ]);
    }

    /**
     * 预约操作
     *
     * @auth-pass-all
     */
    public function actionAjaxModalRefuse()
    {
        $this->showForm();
    }
}
