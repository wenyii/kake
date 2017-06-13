<?php

namespace backend\controllers;

use Yii;

/**
 * 分销订单管理
 */
class ProducerOrderController extends GeneralController
{
    // 模型
    public static $modelName = 'ProducerOrder';

    // 模型描述
    public static $modelInfo = '分销订单';

    public static $uid;

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
            'title' => [
                'title' => '产品'
            ],
            'type' => [
                'title' => '类型',
                'code',
                'info',
                'color' => [
                    0 => 'default',
                    1 => 'primary'
                ]
            ],
            'commission' => [
                'html',
                'title' => '分佣档次'
            ],
            'state' => [
                'code',
                'info',
                'color' => [
                    0 => 'default',
                    1 => 'info',
                ]
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
                ['table' => 'order'],
                [
                    'left_table' => 'order',
                    'table' => 'producer_product',
                    'left_on_field' => 'product_id',
                    'right_on_field' => 'product_id'
                ],
                [
                    'left_table' => 'order',
                    'table' => 'user'
                ],
                [
                    'left_table' => 'order',
                    'table' => 'product'
                ]
            ],
            'select' => [
                'order.order_number',
                'order.product_id',
                'order.price',
                'producer_product.type',
                'user.username',
                'product.title'
            ],
            'where' => [
                ['producer_order.producer_id' => self::$uid],
                ['producer_order.state' => 1],
                ['order.payment_state' => 1],
                ['>', 'order.state', 0],
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if (!empty($record['id']) && $action == 'index') {

            $this->dump($record);
            $controller = $this->controller('product');
            $data = $this->callMethod('sufHandleField', [], [
                ['id' => $record['product_id']],
                'ajaxModalListProducer'
            ], $controller);
            $record['commission'] = ($record['type'] ? $data['type_percent'] : $data['type_fixed']);
        }

        return parent::sufHandleField($record, $action, $callback);
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        self::$uid = $this->user->id;

        return parent::beforeAction($action);
    }
}
