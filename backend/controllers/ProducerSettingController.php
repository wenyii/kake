<?php

namespace backend\controllers;

use common\components\Helper;
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

    /**
     * @var string 模态框的名称
     */
    public static $ajaxModalListTitle = '选择分销商';

    public static $ajaxModalListRecordFilterValueName = 'producer_id';

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
    public static function ajaxModalListOperations()
    {
        return [
            [
                'text' => '选定',
                'script' => true,
                'value' => '$.modalRadioValueToInput("radio", "producer_id")',
                'icon' => 'flag'
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
                'text' => '分销产品',
                'value' => 'producer-product/index',
                'level' => 'info',
                'icon' => 'link',
                'params' => ['username']
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'username' => [
                'elem' => 'input',
                'table' => 'user'
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
    public static function ajaxModalListFilter()
    {
        return [
            'username' => [
                'elem' => 'input',
                'table' => 'user'
            ],
            'name' => [
                'elem' => 'input'
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
            'username' => [
                'code',
                'table' => 'user'
            ],
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
    public static function ajaxModalListAssist()
    {
        return [
            'username' => [
                'code',
                'table' => 'user'
            ],
            'name',
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

            'spread_url' => [
                'title' => '推广链接',
                'label' => 6,
                'readonly' => true,
                'elem' => 'text'
            ],
            'spread_img' => [
                'title' => '推广二维码',
                'img_label' => 4,
                'elem' => 'img',
                'readonly' => true
            ],
            'state' => [
                'elem' => 'select',
                'value' => 1,
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function addAssist()
    {
        $assist = self::editAssist();
        unset($assist['spread_url'], $assist['spread_img']);

        return $assist;
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
        unset($assist['select_producer'], $assist['spread_url'], $assist['spread_img']);

        return $assist;
    }

    /**
     * 分销商列表 - 弹出
     */
    public function actionAjaxModalList()
    {
        return $this->showList();
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
            $this->actionEditForm($reference, 'edit');
        } else {
            $this->actionAddForm($reference, 'add');
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
                ],
                [
                    'table' => 'user',
                    'left_on_field' => 'producer_id'
                ]
            ],
            'select' => [
                'logo.deep_path AS logo_deep_path',
                'logo.filename AS logo_filename',
                'producer_setting.*',
                'user.username'
            ],
            'order' => 'producer_setting.state DESC, producer_setting.update_time DESC'
        ];
    }

    /**
     * @inheritDoc
     */
    public function ajaxModalListCondition()
    {
        return $this->indexCondition();
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
     * 获取推广信息
     *
     * @access private
     * @param integer $userId
     * @return array
     */
    private function spreadInfo($userId)
    {
        $producer = $this->getProducer($userId);
        if (empty($producer)) {
            return [];
        }

        $channel = Helper::integerEncode($userId);
        $link = Yii::$app->params['frontend_url'] . '/?channel=' . $channel;

        $logo = current($producer['logo_preview_url']);
        $logoPath = Yii::$app->params['tmp_path'] . '/' . basename($logo);
        if (!Helper::saveRemoteFile($logo, $logoPath)) {
            return [$link, null];
        }
        $qr = $this->createQrCode($link, 200, $logoPath);

        return [$link, $qr->writeDataUri()];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        // 生成封面图附件地址
        $record = $this->createAttachmentUrl($record, ['logo_attachment_id' => 'logo']);

        if ($action == 'edit') {
            $spread = $this->spreadInfo($record['producer_id']);
            if (!empty($spread)) {
                list($record['spread_url'], $record['spread_img']) = $spread;
                $record['spread_img'] = ['qr' => $record['spread_img']];
            }
        }
        return parent::sufHandleField($record, $action);
    }

    /**
     * 生成推广链接
     *
     * @auth-pass-all
     */
    public function actionSpread()
    {
        $spread = $this->spreadInfo($this->user->id);
        if (empty($spread[1])) {
            Yii::$app->session->setFlash('warning', '请先完善个人设置');

            return $this->redirect(['producer-setting/center']);
        }

        list($link, $img) = $spread;

        return $this->display('spread', compact('link', 'img'));
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
