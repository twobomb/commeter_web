<?php

use app\assets\AppAsset;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = 'Добавление: '.$model->category->name;
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fa fa-info-circle" style="font-size: 24px"></i> Если вам нужно добавить много однотипных средств у которых отличается только инвентарный номер, добавьте одно средство и вопспользуйтесь функцией дублирование!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <h5>Подразделение: <span class="badge bg-primary"><?= $model->department->name ?></span></h5>
    <h5>Добавление в категорию: <span class="badge bg-primary"><?= $model->category->name ?></span></h5>

    <?= $this->render('_form', [
        'model' => $model,
        'is_partial'=>$is_partial
    ]) ?>

</div>
