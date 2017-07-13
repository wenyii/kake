<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

?>

<script type="text/javascript">

    $(function () {
        var options = <?= json_encode($options, JSON_UNESCAPED_UNICODE) ?>;

        $.crop({
            width: options.data.crop.width,
            height: options.data.crop.height,
            postData: {
                url: options.data.url
            },
            submitCallback: function () {
                $.handleUpload(options);
            }
        });
    });
</script>


<div id="crop-box">
    <div class="container">
        <img id="crop" src="<?= $options['data']['url'] ?>">
    </div>
    <div class="assist">
        <?php $crop = $options['data']['crop']; ?>
        <div class="crop-preview"></div>
        <br>
        <button type="button" class="btn btn-primary crop-submit">选定区域进行截取</button>
    </div>
</div>