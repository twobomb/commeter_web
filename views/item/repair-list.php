<?php

use app\models\Employee;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var app\models\search\EmployeeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Обслуживания';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([

        'panel' => [
            'after' => false,
            'heading' => '<i class="fa fa-wrench"></i>  Обслуживания',
            'type' => 'primary',
            'before' => '<div style="padding-top: 7px;"><em>* Список всех обслуживаний.</em></div>',
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
                'detailUrl'=>'/item/detail-repair'
            ],
            [
                'headerOptions' => ['style' => 'width:300px;'],
                'attribute' => 'item_id',
                'format'=>"raw",
                "filter"=>false,
                "label"=>"Средство",
                'value' => function($v){
                    return \yii\bootstrap5\Html::a($v->item->name,"/item/view?id=$v->item_id",['target'=>"_blank"]);
                },
            ],

            [
                'attribute' => 'inv_num',
                'label'=>'Инв.номер',
                "value"=>function(\app\models\Repair $v){

                    return $v->item->inv_num;
                }
            ],
            [
                'headerOptions' => ['style' => 'width:300px;'],
                'attribute' => 'category_id',
                'filter' =>  ArrayHelper::map(\app\models\Category::find()->asArray()->all(), 'id', 'name'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
                'format'=>"raw",
                "label"=>"Категория",
                'value' => function($v){
                    return $v->item->category->name;
                },

            ],
            [
                'attribute' => 'type',
                'filter' =>  \app\models\Repair::getTypes(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
                "value"=>function(\app\models\Repair $v){

                    return $v->DisplayTypeName();
                }
            ],

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, \app\models\Repair $model, $key, $index, $column) {

                    return Url::toRoute(["repair-".$action, 'id' => $model->id]);
                },
                'template' => '{delete}'
            ],
            //'cabinet:ntext',
            //'is_deleted',
            //'sort_id',
        ],
    ]); ?>


</div>
