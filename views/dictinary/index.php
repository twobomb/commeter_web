<?php

use app\models\Dictinary;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var app\models\DictinarySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Словари';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictinary-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([

        'panel' => [
            'after' => false,
            'heading' => '<i class="fas fa-user-friends"></i>  Словари',
            'type' => 'primary',
            'before' => '<div style="padding-top: 7px;"><em>* Список всех словарей.</em></div>',
        ],
        'hover' => true,
        'export' => [
            'fontAwesome' => false
        ],
        'floatHeader' => true, // table header floats when you scroll
        'exportConfig' => [
            'xls' => [],
            'csv' => [],
            'txt' => [],
            'json' => [],
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
        'toolbar' =>  [
            [
                'content' =>
                    Html::a('<i class="fas fa-plus"></i> Добавить словарь',"dictinary/create", [
                        'class' => 'btn btn-success'
                    ]),
                'options' => ['class' => 'btn-group mr-2 me-2']
            ],
            '{export}',
            '{toggleData}',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => '\kartik\grid\ExpandRowColumn',
                'value'=>function ($model, $key, $index,$column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detailUrl'=>'/dictinary/detail-dictinary'
            ],
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:50px'],
            ],
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Dictinary $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

</div>
