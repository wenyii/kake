<?php

namespace frontend\controllers;

use common\models\Main;
use Yii;

/**
 * Activity controller
 */
class ActivityController extends GeneralController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->mustLogin();

        return parent::beforeAction($action);
    }

    /**
     * 我和酒店的故事
     */
    public function actionStory()
    {
        $this->sourceCss = ['activity/activity'];
        $this->sourceJs = [
            'html2canvas',
            'activity/activity'
        ];

        return $this->render('story');
    }

    /**
     * ajax 上传照片
     */
    public function actionAjaxUploadPhoto()
    {
        $this->uploader([
            'suffix' => [
                'png',
                'jpg',
                'jpeg',
                'jpe',
                'gif'
            ],
            'pic_sizes' => '200-MAX*200-MAX',
            'max_size' => 2048
        ]);
    }

    /**
     * 提交酒店故事数据
     */
    public function actionAjaxStory()
    {
        
    }
}