<?php

namespace console\controllers;

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
        
    }
}