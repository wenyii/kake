<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>

<div class="browser" ng-init="pollOrder('<?= $order_number ?>', <?= $user_id ?>, '<?= $time ?>')">
    <img class="img-responsive"
         src="<?= $params['frontend_source'] ?>/img/message/browser.png"/>
</div>