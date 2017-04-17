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
            [
                'text' => '主订单',
                'value' => 'order/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => ['order_number']
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
                'color' => 'default'
            ],
            'price' => 'code',
            'name' => [
                'title' => '套餐'
            ],
            'conformation_number' => [
                'empty'
            ],
            'check_in_name' => 'empty',
            'check_in_phone' => 'empty',
            'check_in_time' => 'empty',
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
            ],
            'select' => [
                'product_package.name',
                'order.order_number',
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
        $result = $this->service('order.agree-refund', [
            'order_sub_id' => $id,
            'user_id' => $this->user->id
        ]);

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
        } else {

            $info = null;
            $order = $this->getOrderBySubId($id);

            // 微信退款
            if ($order['payment_method'] == 0) {
                $orderNo = $order['order_number'];
                $refundNo = $order['id'] . 'R' . $orderNo;
                $result = Yii::$app->wx->payment->refund($orderNo, $refundNo, $order['total_price'], $order['price']);
                $info = isset($result->err_code_des) ? $result->err_code_des : '退款申请已经提交';
            }

            $info && $info = ' (' . $info . ')';
            Yii::$app->session->setFlash('success', '同意退款操作完成' . $info);
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
