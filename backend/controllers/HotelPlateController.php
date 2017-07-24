<?php

namespace backend\controllers;

/**
 * 酒店板块管理
 *
 * @auth-inherit-except front
 */
class HotelPlateController extends GeneralController
{
    // 模型
    public static $modelName = 'HotelPlate';

    // 模型描述
    public static $modelInfo = '酒店板块';

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增酒店板块',
                'value' => 'hotel-plate/add',
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
                'max-width' => '350px'
            ],
            'preview_url' => [
                'title' => '板块封面图',
                'img',
                'width' => '200px'
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
                'placeholder' => '32个字以内'
            ],

            'attachment_id' => [
                'hidden' => true
            ],
            'old_attachment_id' => [
                'value_key' => 'attachment_id',
                'hidden' => true
            ],
            'preview_url' => [
                'title' => '板块封面图',
                'elem' => 'img',
                'img_label' => 4,
                'upload_name' => 'upload'
            ],
            'upload' => [
                'type' => 'file',
                'tag' => 1,
                'rules' => [
                    'suffix' => 'jpg,jpeg,png',
                    'pic_sizes' => '188*380', // 336*234
                    'max_size' => 512
                ],
                'preview_name' => 'preview_url',
                'field_name' => 'attachment_id'
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
                ['table' => 'attachment']
            ],
            'select' => [
                'hotel_plate.*',
                'attachment.deep_path AS deep_path',
                'attachment.filename AS filename'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function editCondition()
    {
        return self::indexCondition();
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        // 生成封面图附件地址
        $record = $this->createAttachmentUrl($record, ['attachment_id']);

        return parent::sufHandleField($record, $action);
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        $this->sourceJs = [
            'jquery.ajaxupload',
            'jquery.cropper'
        ];
        $this->sourceCss = ['cropper'];

        return parent::beforeAction($action);
    }
}
