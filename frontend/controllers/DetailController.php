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
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->mustLogin();
    }

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
        $this->sourceJs = [
            'detail/index'
        ];

        $productId = Yii::$app->request->get('id');
        $packageList = $this->listProductPackage($productId);

        return $this->render('choose-package', compact('packageList', 'productId'));
    }

    /**
     * 支付前处理
     */
    public function actionPrefixPayment()
    {
        /*
        $_POST = [
            'user_info' => [
                'name' => 'Leon',
                'phone' => '15021275672',
                'captcha' => '256461'
            ],
            'payment_method' => 'wx',
            'product_id' => 1,
            'package' => [
                // package_id => numbers
                1 => 1,
                2 => 4,
            ]
        ];
        */

        // 用户信息
        $userInfo = Yii::$app->request->get('user_info');
        $result = $this->service('user.edit-real-info', [
            'user_id' => $this->user->id,
            'real_name' => $userInfo['name'],
            'phone' => $userInfo['phone'],
            'captcha' => $userInfo['captcha']
        ]);

        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }

        // 支付方式
        $paymentMethod = Yii::$app->request->get('payment_method');
        if (!in_array($paymentMethod, [
            'wx',
            'ali'
        ])
        ) {
            $this->error(Yii::t('common', 'payment link illegal'));
        }

        return $this->createSafeLink([
            'product_id' => Yii::$app->request->get('product_id'),
            'package' => Yii::$app->request->get('package')
        ], 'order/' . $paymentMethod);
    }
}
