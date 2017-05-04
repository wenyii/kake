<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 子订单管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except front
 */
class OrderSubController extends GeneralController
{
    // 模型
    public static $modelName = 'OrderSub';

    // 模型描述
    public static $modelInfo = '子订单';

    /**
     * @var string 模态框的名称
     */
    public static $ajaxModalListTitle = '选择子订单';

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
                'text' => '主订单',
                'value' => 'order/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => ['order_number']
            ],
            [
                'text' => '同意预约',
                'value' => 'agree-order',
                'level' => 'success confirm-button',
                'icon' => 'thumbs-up',
                'show_condition' => function ($record) {
                    return $record['state'] == 1;
                }
            ],
            [
                'text' => '拒绝预约',
                'type' => 'script',
                'level' => 'warning',
                'icon' => 'thumbs-down',
                'value' => '$.showPage',
                'params' => function ($record) {
                    return [
                        'order-instructions-log.refuse',
                        [
                            'order_sub_id' => $record['id'],
                            'type' => 2
                        ],
                    ];
                },
                'show_condition' => function ($record) {
                    return $record['state'] == 1;
                }
            ],
            [
                'text' => '备注',
                'value' => 'order-instructions-log/index',
                'params' => function ($record) {
                    return ['order_sub_id' => $record['id']];
                },
                'level' => 'default',
                'icon' => 'paperclip'
            ],
            [
                'text' => '同意退款',
                'value' => 'agree-refund',
                'level' => 'success confirm-button',
                'icon' => 'thumbs-up',
                'show_condition' => function ($record) {
                    return $record['state'] == 3;
                }
            ],
            [
                'text' => '拒绝退款',
                'type' => 'script',
                'level' => 'warning',
                'icon' => 'thumbs-down',
                'value' => '$.showPage',
                'params' => function ($record) {
                    return [
                        'order-instructions-log.refuse',
                        [
                            'order_sub_id' => $record['id'],
                            'type' => 1
                        ],
                    ];
                },
                'show_condition' => function ($record) {
                    return $record['state'] == 3;
                }
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListOperations()
    {
        return [
            [
                'text' => '选定',
                'script' => true,
                'value' => '$.modalRadioValueToInput("radio", "order_id")',
                'icon' => 'flag'
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
                'field' => 'order_number',
                'elem' => 'input',
                'equal' => true
            ],
            'username' => [
                'table' => 'user',
                'elem' => 'input'
            ],
            'check_in_name' => 'input',
            'check_in_phone' => 'input',
            'check_in_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ],
            'conformation_number' => [
                'elem' => 'input',
                'equal' => true
            ],
            'state' => [
                'value' => 'all'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListFilter()
    {
        return [
            'order_number' => [
                'table' => 'order',
                'field' => 'order_number',
                'elem' => 'input',
                'equal' => true
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
                'table' => 'order',
                'field' => 'order_number',
                'code',
                'color' => 'default',
                'tip'
            ],
            'price' => 'code',
            'name' => [
                'title' => '套餐',
                'max-width' => '200px',
                'tip'
            ],
            'conformation_number' => [
                'empty'
            ],
            'check_in_name' => 'empty',
            'check_in_phone' => 'empty',
            'check_in_time' => 'empty',
            'payment_state' => [
                'table' => 'order',
                'code',
                'info',
                'color' => [
                    0 => 'warning',
                    1 => 'success',
                    2 => 'default'
                ]
            ],
            'add_time' => 'tip',
            'update_time' => 'tip',
            'state' => [
                'code',
                'info',
                'color' => [
                    0 => 'info',
                    1 => 'primary',
                    2 => 'primary',
                    3 => 'warning',
                    4 => 'default',
                    5 => 'success'
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
            'order_number' => [
                'table' => 'order',
                'field' => 'order_number',
                'code'
            ],
            'price' => 'code',
            'name' => [
                'title' => '套餐'
            ],
            'check_in_name' => 'empty',
            'check_in_phone' => 'empty',
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
            'check_in_name',
            'check_in_phone',
            'check_in_time' => [
                'type' => 'date'
            ],
            'conformation_number',
            'state' => [
                'elem' => 'select'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function ajaxModalListCondition()
    {
        $condition = self::indexCondition();
        $condition['where'] = [
            ['order_sub.state' => 5]
        ];

        return $condition;
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'join' => [
                ['table' => 'order'],
                ['table' => 'product_package'],
                [
                    'left_table' => 'order',
                    'table' => 'user'
                ]
            ],
            'select' => [
                'product_package.name',
                'order.order_number',
                'order.payment_state',
                'order_sub.*'
            ],
            'order' => 'order_sub.id DESC'
        ];
    }

    /**
     * 选择订单 - 弹出层
     *
     * @auth-pass-all
     */
    public function actionAjaxModalList()
    {
        return $this->showList();
    }

    /**
     * 通过子订单 ID 获取简单版订单情况
     *
     * @access public
     *
     * @param integer $order_sub_id
     *
     * @return array
     */
    public function getOrderBySubId($order_sub_id)
    {
        return $this->service('order.detail', [
            'table' => 'order_sub',
            'join' => [
                ['table' => 'order']
            ],
            'where' => [
                ['order_sub.id' => $order_sub_id],
            ],
            'select' => [
                'order.*',
                'order.price AS total_price',
                'order_sub.*'
            ],
        ]);
    }

    /**
     * 同意预约
     *
     * @access public
     *
     * @param integer $id
     */
    public function actionAgreeOrder($id)
    {
        $result = $this->service('order.agree-order', [
            'order_sub_id' => $id,
            'user_id' => $this->user->id
        ]);

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
        } else {
            Yii::$app->session->setFlash('success', '同意预约操作完成');
        }

        $this->goReference($this->getControllerName());
    }

    /**
     * 拒绝预约
     *
     * @access public
     */
    public function actionRefuseOrder()
    {
        $params = Yii::$app->request->post();
        $result = $this->service('order.refuse-order', [
            'order_sub_id' => $params['order_sub_id'],
            'remark' => $params['remark'],
            'user_id' => $this->user->id
        ]);

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
        } else {
            Yii::$app->session->setFlash('success', '拒绝预约操作完成');
        }

        $this->goReference($this->getControllerName());
    }

    /**
     * 同意退款
     *
     * @access public
     *
     * @param integer $id
     */
    public function actionAgreeRefund($id)
    {
        $order = $this->getOrderBySubId($id);

        $orderNo = $order['order_number'];
        $refundNo = $order['id'] . 'R' . $orderNo;

        // 支付宝
        $success = true;
        if ($order['payment_method']) {
            $price = intval($order['price']) / 100;
            $result = Yii::$app->ali->alipayTradeRefund($orderNo, $refundNo, $price);
            if (is_string($result)) {
                $success = false;
                $info = $result;
            } else {
                $info = '退款申请已经提交';
            }
        } else { // 微信
            try {
                $result = Yii::$app->wx->payment->refund($orderNo, $refundNo, $order['total_price'], $order['price']);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            if (isset($result->err_code_des)) {
                $success = false;
                $info = $result->err_code_des;
            } else {
                $info = '退款申请已经提交';
            }
        }

        $info = ($order['payment_method'] ? '[支付宝反馈] ' : '[微信反馈] ') . $info;
        Yii::info('UID:' . $this->user->id . ' -> ' . $info);

        if (!$success) {
            Yii::$app->session->setFlash('danger', $info);
        } else {
            $result = $this->service('order.agree-refund', [
                'order_sub_id' => $id,
                'user_id' => $this->user->id
            ]);
            if (is_string($result)) {
                Yii::$app->session->setFlash('danger', Yii::t('common', $result));
            } else {
                Yii::$app->session->setFlash('success', $info);
            }
        }

        $this->goReference($this->getControllerName());
    }

    /**
     * 拒绝退款
     *
     * @access public
     */
    public function actionRefuseRefund()
    {
        $params = Yii::$app->request->post();
        $result = $this->service('order.refuse-refund', [
            'order_sub_id' => $params['order_sub_id'],
            'remark' => $params['remark'],
            'user_id' => $this->user->id
        ]);

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
        } else {
            Yii::$app->session->setFlash('success', '拒绝退款操作完成');
        }

        $this->goReference($this->getControllerName());
    }
}
