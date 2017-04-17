<?php
namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Detail controller
 */
class DetailController extends GeneralController
{
    /**
     * Displays detail.
     */
    public function actionIndex()
    {
        $this->sourceCss = null;
        $this->sourceJs = null;

        $detail = $this->getProduct(Yii::$app->request->get('id'));
        $this->dump($detail);

        return $this->render('index', compact('detail'));
    }
}
