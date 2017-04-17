<?php
/* @var $this yii\web\View */

use yii\widgets\LinkPager;
use yii\helpers\Url;

?>

<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>配置所属项目</th>
        <th>配置名称</th>
        <th>配置值</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 0; ?>
    <?php foreach ($config as $key => $item): ?>
        <tr>
            <?php $i += 1; ?>
            <td><?= ($page->getPage() * $page->getPageSize()) + $i ?></td>
            <td><code class="default"><?= $item['app_info'] ?></code></td>
            <td><code class="default"><?= $key ?></code></td>
            <td><?= $item['value'] ?></td>
            <td>
                <form method="post" action="<?= Url::to(['/config/add-form']) ?>">
                    <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="app" value="<?= $item['app'] ?>">
                    <input type="hidden" name="key" value="<?= $key ?>">
                    <input type="hidden" name="value" value="<?= $item['value'] ?>">
                    <button class="btn btn-primary btn-xs">配置到数据库</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="page">
    <?php
    echo LinkPager::widget([
        'pagination' => $page,
        'firstPageLabel' => true,
        'lastPageLabel' => true
    ]);
    ?>
</div>