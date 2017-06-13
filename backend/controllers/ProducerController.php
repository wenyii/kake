<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;
use yii\helpers\Url;

/**
 * 分销商管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class ProducerController extends GeneralController
{
    /**
     * 生成推广链接
     */
    public function actionIndex()
    {
        $producer = Helper::integerEncode($this->user->id);
        $link = Yii::$app->params['frontend_url'] . '/?channel=' . $producer;

        return $this->display('index', ['link' => $link]);
    }
}
