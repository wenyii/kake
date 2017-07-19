<?php

namespace backend\controllers;

use common\models\Main;
use Yii;
use yii\data\Pagination;
use yii\helpers\Html;

/**
 * 酒店故事管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class ActivityStoryController extends GeneralController
{
    // 模型
    public static $modelName = 'ActivityStory';

    // 模型描述
    public static $modelInfo = '酒店故事';

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'username' => [
                'table' => 'user',
                'elem' => 'input'
            ],
            'add_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
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
                'table' => 'user'
            ],
            'photo_preview_url' => [
                'title' => '照片',
                'img',
                'width' => '200px'
            ],
            'story' => [
                'max-width' => '240px'
            ],
            'add_time',
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
    public static function indexSorter()
    {
        return [
            'add_time'
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'join' => [
                ['table' => 'user'],
                [
                    'table' => 'attachment',
                    'as' => 'photo',
                    'left_on_field' => 'photo_attachment_id'
                ]
            ],
            'select' => [
                'activity_story.*',
                'photo.deep_path AS photo_deep_path',
                'photo.filename AS photo_filename',
                'user.username'
            ],
            'where' => [
                ['activity_story.state' => 1]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        // 生成封面图附件地址
        $record = $this->createAttachmentUrl($record, ['photo_attachment_id' => 'photo']);

        return parent::sufHandleField($record, $action);
    }
}
