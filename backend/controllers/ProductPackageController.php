<?php

namespace backend\controllers;

/**
 * 酒店产品套餐管理
 *
 * @auth-inherit-except front
 */
class ProductPackageController extends GeneralController
{
    // 模型
    public static $modelName = 'ProductPackage';

    // 模型描述
    public static $modelInfo = '酒店产品套餐';

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = [
        'base_price',
        'price'
    ];

    public static $_status;

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增套餐',
                'value' => 'product-package/add',
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
                'text' => '所属产品',
                'value' => 'product/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => function ($record) {
                    return ['id' => $record['product_id']];
                }
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function pageDocument()
    {
        return [
            'ajax-modal-package' => [
                'modal' => true,
                'title_info' => '添加套餐',
                'button_info' => '添加套餐',
                'action' => <<<EOF
$.handleModalForm($(this), $.package, {
    fn: $.createTag,
    params: {
        containerName: "package",
        fieldName: "package_ids",
        fieldNameNew: "new_package_ids"
    }
});
EOF
            ],
        ];
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
            'name' => 'input',
            'title' => [
                'title' => '产品',
                'elem' => 'input',
                'table' => 'product'
            ],
            'hotel_name' => [
                'title' => '酒店',
                'elem' => 'input',
                'table' => 'hotel',
                'field' => 'name'
            ],
            'info' => 'input',
            'bidding' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'state' => [
                'value' => 1
            ],
            'status' => [
                'title' => '产品状态',
                'table' => 'product',
                'field' => 'state',
                'value' => 1
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexSorter()
    {
        return [
            'product_id',
            'price'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'product_id' => 'code',
            'name' => [
                'max-width' => '120px'
            ],
            'base_price' => [
                'code',
                'tip'
            ],
            'title' => [
                'title' => '产品',
                'tip'
            ],
            'hotel_name' => [
                'title' => '酒店',
                'tip'
            ],
            'price' => 'code',
            'sale_price' => [
                'title' => '折后价格',
                'code'
            ],
            'bidding' => [
                'code',
                'color' => [
                    0 => 'default',
                    1 => 'success'
                ],
                'info'
            ],
            'purchase_limit' => [
                'code',
                'empty',
                'not_set_info' => '<span class="not-set">+∞</span>'
            ],
            'info' => [
                'max-width' => '250px',
                'tpl' => '<pre>%s</pre>'
            ],
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ],
            'status' => [
                'title' => '产品状态',
                'code',
                'color' => 'auto',
                'tip',
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
            'product_id' => [
                'readonly' => true,
                'same_row' => true
            ],
            'select_product' => [
                'title' => false,
                'elem' => 'button',
                'value' => '选择酒店产品',
                'script' => '$.showPage("product.list", {state: 1})'
            ],
            'name' => [
                'placeholder' => '32个字以内'
            ],
            'base_price' => [
                'placeholder' => '保留到小数点后两位'
            ],
            'price' => [
                'placeholder' => '保留到小数点后两位'
            ],
            'bidding' => [
                'elem' => 'select',
                'tip' => '是否参与最低价格显示',
                'value' => 1
            ],
            'purchase_limit' => [
                'placeholder' => '0表示不限制',
                'tip' => '以用户为单位进行限购',
                'value' => \Yii::$app->params['default_purchase_limit']
            ],
            'info' => [
                'elem' => 'textarea',
                'row' => 8,
                'placeholder' => '256个字以内'
            ],
            'state' => [
                'elem' => 'select',
                'value' => 1
            ]
        ];
    }

    /**
     * 套餐弹出层
     */
    public static function ajaxModalPackageAssist()
    {
        return [
            'name' => [
                'label' => 5,
                'placeholder' => '32个字以内'
            ],
            'base_price' => [
                'label' => 5,
                'placeholder' => '保留到小数点后两位'
            ],
            'price' => [
                'label' => 5,
                'placeholder' => '保留到小数点后两位'
            ],
            'bidding' => [
                'elem' => 'select',
                'tip' => '是否参与最低价格显示',
                'value' => 1
            ],
            'purchase_limit' => [
                'placeholder' => '0表示不限制',
                'tip' => '以用户为单位进行限购',
                'value' => \Yii::$app->params['default_purchase_limit']
            ],
            'info' => [
                'elem' => 'textarea',
                'label' => 8,
                'row' => 8,
                'placeholder' => '256个字以内'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        return parent::sufHandleField($record, $action, function ($record) {

            if (empty($record['id'])) {
                return $record;
            }

            $model = parent::model(self::$modelName);
            self::$_status = $model->_state;
            $record = $this->getFieldInfo($record, 'status');

            if (!empty($record['sale_rate'])) {

                $controller = $this->controller('product');
                $record['sale'] = $this->callMethod('saleLogic', 0, [$record], $controller);

                if (!$record['sale']) {
                    return $record;
                }

                $price = $record['price'];
                $rate = $record['sale_rate'];
                $sale_price = 0;

                switch ($record['sale_type']) {
                    case '1' :
                        $sale_price = intval($price - $rate) / 100;
                        break;

                    case '2' :
                        $sale_price = ($price - $price * ($rate / 100 / 100)) / 100;
                        break;
                }

                if ($sale_price > 0) {
                    $record['sale_price'] = $sale_price;
                } else {
                    $record['sale_price'] = $record['price'] / 100;
                }
            }

            return $record;
        });
    }

    /**
     * 填写套餐 - 弹出层
     *
     * @auth-pass-all
     */
    public function actionAjaxModalPackage()
    {
        $this->showForm();
    }

    /**
     * @inheritDoc
     */
    public function indexCondition($as = null)
    {
        return array_merge(parent::indexCondition(), [
            'join' => [
                ['table' => 'product'],
                [
                    'left_table' => 'product',
                    'table' => 'hotel'
                ]
            ],
            'select' => [
                'product.title',
                'product.sale_type',
                'product.sale_rate',
                'product.sale_from',
                'product.sale_to',
                'product.state AS status',
                'hotel.name AS hotel_name',
                'product_package.*'
            ]
        ]);
    }
}
