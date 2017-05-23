<?php

namespace console\controllers;

use console\models\ActivityLotteryCode;
use Yii;
use yii\helpers\Console;

/**
 * Activity mission about we chat
 *
 * @author    <jiangxilee@gmail.com>
 * @copyright 2017-05-22 13:29:40
 */
class ActivityController extends GeneralController
{
    /**
     * @var integer Limit for openid list
     */
    public $limit = 20;

    /**
     * Define the params
     *
     * @access public
     *
     * @param string $actionID
     *
     * @return array
     */
    public function options($actionID)
    {
        $params = [];
        switch ($actionID) {
            case 'refresh-subscribe' :
                $params = ['limit'];
                break;
        }

        return $params;
    }

    /**
     * Define the params alias
     *
     * @access public
     * @return array
     */
    public function optionAliases()
    {
        return ['l' => 'limit'];
    }

    /**
     * Refresh the subscribe status
     *
     * @access public
     */
    public function actionRefreshSubscribe()
    {
        $model = new ActivityLotteryCode();

        /**
         * Handler query status by we chat api
         *
         * @param integer $page
         */
        $handler = function ($page = 1) use ($model, &$handler) {

            $page = intval($page) > 0 ? $page : 1;
            $where = ['state' => 1];

            $count = $model::find()
                ->where($where)
                ->count();
            $length = strlen($count);
            $totalPage = ceil($count / $this->limit);

            $result = $model::find()
                ->select('openid')
                ->where($where)
                ->offset(($page - 1) * $this->limit)
                ->limit($this->limit)
                ->asArray()
                ->all();
            $result = array_column($result, 'openid');

            $data = Yii::$app->wx->user->batchGet(array_values($result));

            if (!empty($data->user_info_list)) {
                foreach ($data->user_info_list as $user) {
                    $model::updateAll(['subscribe' => $user['subscribe'] ? 1 : 0], ['openid' => $user['openid']]);
                }
            }

            $progress = $page * $this->limit;
            $progress = str_pad($progress, $length, 0, STR_PAD_LEFT) . ' / ' . $count;

            $this->console('Task completion ：%s', [
                $this->color($progress, Console::FG_GREEN)
            ], null, null, $page == $totalPage ? null : ' ');

            if (count($result) == $this->limit) {
                $handler(++$page);
            }
        };
        $handler();

        return self::EXIT_CODE_NORMAL;
    }
}