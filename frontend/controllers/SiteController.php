<?php

namespace frontend\controllers;

use common\components\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
        $this->sourceJs = null;

        //焦点图
        $focusList = $this->listProductFocus(2);

        $list = $this->listProduct(1, 1);

        //闪购模块
        // $flashSalesList = $this->listProduct(2, 1);

        //广告模块
        $bannerList = $this->listBanner(1);

        //精品推荐
        // $standardList = $this->listProduct(0,1);

        return $this->render('index', compact('focusList', 'flashSalesList', 'bannerList', 'standardList'));

    }
}
