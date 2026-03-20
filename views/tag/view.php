<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Tag $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Теги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tag-view">

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
    <h5>Средства:</h5>
    <?PHP

    echo "<ul class='list-group'>";
    if(count($model->items) == 0)
        echo "<li class='list-group-item'>Пока не привязано ни одного средства <i style='font-size: 20px' class='fa fa-frown'></i></li>";
    foreach ($model->items as $di){
        echo "<li class='list-group-item'><a href='/item/view?id=$di->id'>[{$di->category->name}] $di->name ($di->inv_num) </a> </li>";
    }
    echo "</ul>";
    ?>

</div>
