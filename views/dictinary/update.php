<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Dictinary $model */

$this->title = 'Редактирование словаря: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Словари', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="dictinary-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
