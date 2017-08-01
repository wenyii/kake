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

        list($html, $over) = $this->renderListPage(Yii::$app->request->get());

        return $this->render('index', compact('html', 'over'));
    }

    /**
     * ajax 获取下一页列表
     */
    public function actionAjaxList()
    {
        list($html, $over) = $this->renderListPage(Yii::$app->request->post());
        $this->success(compact('html', 'over'));
    }

    /**
     * 渲染列表视图并返回 html
     *
     * @access private
     *
     * @param array $params
     *
     * @return array
     */
    private function renderListPage($params)
    {
        $page = isset($params['page']) ? $params['page'] : 1;
        $pageSize = Yii::$app->params['product_page_size'];

        $list = $this->listProduct($page, $pageSize, DAY, $params);
        $content = $this->renderPartial('list', compact('list'));

        return [
            $content,
            count($list) < $pageSize
        ];
    }

    /**
     * 地区列表页
     *
     * @access public
     *
     * @param integer $plate
     *
     * @return string
     */
    public function actionRegion($plate = null)
    {
        $this->sourceCss = null;

        $region = $this->listRegion($plate);

        return $this->render('region', compact('region'));
    }
}
