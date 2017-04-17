<?php

namespace backend\controllers;

use common\components\Helper;
use common\models\Main;
use Yii;

/**
 * 酒店产品管理
 */
class ProductController extends GeneralController
{
    /**
     * @var string 所有操作对应的模型
     */
    public static $modelName = 'Product';

    /**
     * @var string 模型描述
     */
    public static $modelInfo = '酒店产品';

    /**
     * @var string 添加操作使用到的 api
     */
    public static $addApiName = 'product.add-product';

    /**
     * @var string 编辑操作使用到的 api
     */
    public static $editApiName = 'product.edit-product';

    /**
     * @var string 模态框的名称
     */
    public static $ajaxModalListTitle = '选择酒店产品';

    /**
     * @var array Hook
     */
    public static $hookUbbAndHtml = [
        'enjoy',
        'characteristic',
        'use'
    ];

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = [
        'sale_rate',
        'sale_price'
    ];

    /**
     * @var array Hook
     */
    public static $hookDateSectionDouble = ['sale'];

    /**
     * @var array Hook
     */
    public static $hookLogic = ['sale'];

    /**
     * @var array Field
     */
    public static $_sale = [
        1 => '是'
    ];

    /**
     * 是否打折逻辑
     *
     * @param array $record
     *
     * @return boolean
     */
    public static function saleLogic($record)
    {
        $from = strtotime($record['sale_from']);
        $to = strtotime($record['sale_to']);

        return !empty($record['sale_rate']) && $from < TIME && $to > TIME;
    }

    /**
     * 是否打折反向逻辑
     *
     * @param integer $index
     *
     * @return array
     */
    public static function saleReverseWhereLogic($index)
    {
        $indexes = [
            1 => [
                [
                    '>',
                    'product.sale_rate',
                    0
                ],
                [
                    '<',
                    'product.sale_from',
                    date('Y-m-d H:i:s', TIME)
                ],
                [
                    '>',
                    'product.sale_to',
                    date('Y-m-d H:i:s', TIME)
                ]
            ]
        ];

        return isset($indexes[$index]) ? $indexes[$index] : [];
    }

    /**
     * 宏操作
     *
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增酒店产品',
                'value' => 'product/add',
                'icon' => 'plus'
            ]
        ];
    }

    /**
     * 宏操作
     *
     * @inheritDoc
     */
    public static function ajaxModalListOperations()
    {
        return [
            [
                'text' => '选定',
                'script' => true,
                'value' => '$.modalRadioValueToInput("radio", "product_id")',
                'icon' => 'flag'
            ]
        ];
    }

    /**
     * 微操作
     *
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
                'text' => '套餐',
                'value' => 'product-package/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => function($record) {
                    return ['product_id' => $record['id']];
                }
            ],
        ]);
    }

    /**
     * 筛选器
     *
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'id' => [
                'elem' => 'input',
                'equal' => true
            ],
            'title' => 'input',
            'destination' => 'input',
            'hotel_name' => [
                'elem' => 'input',
                'table' => 'hotel',
                'field' => 'name',
                'title' => '酒店名称'
            ],
            'classify' => [
                'value' => 'all'
            ],
            'sale_type' => [
                'value' => 'all',
            ],
            'sale' => [
                'title' => '打折中',
                'value' => 'all'
            ],
            'stock' => [
                'elem' => 'input',
                'equal' => true
            ],
            'min_night' => [
                'elem' => 'input',
                'equal' => true
            ],
            'manifestation' => [
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
    public static function ajaxModalListFilter()
    {
        return [
            'title' => 'input',
            'hotel_name' => [
                'elem' => 'input',
                'table' => 'hotel',
                'field' => 'name',
                'title' => '酒店名称'
            ],
            'classify' => [
                'value' => 'all'
            ],
            'state' => [
                'value' => 'all'
            ]
        ];
    }

    /**
     * 生成列表页的辅助数据
     *
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'id' => [
                'code',
                'color' => 'default'
            ],
            'title',
            'destination',
            'hotel_name' => [
                'table' => 'hotel',
                'field' => 'name',
                'title' => '酒店名称',
                'tip'
            ],
            'classify' => [
                'code',
                'info'
            ],
            'sale' => [
                'title' => '打折中',
                'info',
                'empty',
                'code',
                'color' => 'success'
            ],
            'top' => [
                'code',
                'empty',
                'color' => [
                    0 => 'default',
                    1 => 'primary'
                ],
                'info'
            ],
            'stock',
            'min_night' => 'tip',
            'manifestation' => [
                'code',
                'info'
            ],
            'purchase_times',
            'share_times',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ]
        ];
    }

    /**
     * 生成列表页的辅助数据
     *
     * @inheritDoc
     */
    public static function ajaxModalListAssist()
    {
        return [
            'title',
            'destination',
            'classify' => [
                'code',
                'info'
            ],
            'purchase_times' => 'code',
            'share_times' => 'code',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ]
        ];
    }

    /**
     * 生成编辑表单的辅助数据
     *
     * @inheritDoc
     */
    public static function editAssist($action = null)
    {
        $description = (new Main('ProductDescription'))->attributeLabels();

        return [
            'title' => [
                'placeholder' => '64个字以内'
            ],
            'info' => [
                'elem' => 'textarea',
                'placeholder' => '256个字以内'
            ],
            'destination' => [
                'placeholder' => '32个字以内'
            ],
            'hotel_id' => [
                'readonly' => true,
                'same_row' => true,
                'table' => 'hotel'
            ],
            'select_hotel' => [
                'title' => false,
                'elem' => 'button',
                'value' => '选择酒店',
                'script' => '$.showPage("hotel.list")'
            ],
            'classify' => [
                'elem' => 'select',
                'value' => 4
            ],
            'sale_type' => [
                'elem' => 'select',
                'same_row' => true
            ],
            'sale_rate' => [
                'title' => false,
                'placeholder' => '填写后将以折后价格售卖',
                'tip' => [
                    '此处填写的是折扣掉的值',
                    '',
                    '<span class=text-danger>打折效果对所有套餐生效</span>',
                    '固定折扣价' => '按实际需要折扣的金额额度直接填写',
                    '百分比折扣' => '如需打 85 折则填写 15 (100-85)',
                ]
            ],
            'sale_from' => [
                'type' => 'datetime-local',
                'label' => 3,
                'tip' => [
                    '`折扣值` 字段填写后有效',
                    '',
                    'AM' => '上午',
                    'PM' => '下午'
                ]
            ],
            'sale_to' => [
                'type' => 'datetime-local',
                'label' => 3,
                'tip' => [
                    '必须晚于开始时间',
                    '',
                    'AM' => '上午',
                    'PM' => '下午'
                ]
            ],

            'package_ids' => [
                'hidden' => true
            ],
            'old_package_ids' => [
                'value_field' => 'package_ids',
                'hidden' => true
            ],
            // format 将指定的字段按该格式替换掉并返回给 JS 处理
            // table 写入到该表
            // foreign_key 对应当前表的外键字段
            // handler_controller 对应处理数据的模型
            'package' => [
                'title' => '套餐',
                'elem' => 'tag',
                'label' => 10,
                'format' => '{name} (¥{price})',
                'field_name' => 'package_ids',
                'table' => 'product_package',
                'foreign_key' => 'product_id',
                'service_api' => 'product.package-list'
            ],
            'add_package' => [
                'title' => '',
                'elem' => 'button',
                'value' => '添加酒店套餐',
                'script' => '$.showPage("product-package.package")'
            ],

            'top' => [
                'elem' => 'select',
                'value' => 0,
            ],
            'stock' => [
                'value' => 0,
            ],
            'min_night' => [
                'value' => 1,
            ],
            'manifestation' => [
                'elem' => 'select',
                'value' => 0
            ],

            'attachment_cover' => [
                'hidden' => true
            ],
            'old_attachment_cover' => [
                'value_field' => 'attachment_cover',
                'hidden' => true
            ],
            'cover_preview_url' => [
                'title' => '封面图预览',
                'elem' => 'img',
                'upload_key' => 'upload_cover'
            ],
            'upload_cover' => [
                'title' => '',
                'type' => 'file',
                'tag' => 1,
                'rules' => [
                    'suffix' => 'jpg,jpeg,png',
                    'pic_sizes' => '750*500',
                    'max_size' => 256
                ],
                'preview_name' => 'cover_preview_url',
                'field_name' => 'attachment_cover'
            ],

            // < 存储当前附件 item >
            'attachment_ids' => [
                'hidden' => true
            ],
            // < 存储旧时附件 item >
            'old_attachment_ids' => [
                'value_field' => 'attachment_ids',
                'hidden' => true
            ],
            // < 附件预览 item >
            // upload_key 标示指向要绑定的 < 上传附件 item > 的 key 名
            'slave_preview_url' => [
                'title' => '次要图预览',
                'elem' => 'img',
                'upload_key' => 'upload_slave'
            ],
            // < 上传附件 item >
            // rules 标示上传附件的规范 (实为 common\components\Upload 组件的参数)
            // tag 标记 (用于 common\components\Upload 组件寻找 rules 所设定), 单控制器不重复出现
            // preview_name < 附件预览 item > 的 name 值
            // field_name < 存储当前附件 item > 的 name 值
            // multiple 是否支持多附件
            // cover 是否需要设定封面图, 在 multiple 为 true 时有效
            // cover_name 封面附件存储 item 的 name 值
            'upload_slave' => [
                'title' => '',
                'type' => 'file',
                'tag' => 1,
                'rules' => [
                    'suffix' => 'jpg,jpeg,png',
                    'pic_sizes' => '750*500',
                    'max_size' => 256
                ],
                'preview_name' => 'slave_preview_url',
                'field_name' => 'attachment_ids',
                'multiple' => true
            ],

            'enjoy' => [
                'elem' => 'ckeditor',
                'row' => 6,
                'title' => $description['enjoy'],
                'placement' => 'left',
                'tip' => '必须填写',
                'width' => 414
            ],
            'characteristic' => [
                'elem' => 'ckeditor',
                'row' => 6,
                'title' => $description['characteristic'],
                'placement' => 'left',
                'tip' => '必须填写',
                'width' => 414,
                'height' => 500
            ],
            'use' => [
                'elem' => 'ckeditor',
                'row' => 6,
                'title' => $description['use'],
                'placement' => 'left',
                'tip' => '必须填写',
                'width' => 414
            ],

            'state' => [
                'elem' => 'select',
                'value' => 1
            ]
        ];
    }

    /**
     * 列表页面的查询构建器
     *
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'join' => [
                [
                    'table' => 'attachment',
                    'as' => 'master',
                    'left_on_field' => 'attachment_cover'
                ],
                [
                    'table' => 'hotel',
                    'field' => 'name'
                ]
            ],
            'select' => [
                'master.deep_path AS master_deep_path',
                'master.filename AS master_filename',
                'hotel.name AS hotel_name',
                'product.*'
            ],
            'order' => 'product.top DESC, product.state DESC, product.update_time DESC'
        ];
    }

    /**
     * 编辑页面的查询构建器
     *
     * @inheritDoc
     */
    public function editCondition()
    {
        return [
            'join' => [
                [
                    'table' => 'attachment',
                    'as' => 'cover',
                    'left_on_field' => 'attachment_cover',
                ],
                ['table' => 'product_description'],
                [
                    'table' => 'hotel',
                    'field' => 'name'
                ]
            ],
            'select' => [
                'cover.deep_path AS cover_deep_path',
                'cover.filename AS cover_filename',
                'hotel.name AS hotel_name',
                'product_description.*',
                'product.*'
            ],
        ];
    }

    /**
     * 选择酒店 - 弹出层
     *
     * @auth-pass-all
     */
    public function actionAjaxModalList()
    {
        return $this->showList();
    }

    /**
     * 数据写入前的钩子
     *
     * @inheritDoc
     */
    public function preHandleField($record, $action = null)
    {
        if (!empty($record['sale_rate'])) {
            if ($record['sale_type'] == 2 && ($record['sale_rate'] < 1 || $record['sale_rate'] > 99)) {
                Yii::$app->session->setFlash('warning', '百分比折扣时折扣率请填写 1 ~ 99 之间的数');
                $this->goReference('product/edit');
            }
        } else {
            $record['sale_rate'] = 0;
        }

        if ($action === 'edit' && empty($record['package_ids']) && empty($record['new_package_ids'])) {
            Yii::$app->session->setFlash('warning', '酒店产品至少设定一个套餐');
            $this->goReference('product/edit');
        }

        // TODO
        // 防止附件数据混乱, 匹配 img 标签中的 attachment-id="\d+" 属性

        return parent::preHandleField($record, $action);
    }

    /**
     * 数据展示前的钩子
     *
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        // 生成封面图附件地址
        $record = $this->createAttachmentUrl($record, ['attachment_cover' => 'cover']);

        if (in_array($action, [
            'edit',
            'detail'
        ])) {
            // 生成其他图附件地址
            $record = $this->createAttachmentUrls($record, ['attachment_ids' => 'slave']);
            // 获取套餐数据
            $record = $this->listForeignData($record, 'package');
        }

        // TODO
        // 防止附件数据混乱, 匹配 img 标签中的 attachment-id="\d+" 属性

        return parent::sufHandleField($record, $action);
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        $this->sourceJs = [
            'jquery.ajaxupload',
            'ckeditor/ckeditor'
        ];

        return parent::beforeAction($action);
    }

    /**
     * 填写酒店 - 弹出层
     *
     * @auth-pass-all
     */
    public function actionAjaxModalHotel()
    {
        $this->showForm('ajax-modal-hotel');
    }
}