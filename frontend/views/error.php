<?php
/* @var $this yii\web\View */
/* @var $title string */
/* @var $type string */
/* @var $message string */
?>

<?= $title ?>
<?= $type ?>
<?= $message ?>
<div class="error">
  <img class="img-responsive"
             src="<?= \Yii::$app->params['frontend_source'] ?>/img/404.jpg"/>
</div>