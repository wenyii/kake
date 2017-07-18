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
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * 上传照片
     */
    public function actionUploadPhoto()
    {
        $this->sourceCss = ['activity/activity'];
        $this->sourceJs = [
            'html2canvas',
            'activity/activity'
        ];

        return $this->render('upload-photo');
    }
}