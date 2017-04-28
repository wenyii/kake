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
        $this->sourceJs = false;

        list($html, $over) = $this->renderListPage(1);

        return $this->render('index', compact('html', 'over'));
    }

    /**
     * ajax 获取下一页列表
     */
    public function actionAjaxList()
    {
        $page = Yii::$app->request->post('page');

        list($html, $over) = $this->renderListPage($page);
        $this->success(compact('html', 'over'));
    }

    /**
     * 渲染列表视图并返回 html
     *
     * @access private
     *
     * @param integer $page
     *
     * @return array
     */
    private function renderListPage($page)
    {
        $pageSize = Yii::$app->params['product_page_size'];
        $list = $this->listProduct($page, $pageSize);
        $content = $this->renderPartial('list', compact('list'));

        return [
            $content,
            count($list) == $pageSize ? false : true
        ];
    }
}
