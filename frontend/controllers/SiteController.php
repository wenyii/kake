<?php

namespace frontend\controllers;

use Yii;

/**
 * Site controller
 */
class SiteController extends GeneralController
{
    /**
     * Displays homepage.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = false;

        // 焦点图
        $params = Yii::$app->params;
        $focusList = $this->listProductFocus($params['site_focus_limit']);

        // 闪购模块
        $flashSalesList = $this->listProduct(1, $params['site_sale_limit'], 0, [
            'manifestation' => 2
        ]);

        // banner 模块
        $banner = $this->listAd(1, $params['site_banner_limit']);

        // 精品推荐
        $standardList = $this->listProduct(1, $params['site_product_limit'], DAY, [
            'manifestation' => 0
        ]);

        return $this->render('index', compact('focusList', 'flashSalesList', 'banner', 'standardList'));
    }
}
