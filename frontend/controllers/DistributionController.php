<?php

namespace frontend\controllers;

use common\components\Helper;
use Yii;

/**
 * Distribution controller
 */
class DistributionController extends GeneralController
{
    /**
     * Displays index.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        $channel = Yii::$app->request->get('channel');
        if (!$channel) {
            $this->error(Yii::t('common', 'params channel is required'));
        }

        // 获取分销商信息
        $producer = $this->getProducer($this->user->id);
        if (empty($producer)) {
            $this->error(Yii::t('common', 'become a producer please contact service'));
        }

        // 获取分销产品
        $limit = Yii::$app->params['distribution_limit'];
        $product = $this->service('producer.list-product-ids', [
            'producer_id' => $this->user->id,
            'limit' => $limit
        ]);
        if (empty($product)) {
            $this->error(Yii::t('common', 'the distributor need select product first'));
        }
        $product = $this->listProduct(1, null, DAY, ['ids' => $product]);

        return $this->render('index', compact('producer', 'product'));
    }

    /**
     * Displays center.
     */
    public function actionCenter()
    {
        $this->sourceCss = null;
        $this->sourceJs = false;

        $list = $this->cache('list-self-producer-log', function () {
            $controller = $this->controller('producer-log');
            $controller::$uid = $this->user->id;
            $list = $this->callMethod('listProducerLog', null, [false], $controller);

            return $list;
        }, DAY, null, Yii::$app->params['use_cache']);

        // 分销记录
        $producerLogCount = count($list);

        return $this->render('center');
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->mustLogin();

        return parent::beforeAction($action);
    }
}
