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
     * Displays homepage.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        return $this->render('index');
    }

    /**
     * 退出登录
     *
     * @access public
     * @return object
     */
    public function actionLogout()
    {
        Yii::$app->session->remove(self::USER);

        return $this->redirect('site/index');
    }
}
