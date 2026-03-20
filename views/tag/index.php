<?php

use app\models\Tag;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var app\models\search\TagSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Теги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fa fa-info-circle" style="font-size: 24px"></i>
        Теги позволяют например группировать средства одной категории для удобного поиска и работы с ними. Одному средству можно присвоить несколько тегов. <br><i>Например создав тег 'Сломан SSD' и присвоив его нужным компьютерам и ноутбукам, всегда можно быстро найти все устройства со сломанным SSD!</i>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <?= GridView::widget([

        'panel' => [
            'after' => false,
            'heading' => '<i class="fas fa-tags"></i>  Теги',
            'type' => 'primary',
            'before' => '<div style="padding-top: 7px;"><em>* Теги являются персональными для каждого пользователя и никто кроме вас не увидит их.</em></div>',
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
                    Html::a('<i class="fas fa-plus"></i> Добавить тег',"create", [
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
                'detailUrl'=>'/tag/detail-tag'
            ],
            'name',
            [
                'format'=>"raw",
                "label"=>"Количество привязок",
                'value' => function($v){
                    return count($v->items);
                },

            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Tag $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

</div>
