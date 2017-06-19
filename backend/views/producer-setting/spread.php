<?php
/* @var $this yii\web\View */
?>

<div class="title col-sm-offset-1">
    <span class="glyphicon glyphicon-fire"></span> 推广链接
</div>

<form class="form-horizontal">
    <div class="form-group">
        <div class="col-sm-8 col-sm-offset-1 input-group-lg">
            <input class="form-control" type="text" value="<?= $link ?>">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-3 col-sm-offset-1">
            <a href="javascript:void(null)" class="thumbnail">
                <img src="<?= $img ?>">
            </a>
        </div>
        <div class="col-sm-8">
            <p class="navbar-text">下载二维码：右键 > 图片另存为...</p>
        </div>
    </div>
</form>