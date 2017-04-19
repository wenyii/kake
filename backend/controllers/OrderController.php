<?php

namespace backend\controllers;

use Yii;

/**
 * 主订单管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except front
 */
class OrderController extends GeneralController
{
    // 模型
    public static $modelName = 'Order';

    // 模型描述
    public static $modelInfo = '主订单';

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = ['price'];

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return array_merge(parent::indexOperation(), [
            [
                'text' => '查询订单',
                'value' => 'select-order',
                'level' => 'primary',
                'icon' => 'globe',
                'params' => ['order_number', 'payment_method']
            ],
            [
                'text' => '子订单',
                'value' => 'order-sub/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => ['order_number']
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'order_number' => [
                'elem' => 'input',
                'equal' => true
            ],
            'username' => [
                'table' => 'user',
                'elem' => 'input',
                'title' => '下单人'
            ],
            'phone' => [
                'table' => 'user',
                'elem' => 'input',
                'title' => '下单人联系方式'
            ],
            'product_title' => [
                'table' => 'product',
                'field' => 'title',
                'elem' => 'input',
                'title' => '产品标题'
            ],
            'hotel_name' => [
                'table' => 'hotel',
                'field' => 'name',
                'elem' => 'input',
                'title' => '酒店名称'
            ],
            'payment_method' => [
                'value' => 'all'
            ],
            'payment_state' => [
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
                'color' => 'default'
            ],
            'username' => [
                'title' => '下单人'
            ],
            'phone' => [
                'title' => '下单人联系方式',
                'empty'
            ],
            'product_title' => [
                'title' => '产品标题',
                'tip'
            ],
            'hotel_name' => [
                'title' => '酒店名称',
                'tip'
            ],
            'price' => 'code',
            'package_info' => [
                'title' => '套餐详情',
                'html'
            ],
            'payment_state' => [
                'code',
                'info',
                'color' => [
                    0 => 'warning',
                    1 => 'success',
                    2 => 'default'
                ]
            ],
            'payment_method' => [
                'code',
                'info',
                'color' => [
                    0 => 'success',
                    1 => 'info'
                ]
            ],
            'add_time' => 'tip',
            'update_time' => 'tip',
            'state' => [
                'code',
                'info'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListAssist()
    {
        return [
            'id' => 'code',
            'order_number' => 'code',
            'username' => [
                'title' => '下单人'
            ],
            'hotel_name' => [
                'title' => '酒店名称'
            ],
            'product_title' => [
                'title' => '产品标题'
            ],
            'price' => 'code',
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
            'payment_state' => [
                'elem' => 'select'
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
                ['table' => 'user'],
                ['table' => 'product'],
                [
                    'left_table' => 'product',
                    'table' => 'hotel',
                    'left_on_field' => 'hotel_id'
                ]
            ],
            'select' => [
                'user.username',
                'user.phone',
                'product.title AS product_title',
                'hotel.name AS hotel_name',
                'order.*'
            ],
            'order' => 'order.id DESC'
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if (!empty($record['id']) && $action == 'list') {
            $package = $this->service('general.list-package-by-order-id', ['order_id' => $record['id']]);

            $record['package_record'] = $package;
            $record['package_info'] = null;
            foreach ($package as $item) {
                $record['package_info'] .= $item['name'] . ' × ' . $item['number'] . '<br>';
            }
        }

        return parent::sufHandleField($record, $action, $callback);
    }

    /**
     * 查询订单
     *
     * @access public
     *
     * @param string $order_number
     * @param integer $payment_method
     */
    public function actionSelectOrder($order_number, $payment_method)
    {
        if ($payment_method) {
            $result = Yii::$app->ali->alipayTradeQuery($order_number);
            if (is_string($result)) {
                Yii::error($result);
                $this->error($result);
            }
            $state = $result['trade_status'];
            $paymentState = [
                'WAIT_BUYER_PAY' => '订单等待支付中',
                'TRADE_CLOSED' => '订单未付款或已全额退款',
                'TRADE_SUCCESS' => '订单已完成支付',
                'TRADE_FINISHED' => '订单已经完成，不可退款',
            ];
        } else {
            $result = Yii::$app->wx->payment->query($order_number);
            $state = $result->trade_state;
            $paymentState = [
                'SUCCESS' => '订单已完成支付',
                'NOTPAY' => '订单暂未支付',
                'REFUND' => '订单已经申请退款',
                'CLOSE' => '订单已经关闭',
                'USERPAYING' => '订单等待支付中',
                'PAYERROR' => '订单支付失败',
            ];
        }

        $prefix = $payment_method ? '[支付宝]' : '[微信]';

        Yii::$app->session->setFlash('info', $prefix . ' ' . $paymentState[$state]);
        $this->goReference($this->getControllerName());
    }
}
