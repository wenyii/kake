<?php
/* @var $this yii\web\View */
/* @var $title string */
/* @var $type string */
/* @var $message string */
?>

<?= $title ?>
<?= $type ?>
<?= $message ?>
<div class="public">
    <div class="title">Oops!</div>
    <div class="prompt-img">
     <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/error/404.png"/>
    </div>
    <div class="prompt-message">
      亲,您所访问的页面走失了!
    </div>

</div>