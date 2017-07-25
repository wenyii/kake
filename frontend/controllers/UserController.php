<?php

namespace frontend\controllers;

use Yii;

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
