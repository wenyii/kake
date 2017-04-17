<?php

namespace backend\controllers;

use common\components\Helper;
use yii;

/**
 * 用户登录
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class LoginController extends GeneralController
{
    /**
     * 登录展示页
     *
     * @access public
     * @auth-pass-all
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'simple';
        $this->sourceJs = null;
        $this->sourceCss = [
            'login/index'
        ];

        return $this->render('index', [
            'app_name' => Yii::$app->params['app_name']
        ]);
    }

    /**
     * 用户登录
     *
     * @access public
     * @auth-pass-all
     * @return void
     */
    public function actionAjaxLogin()
    {
        $params = Yii::$app->request->post();
        $params['ip'] = Yii::$app->request->userIP;
        $params['type'] = 'backend-login';

        $user = $this->service('user.login-check', $params);

        if (is_string($user)) {
            Yii::info(Yii::t('common', $user));
            $this->fail($user);
        }

        // Actions after login
        Helper::popOne($user, 'password');
        Yii::$app->session->set(self::USER, $user);

        $this->success($user, '登录成功');
    }

    /**
     * 退出登录
     *
     * @access public
     * @auth-pass-all
     * @return object
     */
    public function actionLogout()
    {
        Yii::$app->session->remove(self::USER);

        return $this->redirect(['/login/index']);
    }
}
