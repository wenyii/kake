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
                'title' => '产品',
                'tip'
            ],
            'username' => [
                'code',
                'title' => '购买粉丝'
            ],
            'survey' => [
                'html',
                'title' => '分佣额明细（取决于套餐状态）'
            ],
            'commission_success' => [
                'title' => '成功分佣额',
                'tpl' => '￥%s',
                'code'
            ],
            'commission_fail' => [
                'title' => '失败分佣额',
                'tpl' => '￥%s',
                'code'
            ],
            'commission' => [
                'html',
                'table' => 'product_producer'
            ],
            'join_count' => [
                'title' => '统计参与'
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
                    'table' => 'order',
                    'left_on_field' => 'id',
                    'right_on_field' => 'producer_log_id'
                ],
                [
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
                'order.id AS order_id',
                'order.price',
                'producer_product.type',
                'user.username',
                'product.title',
                'producer_log.product_id',
                'producer_log.state'
            ],
            'where' => [
                ['producer_log.producer_id' => self::$uid],
                ['producer_log.state' => 1],
                ['producer_product.producer_id' => self::$uid],
                ['order.payment_state' => 1],
                ['order.state' => 1],
            ]
        ];
    }

    /**
     * 处理列表
     *
     * @param array $list
     *
     * @return array
     */
    public function sufHandleFields($list)
    {
        $orderIds = array_column($list, 'order_id');
        $subList = $this->service('general.list', [
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
                ]
            ],
        ]);

        $orderSub = $this->controller('order-sub');
        array_walk($subList, function (&$value) use ($orderSub) {
            $value = $this->callMethod('sufHandleField', $value, [$value], $orderSub);
        });

        $list = array_combine($orderIds, $list);
        $stateOk = OrderSubController::$stateOk;

        foreach ($subList as $k => &$v) {

            $id = $v['order_id'];
            $state = $v['state'];

            if (!isset($list[$id]['join_count'])) {
                $list[$id] = array_merge($list[$id], [
                    'join_count' => 0,
                    'commission_success' => 0,
                    'commission_fail' => 0,
                    'survey' => []
                ]);
            }

            $survey = &$list[$id]['survey'];
            if (!isset($survey[$state])) {
                $survey[$state] = [
                    'info' => $v['state_info'],
                    'num' => 1,
                    'result' => in_array($state, $stateOk) ? '✔️' : '✘',
                    'price' => $v['price']
                ];
            } else {
                $survey[$state]['num'] += 1;
                $survey[$state]['price'] += $v['price'];
            }

            if (in_array($state, $stateOk)) {
                $list[$id]['join_count'] += 1;
                $list[$id]['commission_success'] += $v['price'];
            } else {
                $list[$id]['commission_fail'] += $v['price'];
            }
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if ($action == 'index') {

            $productCtrl = $this->controller('product');
            $data = $this->callMethod('sufHandleField', [], [
                ['id' => $record['product_id']],
                'ajaxModalListProducer'
            ], $productCtrl);
            $record['commission'] = ($record['type'] ? $data['type_percent'] : $data['type_fixed']);

            $orderCtrl = $this->controller('order');
            $record = $this->callMethod('sufHandleField', [], [$record], $orderCtrl);

            $record['join_count'] = $record['join_count'] ? '✔️' : '✘';
            $record['survey'] = ViewHelper::createTable($record['survey'], [
                '状态',
                '个数',
                '分佣',
                '总金额'
            ], ['price' => '￥%s']);
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
