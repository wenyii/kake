<?php
/* @var $this yii\web\View */
?>

<div class="container">
    <form class="form-signin" role="form">
        <h2 class="form-signin-heading"><?= $app_name ?></h2>
        <input type="text" name="phone" class="form-control" placeholder="Phone" autofocus>
        <div class="input-group">
            <input type="text" name="captcha" class="form-control" placeholder="Captcha">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button" id="get-captcha">发送验证码</button>
            </span>
        </div>

        <input type="button" class="btn btn-lg btn-danger btn-block" value="Sign in">
    </form>
</div>