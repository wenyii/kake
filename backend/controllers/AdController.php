<?php

namespace backend\controllers;

/**
 * 广告管理
 *
 * @auth-inherit-except front
 */
class AdController extends GeneralController
{
    // 模型
    public static $modelName = 'Ad';

    // 模型描述
    public static $modelInfo = '广告';

    /**
     * @var array Hook
     */
    public static $hookDateSectionDouble = [''];

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增广告',
                'value' => 'ad/add',
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
            'target' => [
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
            'target' => [
                'code',
                'info'
            ],
            'link_url' => 'link',
            'remark',
            'from',
            'to',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ],
            'preview_url' => [
                'img',
                'width' => '200px',
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function editAssist($action = null)
    {
        return [
            'target' => [
                'elem' => 'select',
                'value' => 0,
                'tip' => [
                    '_self' => '当前窗口打开',
                    '_blank' => '新窗口打开',
                ],
            ],
            'url' => [
                'label' => 4,
                'tip' => [
                    '格式1' => 'site/index 表示网站域名 + ?r=site/index',
                    '格式2' => '以 http(s):// 开头的完整地址串'
                ],
            ],
            'remark' => [
                'elem' => 'textarea',
                'placeholder' => '128个字以内'
            ],
            'from' => [
                'type' => 'datetime-local',
                'label' => 3,
                'tip' => [
                    'AM' => '上午',
                    'PM' => '下午'
                ]
            ],
            'to' => [
                'type' => 'datetime-local',
                'label' => 3,
                'tip' => [
                    'AM' => '上午',
                    'PM' => '下午'
                ]
            ],

            'attachment_id' => [
                'hidden' => true
            ],
            'old_attachment_id' => [
                'value_field' => 'attachment_id',
                'hidden' => true
            ],
            'preview_url' => [
                'elem' => 'img',
                'img_label' => 4,
                'upload_key' => 'upload'
            ],
            'upload' => [
                'type' => 'file',
                'tag' => 1,
                'rules' => [
                    'suffix' => 'jpg,jpeg,png',
                    'pic_sizes' => '750*160',
                    'max_size' => 128
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
                'attachment.deep_path',
                'attachment.filename',
                'ad.*'
            ],
            'order' => [
                'ad.state DESC',
                'ad.update_time DESC'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function editCondition()
    {
        return [
            'join' => [
                ['table' => 'attachment']
            ],
            'select' => [
                'attachment.deep_path',
                'attachment.filename',
                'ad.*'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        $record = $this->createAttachmentUrl($record, 'attachment_id');
        $record = $this->createLinkUrl($record, 'url');

        return parent::sufHandleField($record, $action);
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        $this->sourceJs = [
            'jquery.ajaxupload'
        ];

        return parent::beforeAction($action);
    }
}
