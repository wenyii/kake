<?php

namespace frontend\controllers;

use common\components\Helper;
use Yii;
use yii\helpers\Url;

/**
 * Order controller
 */
class OrderController extends GeneralController
{
    /**
     * @const pay code for channel
     */
    const PAY_CODE_WX = 0;
    const PAY_CODE_ALI = 1;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->mustLogin();
    }

    /**
     * @var array 子订单查询条件
     */
    public static $orderSubCondition = [
        'table' => 'order_sub',
        'join' => [
            [
                'table' => 'order'
            ],
            [
                'left_table' => 'order',
                'table' => 'product'
            ],
            [
                'table' => 'bill',
                'left_on_field' => 'id',
                'right_on_field' => 'order_sub_id'
            ],
            [
                'left_table' => 'product',
                'table' => 'hotel'
            ],
            [
                'left_table' => 'product',
                'table' => 'product_description',
            ],
            [
                'left_table' => 'product',
                'table' => 'attachment',
                'as' => 'cover',
                'left_on_field' => 'attachment_cover',
            ]
        ],
        'select' => [
            'product.*',
            'product.id AS product_id',

            'bill.*',
            'bill.id AS bill_id',

            'hotel.name AS hotel_name',
            'hotel.address',

            'product_description.*',

            'cover.deep_path AS cover_deep_path',
            'cover.filename AS cover_filename',

            'order.*',
            'order.id AS order_id',

            'order_sub.*'
        ]
    ];

    /**
     * 微信下单并创建本地订单
     *
     * @access public
     *
     * @link   http://leon.m.kakehotels.com/order/wx/?xxx
     * @return string
     */
    public function actionWx()
    {
        $params = $this->validateSafeLink();

        $product = $this->getProduct($params['product_id']);
        if (empty($product)) {
            $this->error(Yii::t('common', 'product does not exist'));
        }

        $packageData = $this->listProductPackage($params['product_id']);
        if (empty($packageData)) {
            $this->error(Yii::t('common', 'product package does not exist'));
        }

        $price = 0;
        $_package = [];
        foreach ($params['package'] as $id => $number) {
            if (!isset($packageData[$id])) {
                $this->error(Yii::t('common', 'product package illegal'));
            }

            $_package[$id] = $packageData[$id];
            $_package[$id]['number'] = $number;
            $_package[$id]['price'] = intval($packageData[$id]['price'] * 100);

            $price += $_package[$id]['price'] * $number;
        }

        // 生成订单编号
        $orderNumber = Helper::createOrderNumber(self::PAY_CODE_WX, $this->user->id);

        // 微信统一下单
        $prepayId = Yii::$app->wx->order([
            'body' => $product['title'],
            'out_trade_no' => $orderNumber,
            'total_fee' => $price,
            'notify_url' => Yii::$app->params['frontend_url'] . '/order/wx-paid',
            'openid' => $this->user->openid,
        ]);

        // 本地下单
        if ($prepayId) {
            $result = $this->service('order.add', [
                'order_number' => $orderNumber,
                'user_id' => $this->user->id,
                'product_id' => $product['id'],
                'payment_method' => self::PAY_CODE_WX,
                'price' => $price,
                'package' => $_package
            ]);

            if (is_string($result)) {
                $this->error(Yii::t('common', $result));
            }
        }

        $this->sourceJs = ['order/wx'];

        return $this->render('wx', [
            'json' => Yii::$app->wx->payment->configForPayment($prepayId)
        ]);
    }

    /**
     * 支付宝下单并创建本地订单
     *
     * @access public
     *
     * @param integer $product_id
     * @param array   $package
     *
     * @link   http://leon.m.kakehotels.com/order/ali/?product_id=1
     * @return string
     */
    public function actionAli($product_id = 1, $package = [1 => 1])
    {
        Yii::$app->ali->order([
            'subject' => '一直破洞的袜子',
            'out_trade_no' => '11223344556677',
            'total_amount' => 0.02,
        ]);
        return null;
    }

    /**
     * 获取主订单详情
     *
     * @access public
     *
     * @param string $param
     * @param string $field
     *
     * @return array
     */
    public function getOrder($param, $field = 'id')
    {
        if (empty($param)) {
            $this->error(Yii::t('common', 'order param required'));
        }

        $detail = $this->service('order.detail', [
            'join' => [
                ['table' => 'product'],
            ],
            'select' => [
                'product.*',
                'order.*'
            ],
            'order' => 'order.id DESC',
            'where' => [
                ['order.' . $field => $param],
                ['order.state' => 1]
            ]
        ]);

        return $detail;
    }

    /**
     * 订单支付 - 一般用于首次支付失败后重复调用
     *
     * @link http://leon.m.kakehotels.com/order/wx-pay/?xxx
     * @return string
     */
    public function actionWxPay()
    {
        $params = $this->validateSafeLink();
        $order = $this->getOrder($params['order_id']);

        // 查询订单
        $result = Yii::$app->wx->payment->query($order['order_number']);
        if (!in_array($result->trade_state, [
            'NOTPAY',
            'PAYERROR'
        ])) {
            $this->error(Yii::t('common', 'resubmit the order please'));
        }

        // 生成订单编号
        $orderNumber = Helper::createOrderNumber(self::PAY_CODE_WX, $this->user->id);

        // 微信关闭旧订单并统一下单
        Yii::$app->wx->payment->close($order['order_number']);
        $prepayId = Yii::$app->wx->order([
            'body' => $order['title'],
            'out_trade_no' => $orderNumber,
            'total_fee' => $order['price'],
            'notify_url' => Yii::$app->params['frontend_url'] . '/order/wx-paid',
            'openid' => $this->user->openid,
        ]);

        // 更新本地订单编号
        if ($prepayId) {
            $result = $this->service('order.update-order-number', [
                'id' => $params['order_id'],
                'order_number' => $orderNumber
            ]);

            if (is_string($result)) {
                $this->error(Yii::t('common', $result));
            }
        }

        $this->sourceJs = ['order/wx'];

        return $this->render('wx', [
            'json' => Yii::$app->wx->payment->configForPayment($prepayId)
        ]);
    }

    /**
     * 微信支付回调处理并更新本地订单
     */
    public function actionWxPaid()
    {
        $payment = Yii::$app->wx->payment;
        $response = $payment->handleNotify(function ($notify, $successful) {

            $result = $this->service('order.pay-handler', [
                'order_number' => $notify->out_trade_no,
                'paid_result' => $successful
            ]);

            if (is_string($result)) {
                Yii::error(Yii::t('common', $result));

                return $result;
            }

            return true;
        });

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['wx-paid'])) {
            $action->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }
}