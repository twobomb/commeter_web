<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Редактирование пользователя: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/admin/users-list']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
