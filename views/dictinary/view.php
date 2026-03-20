<?php

use app\models\Dictinary;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Dictinary $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Словари', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="dictinary-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>



    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name:ntext',
        ],
    ]) ?>

    <?PHP


    echo "<ul class='list-group'>";
    foreach ($model->dictinaryItems as $di){
        echo "<li class='list-group-item'>$di->value </li>";
    }
    echo "</ul>";
    ?>

</div>
