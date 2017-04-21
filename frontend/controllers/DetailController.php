<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Detail controller
 */
class DetailController extends GeneralController
{
    /**
     * Displays detail.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        $detail = $this->getProduct(Yii::$app->request->get('id'));
        if (empty($detail)) {
            $this->error(Yii::t('common', 'product data error'));
        }

        if ($detail['min_price'] <= 0) {
            $this->error(Yii::t('common', 'product price error'));
        }

        return $this->render('index', compact('detail'));
    }

    /**
     * 选择套餐
     */
    public function actionChoosePackage()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        $packageList = $this->listProductPackage(Yii::$app->request->get('id'));

        return $this->render('conbo', compact('packageList'));
    }

    /**
     * 用户支付
     */
    public function actionAjaxUserPay()
    {
        // TODO 验证参数并组织以下数据数组
        // 单个套餐数量不超过 10
        // 验证验证码的有效性
        $params = Yii::$app->request->post();
        $this->dump($params);
        $productId = Yii::$app->request->get('id');
        $package = [
            1 => 2,
            2 => 3
        ];
        $userInfo = [
            'name' => $params['name'],
            'phone' => $params['phone'],
            'captcha' => $params['captcha']
        ];
        $paymentMethod = 'wx';

        // TODO 修改用户表对应数据
        // params : $userInfo
        // return : booleans

        // TODO 生成下单链接
        if (!in_array($paymentMethod, [
            'wx',
            'ali'
        ])
        ) {
            $this->error(Yii::t('common', 'payment link illegal'));
        }

        return $this->createSafeLink([
            'product_id' => $productId,
            'package' => $package
        ], 'order/' . $paymentMethod . '/');
    }
}
