<?php

namespace frontend\controllers;

use Yii;

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

        return $this->render('index', ['html' => $this->renderListPage(1)]);
    }

    /**
     * ajax 获取下一页列表
     */
    public function actionAjaxList()
    {
        $page = Yii::$app->request->get('page');
        $this->success([
            'html' => $this->renderListPage($page)
        ]);
    }

    /**
     * 渲染列表视图并返回 html
     *
     * @access private
     *
     * @param integer $page
     *
     * @return string
     */
    private function renderListPage($page)
    {
        $list = $this->listProduct($page, Yii::$app->params['product_page_size']);
        $content = $this->renderPartial('list', compact('list'));

        return $content;
    }
}
