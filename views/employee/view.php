<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Employee $model */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Сотрудники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Точно удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'first_name:ntext',
            'second_name:ntext',
            'last_name:ntext',
            'post:ntext',
            'cabinet:ntext',
            [
                    'attribute'=>'department_id',
                'value'=>function($m){
                    return $m->department->name;
                }
            ],
            [
                    'attribute'=>'is_responsible',
                'value'=>function($m){
                    return $m->is_responsible ==  1?"ДА":"Нет";
                }
            ]
        ],
    ])
    ?>

    <?PHP
    $items = $model->items;?>

    <div class="card cardMyList">
        <div class="card-header">
            <h6>Список сресдтв закрепленных за сотрудником [<?=count($items)?>]: </h6>
        </div>
      <?PHP

    echo "<div class=\"card-body\"><ol>";

    if(count($items) > 0):
        foreach ($items as $it):
        ?>
        <li><?= Html::a($it->name." ($it->inv_num)","/item/view?id=$it->id",["class"=>"btn btn-outline-primary mt-1"]) ?></li>
        <?PHP
        endforeach;
    else:
        echo "Список пуст";
    endif;

    echo "</ol></div>";
    ?>
    </div>


    <?PHP
    $items = $model->itemsResponsible;
    if(count($items) > 0 || $model->is_responsible): ?>
    <div class="card cardRespList mt-5">
        <div class="card-header">
            <h6>Список сресдтв за которые сотрудник несет мат.ответственность [<?=count($items)?>]: </h6>

            <a class="btn btn-warning" href="/employee/replace-responsible?id=<?=$model->id?>">Заменить мат.ответственного у всех средств</a>
        </div>
      <?PHP

    echo "<div class=\"card-body\"><ol>";

    if(count($items) > 0):
        foreach ($items as $it):
        ?>
        <li><?= Html::a($it->name." ($it->inv_num)","/item/view?id=$it->id",["class"=>"btn btn-outline-primary mt-1"]) ?></li>
        <?PHP
        endforeach;
    else:
        echo "Список пуст";
    endif;

    echo "</ol></div>";
    endif;
    ?>
    </div>

</div>
