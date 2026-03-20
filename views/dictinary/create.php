<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Dictinary $model */

$this->title = 'Создание словаря';
$this->params['breadcrumbs'][] = ['label' => 'Словари', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictinary-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
