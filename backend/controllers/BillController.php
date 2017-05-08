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
     * @var array Hook
     */
    public static $hookLogic = ['handle'];

    /**
     * @var array Field
     */
    public static $_handle = [
        0 => '待处理',
        1 => '已处理'
    ];

    /**
     * 是否处理
     *
     * @param array $record
     *
     * @return boolean
     */
    public static function handleLogic($record)
    {
        return !empty($record['courier_number']);
    }

    /**
     * 是否处理反向逻辑
     *
     * @param integer $index
     *
     * @return array
     */
    public static function handleReverseWhereLogic($index)
    {
        $indexes = [
            0 => [
                [
                    'or',
                    ['bill.courier_number' => ''],
                    ['bill.courier_number' => null],
                ]
            ],
            1 => [
                [
                    '<>',
                    'bill.courier_number',
                    ''
                ],
                [
                    'NOT',
                    ['bill.courier_number' => null],
                ]
            ]
        ];

        return isset($indexes[$index]) ? $indexes[$index] : [];
    }

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
            'handle' => [
                'title' => '状况',
                'value' => 'all'
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
            'handle' => [
                'title' => '状况',
                'info',
                'code',
                'color' => [
                    0 => 'warning',
                    1 => 'success'
                ]
            ],
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
