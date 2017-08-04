<?php

namespace backend\controllers;

use backend\components\ViewHelper;
use common\components\Helper;
use Yii;

/**
 * 分销记录管理
 */
class ProducerLogController extends GeneralController
{
    // 模型
    public static $modelName = 'ProducerLog';

    // 模型描述
    public static $modelInfo = '分销记录';

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = [
        'log_amount_in',
        'log_amount_out'
    ];

    // 用户id
    public static $uid;

    // 标记
    public static $success = '<span class="text-success">✔</span>';
    public static $fail = '<span class="text-danger">✘</span>';

    /**
     * @inheritDoc
     */
    public static function myOperations()
    {
        return [
            [
                'text' => '结算佣金',
                'value' => 'settlement',
                'level' => 'primary confirm-button',
                'icon' => 'usd'
            ],
            [
                'text' => '结算说明',
                'script' => true,
                'level' => 'warning',
                'value' => '$.showPage("producer-log.help")',
                'icon' => 'info-sign'
            ]
        ];
    }

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
    public static function myOperation()
    {
        return self::indexOperation();
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'producer_name' => [
                'title' => '分销商',
                'elem' => 'input',
                'table' => 'producer_user',
                'field' => 'username'
            ],
            'buyer_name' => [
                'title' => '购买粉丝',
                'elem' => 'input',
                'table' => 'buyer_user',
                'field' => 'username'
            ],
            'product_id' => [
                'elem' => 'input',
                'equal' => true,
                'table' => 'producer_log'
            ],
            'state' => [
                'value' => 1
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
                'title' => '产品',
                'tip'
            ],
            'producer_name' => [
                'title' => '分销商',
                'code'
            ],
            'buyer_name' => [
                'code',
                'title' => '购买粉丝'
            ],
            'product_id' => [
                'title' => '产品ID',
                'tip'
            ],
            'type' => [
                'title' => '分佣类型',
                'code',
                'info',
                'color' => [
                    0 => 'default',
                    1 => 'primary'
                ]
            ],
            'survey_table' => [
                'html',
                'title' => '分佣额明细（取决于套餐状态）'
            ],
            'amount_in' => [
                'title' => '入围订单额',
                'price',
                'code'
            ],
            'amount_out' => [
                'title' => '淘汰订单额',
                'price',
                'code'
            ],
            'commission_table' => [
                'html',
                'title' => '分佣档次',
                'table' => 'product_producer'
            ],
            'counter_info' => [
                'title' => '可否结算',
                'html'
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
    public static function myAssist()
    {
        $assist = self::indexAssist();
        unset($assist['producer_name'], $assist['type'], $assist['state']);
        $assist['counter'] = [
            'title' => '产品销量',
            'code'
        ];
        $assist['commission_quota'] = [
            'title' => '分佣金额',
            'code',
            'price' => 5,
        ];

        return $assist;
    }

    /**
     * @inheritDoc
     */
    public function indexCondition($as = null)
    {
        return array_merge(parent::indexCondition(), [
            'join' => [
                [
                    'table' => 'order',
                    'left_on_field' => 'id',
                    'right_on_field' => 'producer_log_id'
                ],
                [
                    'table' => 'producer_product',
                    'left_on_field' => [
                        'product_id',
                        'producer_id'
                    ],
                    'right_on_field' => [
                        'product_id',
                        'producer_id'
                    ]
                ],
                ['table' => 'product'],
                [
                    'table' => 'user',
                    'as' => 'buyer_user'
                ],
                [
                    'table' => 'user',
                    'left_on_field' => 'producer_id',
                    'as' => 'producer_user'
                ]
            ],
            'select' => [
                'order.id AS order_id',
                'order.price',
                'producer_product.type',
                'buyer_user.username AS buyer_name',
                'producer_user.username AS producer_name',
                'product.title',
                'producer_log.id',
                'producer_log.product_id',
                'producer_log.state'
            ],
            'where' => [
                ['order.payment_state' => 1],
                [
                    '<',
                    'order.state',
                    2
                ],
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function myCondition()
    {
        $condition = $this->indexCondition();
        $condition['where'][1] = ['order.state' => 1];
        $condition['where'][] = ['producer_log.producer_id' => self::$uid];
        $condition['where'][] = ['producer_product.producer_id' => self::$uid];
        $condition['where'][] = ['producer_log.state' => 1];

        return $condition;
    }

    /**
     * 我的分销记录
     *
     * @auth-pass-all
     */
    public function actionMy()
    {
        return parent::showList();
    }

    /**
     * 结算帮助中心
     *
     * @auth-pass-all
     */
    public function actionAjaxModalHelp()
    {
        $this->modal('producer-log/help', [], '结算说明');
    }

    /**
     * 列表我的可结算订单记录
     *
     * @access public
     * @param boolean $settlement
     * @return mixed
     */
    public function listProducerLog($settlement = true)
    {
        $list = $this->showList('my', true, false);
        if (!$settlement) {
            return $list;
        }

        foreach ($list as $key => $item) {
            if (empty($item['sub_counter'])) {
                unset($list[$key]);
            }
        }

        return $list;
    }

    /**
     * 分销订单结算
     *
     * @auth-pass-all
     */
    public function actionSettlement()
    {
        $list = $this->listProducerLog();
        if (empty($list)) {
            Yii::$app->session->setFlash('warning', '暂无可结算的分销订单');
            $this->goReference($this->getControllerName('my'));
        }

        $quota = 0;
        $_list = [];
        foreach ($list as $item) {
            $log = Helper::pullSome($item, [
                'amount_in' => 'log_amount_in',
                'amount_out' => 'log_amount_out',
                'sub_counter' => 'log_sub_counter',
                'commission_quota' => 'log_commission_quota'
            ]);

            $log = $this->preHandleField($log);
            $_list[$item['id']] = $log;
            $quota += $item['commission_quota'];
        }

        $result = $this->service('producer.settlement', [
            'log' => $_list,
            'quota' => (int) ($quota * 100),
            'user_id' => self::$uid
        ]);

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', $result);
        } else {
            $number = count($list);
            $quota = Helper::money($quota);
            $after = Helper::money($result['afterQuota'] / 100);
            Yii::$app->session->setFlash('success', "本次结算订单共计：${number}个，佣金共计：${quota} (保留到小数点后两位)，结算后总佣金余额：${after}");
        }

        $this->goReference($this->getControllerName('my'));
    }

    /**
     * 使用订单 ID 串列表子订单
     *
     * @access private
     *
     * @param array $orderIds
     *
     * @return array
     */
    private function listOrderSubByOrderIds($orderIds)
    {
        $list = $this->service(parent::$apiList, [
            'table' => 'order_sub',
            'select' => [
                'order_id',
                'product_package_id',
                'price',
                'state'
            ],
            'where' => [
                [
                    'in',
                    'order_id',
                    $orderIds
                ],
                [
                    '>=',
                    'price',
                    Yii::$app->params['commission_min_price'] * 100
                ]
            ],
        ]);

        $_list = [];
        $orderSub = $this->controller('order-sub');
        foreach ($list as $item) {

            $item = $this->callMethod('sufHandleField', $item, [$item], $orderSub);
            $state = $item['state'];

            if (empty($_list[$item['order_id']])) {
                $_list[$item['order_id']] = [
                    'sub_counter' => 0,
                    'amount_in' => 0,
                    'amount_out' => 0
                ];
            }

            $_item = &$_list[$item['order_id']];
            if (in_array($state, OrderSubController::$stateOk)) {
                $_item['sub_counter'] += 1;
                $_item['amount_in'] += $item['price'];
            } else {
                $_item['amount_out'] += $item['price'];
            }

            if (empty($_item[$state])) {
                $_item[$state] = [
                    'info' => $item['state_info'],
                    'number' => 1,
                    'amount' => $item['price'],
                    'pass' => in_array($state, OrderSubController::$stateOk) ? self::$success : self::$fail
                ];
            } else {
                $_item[$state]['number'] += 1;
                $_item[$state]['amount'] += $item['price'];
            }
        }

        return $_list;
    }

    /**
     * 产品分佣达标统计
     *
     * @access private
     *
     * @param integer $userId
     * @param array   $productIds
     *
     * @return array
     */
    private function productCounter($userId, $productIds = null)
    {
        $condition = [
            'table' => 'producer_log',
            'join' => [
                [
                    'table' => 'order',
                    'left_on_field' => 'id',
                    'right_on_field' => 'producer_log_id'
                ]
            ],
            'select' => [
                'producer_log.*',
                'order.id AS order_id'
            ],
            'where' => [
                ['producer_log.producer_id' => $userId],
                ['producer_log.state' => 1]
            ]
        ];

        if ($productIds) {
            $condition['where'][] = [
                'in',
                'producer_log.product_id',
                $productIds
            ];
        }

        $list = $this->service(parent::$apiList, $condition);

        list($list, $orderIds) = Helper::valueToKey($list, 'order_id');
        $subList = $this->listOrderSubByOrderIds($orderIds);

        $counter = [];
        foreach ($list as $id => $item) {

            $product = $item['product_id'];
            if (!isset($counter[$product])) {
                $counter[$product] = 0;
            }

            if (!empty($subList[$id]['sub_counter'])) {
                $counter[$product] += 1;
            }
        }

        return $counter;
    }

    /**
     * 在前置字段处理前处理列表
     *
     * @param array $list
     *
     * @return array
     */
    public function sufHandleListBeforeField($list)
    {
        list($list, $orderIds) = Helper::valueToKey($list, 'order_id');
        $subList = $this->listOrderSubByOrderIds($orderIds);

        foreach ($list as $key => &$item) {
            if (empty($subList[$item['order_id']])) {
                unset($list[$key]);
                continue;
            }
            $item['survey'] = $subList[$item['order_id']];
            $survey = Helper::popSome($item['survey'], [
                'sub_counter',
                'amount_in',
                'amount_out'
            ]);
            $item = array_merge($item, $survey);
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if (in_array($action, [
            'index',
            'my'
        ])) {
            $productCtrl = $this->controller('product');
            $data = $this->callMethod('sufHandleField', [], [
                ['id' => $record['product_id']],
                'ajaxModalListProducer'
            ], $productCtrl);

            $key = ProductController::$type[$record['type']];
            $record['commission_data'] = $data['commission_data_' . $key];
            $record['commission_table'] = $data['commission_table_' . $key];

            $orderCtrl = $this->controller('order');
            $record = $this->callMethod('sufHandleField', [], [$record], $orderCtrl);

            $record['counter_info'] = $record['sub_counter'] ? self::$success : self::$fail;
            $record['survey_table'] = ViewHelper::createTable($record['survey'], [
                'info' => '状态',
                'number' => '个数',
                'amount' => '总金额',
                'pass' => '分佣',
            ], [
                'number' => '× %s',
                'amount' => '￥%s'
            ], [
                'info' => 30,
                'number' => 20,
                'amount' => 30,
                'pass' => 20,
            ]);
            unset($record['survey']);
        }

        return parent::sufHandleField($record, $action, $callback);
    }

    /**
     * 在前置字段处理后处理列表
     *
     * @param array  $list
     * @param string $action
     *
     * @return array
     */
    public function sufHandleListAfterField($list, $action = null)
    {
        if ($action != 'my') {
            return $list;
        }

        $productIds = array_column($list, 'product_id');
        $counter = $this->productCounter(self::$uid, $productIds);

        foreach ($list as &$value) {

            $product = $value['product_id'];
            $value['counter'] = 0;
            $value['commission_quota'] = 0;

            if (empty($counter[$product]) || empty($value['commission_data'])) {
                continue;
            }

            $count = $value['counter'] = $counter[$product];
            $type = ProductController::$type[$value['type']];

            $value['commission_quota'] = 0;
            foreach ($value['commission_data'] as $item) {
                if ($count >= $item['from_sales'] && (empty($item['to_sales']) || $count <= $item['to_sales'])) {

                    $in = $value['amount_in'];
                    $price = ($in + $value['amount_out']) * 100;
                    $rate = ($in * 100) / $price;

                    if ($type == 'percent') {
                        $value['commission_quota'] = $in * (($item['commission'] / 100) * $rate);
                    } else if ($type == 'fixed') {
                        $value['commission_quota'] = $item['commission'] * $rate;
                    }

                    break;
                }
            }
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        self::$uid = $this->user->id;

        return true;
    }
}
