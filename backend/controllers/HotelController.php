<?php

namespace backend\controllers;

/**
 * 酒店管理
 *
 * @auth-inherit-except front
 */
class HotelController extends GeneralController
{
    // 模型
    public static $modelName = 'Hotel';

    // 模型描述
    public static $modelInfo = '酒店';

    /**
     * @var string 模态框的名称
     */
    public static $ajaxModalListTitle = '选择酒店';

    /**
     * @inheritDoc
     */
    public static function ajaxModalListOperations()
    {
        return [
            [
                'text' => '选定',
                'script' => true,
                'value' => '$.modalRadioValueToInput("radio", "hotel_id")',
                'icon' => 'flag'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增酒店',
                'value' => 'hotel/add',
                'icon' => 'plus'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'id' => [
                'elem' => 'input',
                'equal' => true
            ],
            'name' => 'input',
            'hotel_region_id' => [
                'list_table' => 'hotel_region',
                'list_value' => 'name',
                'value' => parent::SELECT_KEY_ALL
            ],
            'principal' => 'input',
            'address' => 'input',
            'state' => [
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListFilter()
    {
        return [
            'name' => 'input',
            'hotel_region_id' => [
                'list_table' => 'hotel_region',
                'list_value' => 'name',
                'value' => parent::SELECT_KEY_ALL,
            ],
            'principal' => 'input',
            'address' => 'input',
            'state' => [
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexSorter()
    {
        return [
            'id',
            'name'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'id' => 'code',
            'name' => [
                'max-width' => '250px'
            ],
            'hotel_region_id' => [
                'list_table' => 'hotel_region',
                'list_value' => 'name',
                'info',
                'code'
            ],
            'principal',
            'contact',
            'address' => [
                'title' => '地址',
                'max-width' => '400px'
            ],
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
    public static function ajaxModalListAssist()
    {
        return [
            'name',
            'hotel_region_id' => [
                'list_table' => 'hotel_region',
                'list_value' => 'name',
                'info',
                'code'
            ],
            'address' => [
                'max-width' => '400px'
            ],
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
            'name' => [
                'placeholder' => '64个字以内'
            ],
            'hotel_region_id' => [
                'list_table' => 'hotel_region',
                'list_value' => 'name',
                'elem' => 'select'
            ],
            'principal' => [
                'placeholder' => '32个字以内'
            ],
            'contact',
            'address' => [
                'label' => 5,
                'placeholder' => '64个字以内'
            ],
            'state' => [
                'elem' => 'select',
                'value' => 1
            ]
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
}
