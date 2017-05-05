<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>

<p ng-init='wxPayment(<?= $json ?>, "<?= $order_number ?>")'></p>