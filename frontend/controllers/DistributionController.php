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
        $this->mustLogin();

        $this->sourceCss = null;
        $this->sourceJs = null;

        $producer = $this->getProducer($this->user->id);
        if (empty($producer)) {
            $this->error(Yii::t('common', 'become a producer please contact service'));
        }

        $limit = Yii::$app->params['distribution_limit'];
        $product = $this->service('producer.list-product-ids', [
            'producer_id' => $this->user->id,
            'limit' => $limit
        ]);

        if (empty($product)) {
            $this->error(Yii::t('common', 'please add distribution products first'));
        }

        $product = $this->listProduct(1, null, DAY, ['ids' => $product]);

        return $this->render('index', compact('producer', 'product'));
    }
}
