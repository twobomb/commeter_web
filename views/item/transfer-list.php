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

$this->title = 'Перемещения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([

        'panel' => [
            'after' => false,
            'heading' => '<i class="fas fa-map"></i>  Перемещения',
            'type' => 'primary',
            'before' => '<div style="padding-top: 7px;"><em>* Список всех перемещений.</em></div>',
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
                'detailUrl'=>'/item/detail-transfer'
            ],
            [
                'attribute' => 'department_id_from',
                'filter' =>  ArrayHelper::map(\app\models\Department::find()->asArray()->all(), 'id', 'name'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
                "value"=>function($v){
                    return $v->departmentFrom->name;
                }
            ],
            [
                'attribute' => 'department_id_to',
                'filter' =>  ArrayHelper::map(\app\models\Department::find()->asArray()->all(), 'id', 'name'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
                "value"=>function($v){
                    return $v->departmentTo->name;
                }
            ],
            [
                'attribute' => 'type',
                'filter' =>  \app\models\Transfer::getTypes(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
                "value"=>function($v){
                    return $v->DisplayType();
                }
            ],
            [
                'label'=>"Количество средств",
                "value"=>function($v){
                    return count($v->items);
                }
            ],
            [
                'label'=>"Дата",
                "value"=>function($v){
                    return Yii::$app->formatter->asDate($v->date);
                }
            ],
            //'cabinet:ntext',
            //'is_deleted',
            //'sort_id',
        ],
    ]); ?>


</div>
