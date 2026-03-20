<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Feature $model */

$this->title = 'Редактирование свойства: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Список свойств', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="feature-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
