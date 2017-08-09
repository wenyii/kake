<?php

namespace backend\controllers;

use Yii;

/**
 * 服务号菜单管理
 *
 * @auth-inherit-except add front sort
 */
class WxMenuController extends GeneralController
{
    /**
     * 服务号菜单预览
     *
     * @auth-same wx-menu/edit
     */
    public function actionIndex()
    {
        // $source = Yii::$app->wx->material->lists('news');
        // $this->dump($source);

        $menu = Yii::$app->wx->menu->current();
        $menu = empty($menu->selfmenu_info['button']) ? [] : $menu->selfmenu_info['button'];
        foreach ($menu as &$item) {
            if (!isset($item['sub_button'])) {
                continue;
            }
            $item['sub_button'] = $item['sub_button']['list'];
        }
        $menu = json_encode($menu, JSON_UNESCAPED_UNICODE);

        return $this->display('index', compact('menu'));
    }

    /**
     * 服务号菜单编辑
     */
    public function actionEdit()
    {
        $wx = Yii::$app->wx;
        $menu = json_decode(Yii::$app->request->post('menu'), true);
        if (empty($menu)) {
            $this->error('菜单JSON代码为空或非法');
        }

        $wx->menu->destroy();
        $result = $wx->menu->add($menu);

        if ($result->errmsg == 'ok') {
            Yii::$app->session->setFlash('success', '菜单编辑成功，等待5分钟或重新关注后可见');
        } else {
            Yii::$app->session->setFlash('danger', $result->errmsg);
        }

        return $this->redirect(['wx-menu/index']);
    }
}
