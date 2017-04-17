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
    public static $hookPriceNumber = ['price'];

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
                'params' => function($record) {
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
            'info' => 'input',
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
            'product_id' => 'code',
            'link_url' => [
                'link',
                'url_info' => 'Preview'
            ],
            'name',
            'price' => 'code',
            'sale_price' => [
                'title' => '折后价格',
                'code'
            ],
            'info',
            'state' => [
                'code',
                'color' => 'auto',
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
                'script' => '$.showPage("product.list")'
            ],
            'name',
            'price',
            'info' => [
                'elem' => 'textarea',
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
                'label' => 5
            ],
            'price' => [
                'label' => 5
            ],
            'info' => [
                'elem' => 'textarea',
                'label' => 8,
                'placeholder' => '256个字以内'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        $record = $this->createLinkUrl($record, 'product_id', function ($id) {
            return [
                'detail/index',
                'id' => $id
            ];
        });

        return parent::sufHandleField($record, $action, function ($record) {
            if (!empty($record['id']) && !empty($record['sale_rate'])) {

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
                        $sale_price = $price - ($price * ($rate / 100)) / 100;
                        break;
                }

                if ($sale_price > 0) {
                    $record['sale_price'] = $sale_price;
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
        $this->showForm('ajax-modal-package');
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
                'product.sale_type',
                'product.sale_rate',
                'product.sale_from',
                'product.sale_to',
                'product_package.*'
            ],
        ];
    }
}
