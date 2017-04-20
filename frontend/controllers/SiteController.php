<?php

namespace frontend\controllers;

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

        //闪购模块
        $flashSalesList = $this->listProduct(1, 2, 2);

        //广告模块
        $banner = current($this->listBanner(1));

        //精品推荐
        $standardList = $this->listProduct(1, 4, 0);

        return $this->render('index', compact('focusList', 'flashSalesList', 'banner', 'standardList'));
    }
}
