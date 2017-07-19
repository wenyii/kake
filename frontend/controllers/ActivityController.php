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
            'activity/activity',
            'jquery.ajaxupload',
            'html2canvas',
            'canvas2image',
        ];

        return $this->render('story');
    }

    /**
     * ajax 上传照片
     */
    public function actionUploadPhoto()
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
        $post = Yii::$app->request->post();

        $result = $this->service('general.add-activity-story', [
            'user_id' => $this->user->id,
            'attachment' => $post['attachment'],
            'story' => $post['story']
        ]);

        if (is_string($result)) {
            $this->fail($result);
        }

        $this->success($result);
    }
}