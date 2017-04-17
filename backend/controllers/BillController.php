<?php

namespace backend\controllers;

/**
 * 发票管理
 *
 * @auth-inherit-except front
 */
class BillController extends GeneralController
{
    // 模型
    public static $modelName = 'Bill';

    // 模型描述
    public static $modelInfo = '发票';

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = ['price'];

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增发票',
                'value' => 'bill/add',
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
            'order_number' => [
                'table' => 'order',
                'elem' => 'input',
                'equal' => true
            ],
            'courier_number' => 'input',
            'courier_company' => 'input',
            'invoice_title' => 'input',
            'address' => 'input',
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
            'id' => 'code',
            'order_number' => [
                'code',
                'table' => 'order'
            ],
            'courier_number' => 'empty',
            'courier_company' => 'empty',
            'price' => [
                'code',
                'title' => '票据金额'
            ],
            'invoice_title',
            'address',
            'state' => [
                'code',
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
            'order_sub_id' => [
                'readonly' => true,
                'same_row' => true
            ],
            'select_order' => [
                'title' => false,
                'elem' => 'button',
                'value' => '选择订单',
                'script' => '$.showPage("order-sub.list")'
            ],
            'courier_number',
            'courier_company',
            'invoice_title',
            'address' => [
                'label' => 5
            ],
            'state' => [
                'elem' => 'select'
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
                ['table' => 'order_sub'],
                [
                    'left_table' => 'order_sub',
                    'table' => 'order'
                ]
            ],
            'select' => [
                'order.order_number',
                'order_sub.price',
                'bill.*'
            ],
            'order' => [
                'bill.update_time DESC'
            ]
        ];
    }
}
