<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = 'Редактирование: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="item-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'is_partial'=>$is_partial
    ]) ?>

</div>
