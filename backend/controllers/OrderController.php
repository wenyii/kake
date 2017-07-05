<?php

namespace backend\controllers;

use backend\components\ViewHelper;
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

    public static $payment = [
        0 => 'WeChat',
        1 => 'AliPay'
    ];

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
                'params' => [
                    'order_number',
                    'payment_method'
                ]
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
                'title' => '下单用户'
            ],
            'real_name' => [
                'table' => 'order_contacts',
                'elem' => 'input',
                'title' => '订单联系人'
            ],
            'phone' => [
                'table' => 'order_contacts',
                'elem' => 'input',
                'title' => '订单联系电话'
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
                'title' => '下单用户',
                'tip'
            ],
            'producer_username' => [
                'title' => '分销商户',
                'tip'
            ],
            'real_name' => [
                'title' => '订单联系人'
            ],
            'phone' => [
                'title' => '订单联系电话'
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
                'max-width' => '200px',
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
                'info',
                'color' => [
                    0 => 'danger',
                    1 => 'info',
                    2 => 'default'
                ]
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
                'title' => '下单用户'
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
                ],
                [
                    'left_table' => 'order',
                    'table' => 'order_contacts'
                ],
                ['table' => 'producer_log'],
                [
                    'left_table' => 'producer_log',
                    'table' => 'user',
                    'as' => 'producer_user',
                    'left_on_field' => 'producer_id'
                ]
            ],
            'select' => [
                'user.username',
                'product.title AS product_title',
                'hotel.name AS hotel_name',
                'order_contacts.real_name',
                'order_contacts.phone',
                'producer_user.username AS producer_username',
                'order.*'
            ],
            'order' => 'order.state DESC, order.id DESC'
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if ($action == 'index') {
            $package = $this->service('general.list-package-by-order-id', ['order_id' => $record['id']]);

            $record['package_record'] = $package;
            $record['package_info'] = null;
            foreach ($package as $item) {
                $record['package_info'][] = [
                    $item['name'],
                    $item['number']
                ];
            }

            $record['package_info'] = ViewHelper::createTable($record['package_info'], null, null, [75]);
        }

        return parent::sufHandleField($record, $action, $callback);
    }

    /**
     * 查询订单
     *
     * @access public
     *
     * @param string  $order_number
     * @param integer $payment_method
     */
    public function actionSelectOrder($order_number, $payment_method)
    {
        $payment = self::$payment[$payment_method];
        if ($payment == 'AliPay') {
            $paymentState = [
                'WAIT_BUYER_PAY' => '订单等待支付中',
                'TRADE_CLOSED' => '订单已全额退款(或未付款)',
                'TRADE_SUCCESS' => '订单已完成支付',
                'TRADE_FINISHED' => '订单已经完成(不可退款)',
            ];

            $result = Yii::$app->ali->alipayTradeQuery($order_number);
            if (is_string($result)) {
                $info = $result;
            } else {
                $info = $paymentState[$result['trade_status']];
            }
        } else {
            $paymentState = [
                'SUCCESS' => '订单已完成支付',
                'NOTPAY' => '订单暂未支付',
                'REFUND' => '订单已经申请退款',
                'CLOSE' => '订单已经关闭',
                'USERPAYING' => '订单等待支付中',
                'PAYERROR' => '订单支付失败',
            ];
            $result = Yii::$app->wx->payment->query($order_number);

            if (isset($result->trade_state)) {
                $info = $paymentState[$result->trade_state];
            } else {
                $info = '订单号不存在';
            }
        }

        Yii::$app->session->setFlash('info', '<' . $payment . ' : 接口反馈> ' . $info);
        $this->goReference($this->getControllerName('index'));
    }
}
