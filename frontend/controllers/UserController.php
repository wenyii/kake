<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * User controller
 */
class UserController extends GeneralController
{
    /**
     * 退出登录
     *
     * @access public
     * @return object
     */
    public function actionLogout()
    {
        Yii::$app->session->destroy();

        return $this->redirect('site/index');
    }
}
