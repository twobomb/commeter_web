<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Feature $model */

$this->title = 'Создать свойство';
$this->params['breadcrumbs'][] = ['label' => 'Список свойств', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
