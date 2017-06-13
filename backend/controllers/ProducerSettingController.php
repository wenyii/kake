<?php

namespace backend\controllers;

use Yii;

/**
 * 分销商设置
 *
 * @auth-inherit-except front
 */
class ProducerSettingController extends GeneralController
{
    // 模型
    public static $modelName = 'ProducerSetting';

    // 模型描述
    public static $modelInfo = '分销商';

    public static $uid;

    /**
     * @inheritDoc
     */
    public function pageDocument()
    {
        return [
            'center' => [
                'title_icon' => 'edit',
                'title_info' => '编辑',
                'button_info' => '编辑',
                'action' => 'setting'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增分销商',
                'value' => 'producer-setting/add',
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
            'producer_id' => [
                'elem' => 'input',
                'equal' => true
            ],
            'name' => [
                'elem' => 'input'
            ],
            'theme' => [
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
            'producer_id' => 'code',
            'name',
            'theme' => [
                'info',
                'code'
            ],
            'logo_preview_url' => [
                'title' => 'LOGO预览',
                'img',
                'width' => '128px'
            ],
            'add_time',
            'update_time',
            'state' => [
                'code',
                'info',
                'color' => [
                    0 => 'danger',
                    1 => 'info',
                    2 => 'default'
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
                'readonly' => true,
                'same_row' => true
            ],
            'select_producer' => [
                'title' => false,
                'elem' => 'button',
                'value' => '选择用户',
                'script' => '$.showPage("user.list", {role: 10, state: 1})'
            ],
            'name' => [
                'placeholder' => '32个字符以内'
            ],
            'theme' => [
                'elem' => 'select',
                'value' => 1
            ],

            'logo_attachment_id' => [
                'hidden' => true
            ],
            'old_logo_attachment_id' => [
                'value_field' => 'logo_attachment_id',
                'hidden' => true
            ],
            'logo_preview_url' => [
                'img_label' => 2,
                'title' => 'LOGO预览',
                'elem' => 'img',
                'upload_key' => 'upload_logo'
            ],
            'upload_logo' => [
                'title' => '',
                'type' => 'file',
                'tag' => 1,
                'rules' => [
                    'suffix' => 'jpg,jpeg,png',
                    'pic_sizes' => '128*128',
                    'max_size' => 512
                ],
                'preview_name' => 'logo_preview_url',
                'field_name' => 'logo_attachment_id'
            ],

            'state' => [
                'elem' => 'select',
                'value' => 1
            ]
        ];
    }

    /**
     * 分销商设置辅助编辑
     */
    public static function centerAssist()
    {
        $assist = self::editAssist();
        $assist['producer_id'] = [
            'value' => self::$uid,
            'hidden' => true
        ];
        $assist['id'] = [
            'hidden' => true
        ];
        unset($assist['select_producer']);

        return $assist;
    }

    /**
     * 分销商设置 - 渲染
     *
     * @auth-pass-all
     */
    public function actionCenter()
    {
        self::$uid = $this->user->id;
        $this->logReference($this->getControllerName('center'));

        return $this->showFormWithRecord([
            ['producer_id' => self::$uid]
        ]);
    }

    /**
     * 分销商设置
     *
     * @auth-pass-all
     */
    public function actionSetting()
    {
        $reference = $this->getControllerName('center');

        if (Yii::$app->request->post('id')) {
            $this->actionEditForm($reference);
        } else {
            $this->actionAddForm($reference);
        }
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'join' => [
                [
                    'table' => 'attachment',
                    'as' => 'logo',
                    'left_on_field' => 'logo_attachment_id'
                ]
            ],
            'select' => [
                'logo.deep_path AS logo_deep_path',
                'logo.filename AS logo_filename',
                'producer_setting.*'
            ],
            'order' => 'producer_setting.state DESC, producer_setting.update_time DESC'
        ];
    }

    /**
     * @inheritDoc
     */
    public function editCondition()
    {
        $condition = $this->indexCondition();
        unset($condition['order']);

        return $condition;
    }

    /**
     * 分销商设置查询辅助
     */
    public function centerCondition()
    {
        return $this->editCondition();
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        // 生成封面图附件地址
        $record = $this->createAttachmentUrl($record, ['logo_attachment_id' => 'logo']);

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
}
