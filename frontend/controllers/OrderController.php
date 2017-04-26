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
     * @var array 子订单查询条件
     */
    public static $orderSubCondition = [
        'table' => 'order_sub',
        'join' => [
            ['table' => 'order'],
            ['table' => 'product_package'],
            [
                'left_table' => 'order',
                'table' => 'product'
            ],
            [
                'left_table' => 'product',
                'table' => 'attachment',
                'left_on_field' => 'attachment_cover',
            ],
            [
                'left_table' => 'product',
                'table' => 'hotel'
            ],
            [
                'table' => 'bill',
                'left_on_field' => 'id',
                'right_on_field' => 'order_sub_id'
            ],
            [
                'table' => 'order_instructions_log',
                'left_on_field' => 'id',
                'right_on_field' => 'order_sub_id'
            ]
        ],
        'select' => [
            'order_sub.*',

            'order.order_number',

            'product_package.name AS package_name',

            'product.title',
            'product.destination',
            'product.attachment_cover',

            'attachment.deep_path AS cover_deep_path',
            'attachment.filename AS cover_filename',

            'hotel.name AS hotel_name',

            'bill.id AS bill_id',
            'bill.courier_company',
            'bill.courier_number',
            'bill.invoice_title',
            'bill.address',

            'order_instructions_log.remark',
        ]
    ];

    /**
     * 订单中心
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = [
            'order/index'
        ];

        $ongoing = $this->renderListPage(1, 'ongoing');
        $completed = $this->renderListPage(1, 'completed');

        return $this->render('index', compact('ongoing', 'completed'));
    }

    /**
     * ajax 获取下一页订单列表
     */
    public function actionAjaxList()
    {
        $page = Yii::$app->request->post('page');
        $type = Yii::$app->request->post('type');

        $this->success([
            'html' => $this->renderListPage($page, $type)
        ]);
    }

    /**
     * 渲染订单列表 html
     *
     * @access private
     *
     * @param integer $page
     * @param string $type
     *
     * @return string
     */
    private function renderListPage($page, $type)
    {
        $map = [
            'ongoing' => [0, 1, 2, 3],
            'completed' => [4, 5]
        ];

        if (!isset($map[$type])) {
            return null;
        }

        $list = $this->listOrderSub($page, $map[$type]);
        $content = $this->renderPartial('list-' . $type, compact('list'));

        return $content;
    }

    /**
     * 申请预约页面
     */
    public function actionApplyOrder()
    {
        $this->sourceCss = null;
        $this->sourceJs = [
            'order/index'
        ];

        return $this->render('apply-order');
    }

    /**
     * 预约申请
     */
    public function actionAjaxApplyOrder()
    {
        $result = $this->service('order.apply-order', [
            'order_sub_id' => Yii::$app->request->post('id'),
            'name' => Yii::$app->request->post('name'),
            'phone' => Yii::$app->request->post('phone'),
            'date' => Yii::$app->request->post('date')
        ]);

        if (is_string($result)) {
            $this->fail($result);
        }

        $this->success(null, '预约申请已提交');
    }

    /**
     * 申请退款页面
     */
    public function actionApplyRefund()
    {
        $this->sourceCss = null;
        $this->sourceJs = [
            'order/index'
        ];

        return $this->render('apply-refund');
    }

    /**
     * 退款申请
     */
    public function actionAjaxApplyRefund()
    {
        $result = $this->service('order.apply-refund', [
            'order_sub_id' => Yii::$app->request->post('id'),
            'remark' => Yii::$app->request->post('remark')
        ]);

        if (is_string($result)) {
            $this->fail($result);
        }

        $this->success(null, 'refund request submitted');
    }

    /**
     * 开具发票
     */
    public function actionAjaxApplyBill()
    {
        $result = $this->service('order.apply-bill', [
            'order_sub_id' => Yii::$app->request->post('id'),
            'invoice_title' => Yii::$app->request->post('company') ? Yii::$app->request->post('company_name') : null,
            'address' => Yii::$app->request->post('address')
        ]);

        if (is_string($result)) {
            $this->fail($result);
        }

        $this->success(null, 'invoice request submitted');
    }

    // --↓↓- Payment -↓↓--

    /**
     * 第三方下单前的本地下单
     *
     * @access private
     *
     * @param integer $payCode
     *
     * @return array
     */
    private function localOrder($payCode)
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

        $packagePurchaseTimes = $this->service('order.purchase-times', [
            'user_id' => $this->user->id
        ]);

        $price = 0;
        $_package = [];
        foreach ((array) $params['package'] as $id => $number) {
            if (!isset($packageData[$id])) {
                $this->error(Yii::t('common', 'product package illegal'));
            }

            $limit = 'purchase_limit';
            if (!empty($packageData[$id][$limit])) {
                if (empty($packagePurchaseTimes[$id])) {
                    if ($number > $packageData[$id][$limit]) {
                        $this->error(Yii::t('common', 'product package greater then limit', [
                            'buy' => $number,
                            'max' => $packageData[$id][$limit]
                        ]));
                    }
                } else {
                    if ($number > $packageData[$id][$limit] - $packagePurchaseTimes[$id]) {
                        $this->error(Yii::t('common', 'product package greater then limit with purchased', [
                            'buy' => $number,
                            'max' => $packageData[$id][$limit],
                            'buys' => $packagePurchaseTimes[$id]
                        ]));
                    }
                }
            }

            $_package[$id] = $packageData[$id];
            $_package[$id]['number'] = $number;
            $_package[$id]['price'] = intval($packageData[$id]['price'] * 100);

            $price += $_package[$id]['price'] * $number;
        }

        // 生成订单编号
        $orderNumber = Helper::createOrderNumber($payCode, $this->user->id);

        // 本地下单
        $result = $this->service('order.add', [
            'order_number' => $orderNumber,
            'user_id' => $this->user->id,
            'product_id' => $product['id'],
            'payment_method' => $payCode,
            'price' => $price,
            'package' => $_package
        ]);

        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }

        return [
            $orderNumber,
            $product['title'],
            $price
        ];
    }

    /**
     * 微信下单
     *
     * @access  public
     * @link    http://leon.m.kakehotels.com/order/wx/?xxx
     * @license link create by $this->createSafeLink()
     * @return string
     */
    public function actionWx()
    {
        list($outTradeNo, $body, $price) = $this->localOrder(self::PAY_CODE_WX);

        return $this->wxPay($outTradeNo, $body, $price);
    }

    /**
     * 支付宝下单
     *
     * @access  public
     * @link    http://leon.m.kakehotels.com/order/ali?xxx
     * @license link create by $this->createSafeLink()
     * @return string
     */
    public function actionAli()
    {
        list($outTradeNo) = $this->localOrder(self::PAY_CODE_ALI);

        return $this->createSafeLink([
            'order_number' => $outTradeNo,
            'first' => true
        ], 'order/ali-pay');
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
     * 微信支付订单（可重复调用）
     *
     * @link http://leon.m.kakehotels.com/order/wx-pay/?xxx
     * @return string
     */
    public function actionWxPay()
    {
        $params = $this->validateSafeLink();
        $order = $this->getOrder($params['order_number'], 'order_number');

        // 查询订单
        $result = Yii::$app->wx->payment->query($order['order_number']);
        if (!in_array($result->trade_state, [
            'NOTPAY',
            'PAYERROR'
        ])
        ) {
            $this->error(Yii::t('common', 'resubmit the order please'));
        }

        // 生成订单编号
        $orderNumber = Helper::createOrderNumber(self::PAY_CODE_WX, $this->user->id);

        // 更新本地订单编号
        $result = $this->service('order.update-order-number', [
            'id' => $params['order_id'],
            'order_number' => $orderNumber
        ]);

        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }

        // 关闭旧订单
        Yii::$app->wx->payment->close($order['order_number']);

        return $this->wxPay($orderNumber, $order['title'], $order['price']);
    }

    /**
     * 微信支付页面渲染
     *
     * @param string $outTradeNo
     * @param string $body
     * @param float  $price
     *
     * @return string
     */
    private function wxPay($outTradeNo, $body, $price)
    {
        $prepayId = Yii::$app->wx->order([
            'body' => $body,
            'out_trade_no' => $outTradeNo,
            'total_fee' => $price,
            'notify_url' => Yii::$app->params['frontend_url'] . '/order/wx-paid',
            'openid' => $this->user->openid,
        ]);
        if (!is_string($prepayId)) {
            $this->error(json_encode($prepayId, JSON_UNESCAPED_UNICODE));
        }

        $this->sourceJs = ['order/wx'];

        return $this->render('wx', [
            'json' => Yii::$app->wx->payment->configForPayment($prepayId)
        ]);
    }

    /**
     * 支付宝支付订单（可重复调用）
     *
     * @link http://leon.m.kakehotels.com/order/ali-pay?xxx
     * @return mixed
     */
    public function actionAliPay()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return '请在普通浏览器中打开访问 ⚔';
        }

        $params = $this->validateSafeLink(false);
        $order = $this->getOrder($params['order_number'], 'order_number');

        // 查询订单
        $result = Yii::$app->ali->alipayTradeQuery($order['order_number']);
        if (is_array($result)) {

            if (!in_array($result['trade_status'], [
                'WAIT_BUYER_PAY',
                'TRADE_CLOSED'
            ])
            ) {
                $this->error(Yii::t('common', 'resubmit the order please'));
            }

            // 生成订单编号
            $orderNumber = Helper::createOrderNumber(self::PAY_CODE_ALI, $order['user_id']);

            // 更新本地订单编号
            $result = $this->service('order.update-order-number', [
                'id' => $order['id'],
                'order_number' => $orderNumber
            ]);

            if (is_string($result)) {
                $this->error(Yii::t('common', $result));
            }

            // 关闭旧订单
            Yii::$app->ali->alipayTradeClose($order['order_number']);
        }

        $notifyUrl = Yii::$app->params['frontend_url'] . '/order/ali-paid';
        Yii::$app->ali->alipayTradeWapPay([
            'subject' => $order['title'],
            'out_trade_no' => isset($orderNumber) ? $orderNumber : $order['order_number'],
            'total_amount' => intval($order['price']) / 100,
        ], $notifyUrl);

        return true;
    }

    /**
     * 微信支付回调
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
     * 支付宝支付回调
     */
    public function actionAliPaid()
    {
        $params = Yii::$app->request->post();
        Yii::info('支付宝异步回调数据：' . json_encode($params, JSON_UNESCAPED_UNICODE));

        if (empty($params)) {
            return null;
        }

        if (!Yii::$app->ali->validateSignAsync($params)) {
            Yii::error('支付宝异步回调, 签名验证失败: ' . json_encode($params, JSON_UNESCAPED_UNICODE));
        }

        $result = $this->service('order.pay-handler', [
            'order_number' => $params['out_trade_no'],
            'paid_result' => true
        ]);

        if (is_string($result)) {
            Yii::error(Yii::t('common', $result));
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!in_array($action->id, [
            'ali-pay',
            'ali-paid'
        ])
        ) {
            $this->mustLogin();
        }

        if (in_array($action->id, [
            'wx-paid',
            'ali-paid'
        ])) {
            $action->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }
}