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

        return $this->cache([
            'detail-index'
        ], function () {
            $detail = $this->getProduct(Yii::$app->request->get('id'));
            if (empty($detail)) {
                $this->error(Yii::t('common', 'product data error'));
            }

            if ($detail['min_price'] <= 0) {
                $this->error(Yii::t('common', 'product price error'));
            }

            return $this->render('index', compact('detail'));
        });

    }

    /**
     * 选择套餐
     */
    public function actionChoosePackage()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        $package = $this->listProductPackage(Yii::$app->request->get('id'));

        return $this->render('conbo', compact('package'));

    }
}
