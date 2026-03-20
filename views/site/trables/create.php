<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Trables $model */

$this->title = 'Обращение';
$this->params['breadcrumbs'][] = ['label' => 'Список неисправностей', 'url' => ['/site/trables-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trables-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
