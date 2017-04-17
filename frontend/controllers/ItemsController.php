<?php
namespace frontend\controllers;

use common\components\Helper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Items controller
 */
class ItemsController extends GeneralController
{
    /**
     * Displays index.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        $keyword = Yii::$app->request->get('keyword');
        $adKeyword = Yii::$app->params['site_search_ad_keyword'];
        $adUrl = Yii::$app->params['site_search_ad_url'];

        // 搜索推广
        if (!empty($adUrl) && ('' === trim($keyword) || $adKeyword === $keyword)) {
            $adUrl = $this->compatibleUrl($adUrl);

            return $this->redirect($adUrl);
        }

        $page = Yii::$app->request->get('page');
        $classify = Yii::$app->request->get('classify');
        $sale = !!Yii::$app->request->get('sale');

        $list = $this->listProduct($page, $keyword, null, $classify, $sale);

        return $this->render('index', ['items' => $list]);
    }
}
