<?php

namespace frontend\controllers;

use common\components\Helper;
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

        $params = Yii::$app->params;

        // 焦点图
        $focusList = $this->listProductFocus($params['site_focus_limit']);
        $focusList = array_merge($focusList, $this->listAd(0, $params['site_ad_focus_limit']));
        $focusList = Helper::arraySort($focusList, 'update_time', 'DESC');

        // 闪购模块
        $flashSalesList = $this->listProduct(1, $params['site_sale_limit'], 0, [
            'manifestation' => 2
        ]);

        // banner 模块
        $banner = $this->listAd(1, $params['site_ad_banner_limit']);

        // 精品推荐
        $standardList = $this->listProduct(1, $params['site_product_limit'], DAY, [
            'manifestation' => 0
        ]);

        return $this->render('index', compact('focusList', 'flashSalesList', 'banner', 'standardList'));
    }
}
