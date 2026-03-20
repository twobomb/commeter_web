<?php

use app\models\Feature;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var app\models\search\FeatureSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Свойства';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([

        'panel' => [
            'after' => false,
            'heading' => '<i class="fas fa-user-friends"></i>  Свойства',
            'type' => 'primary',
            'before' => '<div style="padding-top: 7px;"><em>* Список всех Свойств.</em></div>',
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
                    Html::a('<i class="fas fa-plus"></i> Добавить свойство',"/feature/create", [
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
                'detailUrl'=>'/feature/detail-feature'
            ],
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:50px'],
            ],
            'name',
            [
                    'attribute'=>"type",
                    'filter'=>Feature::getTypesList(),
                    'value'=>function($m){
                        $data = Feature::getTypesList();
                        return isset($data[$m->type])?$data[$m->type]:$m->type;
                    }
            ],
            [
                    'label'=>"Использование",
                    'value'=>function($m){
                        if(count($m->categories) == 0)
                            return null;
                        return "Используется в ".count($m->categories)." категории";
                    }
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Feature $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>
