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
    const PRODUCT_PAGE_NUM = 10;

    /**
     * Displays index.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        return $this->cache([
            'items-index'
        ], function () {
            return $this->render('index', ['html' => $this->renderListPage(1)]);
        });

    }

    /**
     * ajax 分页
     */
    public function actionAjaxList()
    {
        $page = Yii::$app->request->get('page');
        $this->success([
            'html' => $this->renderListPage($page)
        ]);
    }

    /**
     * Displays  list
     */
    private function renderListPage($page)
    {
        $list = $this->listProduct($page, self::PRODUCT_PAGE_NUM);
        $content = $this->renderPartial('list', compact('list'));

        return $content;
    }
}
