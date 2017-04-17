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
            'principal' => 'input',
            'address' => 'input',
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
            'name' => 'input',
            'principal' => 'input',
            'address' => 'input',
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
            'name',
            'principal',
            'contact',
            'address' => [
                'title' => '地址'
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
            'address',
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
            'name',
            'principal',
            'contact',
            'address' => [
                'label' => 5
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
