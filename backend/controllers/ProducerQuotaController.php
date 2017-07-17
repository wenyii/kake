<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 分销账目管理
 */
class ProducerQuotaController extends GeneralController
{
    // 模型
    public static $modelName = 'ProducerQuota';

    // 模型描述
    public static $modelInfo = '分销账目';

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = ['quota'];

    // 用户id
    public static $uid;

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'producer_name' => [
                'title' => '分销商',
                'elem' => 'input',
                'table' => 'user',
                'field' => 'username'
            ],
            'state' => [
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'producer_name' => [
                'title' => '分销商',
                'code'
            ],
            'quota' => [
                'title' => '当前佣金余额',
                'price',
                'code'
            ],
            'add_time',
            'update_time',
            'state' => [
                'code',
                'info'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'from' => ['A' => 'producer_quota'],
            'join' => [
                [
                    'table' => 'user',
                    'left_on_field' => 'producer_id'
                ]
            ],
            'select' => [
                'user.username AS producer_name',
                'A.*'
            ],
            'as' => 'A',
            'where' => [
                [
                    'sub' => [
                        'select' => 'MAX(producer_quota.add_time)',
                        'where' => ['`producer_id` = `A`.`producer_id`']
                    ],
                    'tpl' => "['A.add_time' => {SUB_QUERY}]"
                ],
                ['A.state' => 1]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function myCondition()
    {
        return [
            'order' => 'add_time DESC'
        ];
    }

    /**
     * 我的佣金余额
     *
     * @auth-pass-all
     */
    public function actionMy()
    {
        $record = $this->showFormWithRecord([
            'producer_id' => self::$uid,
            'state' => 1
        ], null, true);

        $quota = empty($record) ? 0 : $record['quota'];

        return $this->display('my', [
            'quota' => Helper::money($quota)
        ]);
    }

    /**
     * 提现
     *
     * @auth-pass-all
     */
    public function actionWithdraw()
    {
        $reference = $this->getControllerName('my');
        $quota = Yii::$app->request->post('quota');
        if (empty($quota) || $quota <= 0) {
            Yii::$app->session->setFlash('warning', '请输入申请提现金额');
            $this->goReference($reference);
        }

        // 判断是否有申请中的提现记录
        $controller = $this->controller('producer-withdraw');
        $withdraw = $controller->showFormWithRecord([
            'producer_id' => self::$uid,
            'state' => 1
        ], null, true, false);

        if (!empty($withdraw)) {
            Yii::$app->session->setFlash('danger', "上一次的提现申请还未被处理，请耐心等待");
            $this->goReference($reference);
        }

        // 判断提现是否超额
        $record = $this->showFormWithRecord([
            'producer_id' => self::$uid,
            'state' => 1
        ], 'my', true, false);

        $have = empty($record) ? 0 : $record['quota'];
        if ($quota > $have) {
            $have = Helper::money($have);
            Yii::$app->session->setFlash('danger', "佣金余额不足，可提现余额为：${have}");
            $this->goReference($reference);
        }

        $controller = $this->controller('producer-withdraw');
        $controller->actionAddForm($reference, 'add', [
            'producer_id' => self::$uid,
            'withdraw' => $quota
        ]);
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        self::$uid = $this->user->id;

        return true;
    }
}
