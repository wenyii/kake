<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 分销提现管理
 */
class ProducerWithdrawController extends GeneralController
{
    // 模型
    public static $modelName = 'ProducerWithdraw';

    // 模型描述
    public static $modelInfo = '分销提现';

    /**
     * @var array Hook
     */
    public static $hookPriceNumber = ['withdraw'];

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return [
            [
                'text' => '转账完成',
                'value' => 'complete',
                'level' => 'success confirm-button',
                'icon' => 'eye-open',
                'show_condition' => function ($record) {
                    return $record['state'] == 1;
                }
            ],
            [
                'text' => '关闭申请',
                'value' => 'close',
                'level' => 'default confirm-button',
                'icon' => 'eye-close',
                'show_condition' => function ($record) {
                    return $record['state'] == 1;
                }
            ],
        ];
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
                'value' => 'all'
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
            'withdraw' => [
                'title' => '提现金额',
                'price',
                'code'
            ],
            'add_time',
            'update_time',
            'state' => [
                'code',
                'info',
                'color' => [
                    0 => 'default',
                    1 => 'warning',
                    2 => 'success'
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function indexCondition()
    {
        return [
            'join' => [
                [
                    'table' => 'user',
                    'left_on_field' => 'producer_id'
                ]
            ],
            'select' => [
                'user.username AS producer_name',
                'producer_withdraw.*'
            ]
        ];
    }

    /**
     * 完成提现
     *
     * @access public
     *
     * @param integer $id
     */
    public function actionComplete($id)
    {
        $result = $this->service('producer.withdraw', ['id' => $id]);
        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
        } else {
            $quota = Helper::money($result['quota'] / 100);
            Yii::$app->session->setFlash('success', "提现申请处理完成，该分销商剩余佣金余额：{$quota}，请确认已转账给该分销商");
        }

        $this->goReference($this->getControllerName('index'));
    }

    /**
     * 关闭申请
     *
     * @access public
     *
     * @param integer $id
     */
    public function actionClose($id)
    {
        $reference = $this->getControllerName('index');
        $this->actionEditForm($reference, 'edit', [
            'id' => $id,
            'state' => 0
        ]);
    }
}
