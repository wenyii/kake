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
        // 联系人信息
        $contacts = Yii::$app->request->get('user_info');
        if (!is_numeric($contacts)) {
            $contacts = $this->service('order.add-contacts', [
                'real_name' => $contacts['name'],
                'phone' => $contacts['phone'],
                'captcha' => $contacts['captcha']
            ]);

            if (is_string($contacts)) {
                $this->error(Yii::t('common', $contacts));
            }
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
            'package' => Yii::$app->request->get('package'),
            'order_contacts_id' => $contacts
        ], 'order/' . $paymentMethod);
    }
}
