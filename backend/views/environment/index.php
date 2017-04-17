<?php
/* @var $this yii\web\View */
?>

<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>标题</th>
        <th>内容</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($env as $key => $item): ?>
    <tr>
        <td><?= $key + 1 ?></td>
        <td><?= $item['name'] ?></td>
        <td><?= $item['value'] ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>