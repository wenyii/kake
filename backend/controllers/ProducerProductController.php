<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 分销产品管理
 */
class ProducerProductController extends GeneralController
{
    // 模型
    public static $modelName = 'ProducerProduct';

    // 模型描述
    public static $modelInfo = '分销产品';

    public static $uid;

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增分销产品',
                'value' => 'producer-product/add',
                'icon' => 'plus'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return array_merge(parent::indexOperation(), [
            [
                'text' => '前置',
                'value' => 'front',
                'level' => 'info',
                'icon' => 'sort'
            ],
            [
                'text' => '二维码',
                'type' => 'script',
                'value' => '$.showQrCode',
                'params' => ['link_url'],
                'level' => 'success',
                'icon' => 'qrcode'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'product_id' => [
                'elem' => 'input',
                'equal' => true
            ],
            'type' => [
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
            'title' => [
                'title' => '产品'
            ],
            'type' => [
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
            'add_time' => 'tip',
            'update_time' => 'tip',
            'state' => [
                'code',
                'info',
                'color' => [
                    0 => 'danger',
                    1 => 'info',
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function editAssist($action = null)
    {
        return [
            'producer_id' => [
                'hidden' => true,
                'value' => self::$uid
            ],
            'product_id' => [
                'readonly' => true,
                'same_row' => true,
                'label' => 2
            ],
            'select_product' => [
                'title' => false,
                'elem' => 'button',
                'value' => '选择产品',
                'script' => '$.showPage("product.list-producer", {state: 1})'
            ],
            'type' => [
                'elem' => 'select',
                'value' => 1
            ],
            'state' => [
                'elem' => 'select',
                'value' => 1
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
                ['table' => 'product']
            ],
            'select' => [
                'product.title',
                'producer_product.*'
            ],
            'where' => [['producer_id' => self::$uid]],
            'order' => 'producer_product.state DESC, producer_product.update_time DESC'
        ];
    }

    /**
     * @inheritDoc
     */
    public function preHandleField($record, $action = null)
    {
        if (in_array($action, [
            'add',
            'edit'
        ])) {
            $controller = $this->controller('product');
            $data = $this->callMethod('sufHandleField', [], [
                ['id' => $record['product_id']],
                'ajaxModalListProducer'
            ], $controller);

            if (empty($data['commission_' . ProductController::$type[$record['type']]])) {
                Yii::$app->session->setFlash('warning', '该产品没有设置该分佣类型');
                Yii::$app->session->setFlash('list', $record);
                $this->goReference('producer-product/' . $action);
            }
        }

        return parent::preHandleField($record, $action);
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if ($action == 'index') {
            $record = $this->createLinkUrl($record, 'product_id', function ($id) {
                return [
                    'detail/index',
                    'id' => $id
                ];
            });
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
