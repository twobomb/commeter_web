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

$this->title = 'Сотрудники';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([

        'panel' => [
            'after' => false,
            'heading' => '<i class="fas fa-user-friends"></i>  Сотрудники',
            'type' => 'primary',
            'before' => '<div style="padding-top: 7px;"><em>* Список всех сотрудников.</em></div>',
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
                    Html::a('<i class="fas fa-plus"></i> Добавить сотрудника',"create", [
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
                'detailUrl'=>'/employee/detail-employee'
            ],
            [
                'headerOptions' => ['style' => 'width:300px;'],
                'attribute' => 'fio',
                'format'=>"raw",
                "label"=>"ФИО",
                'value' => function($v){
                    return "$v->second_name $v->first_name $v->last_name";
                },

            ],
            'post:ntext',
            [
                'attribute' => 'department_id',
                'filter' =>  ArrayHelper::map(\Yii::$app->user->identity->getAccessDepartments(false), 'id', 'name'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ],
                "value"=>function($v){
                        return $v->department->name;
                }

            ],
            //'cabinet:ntext',
            [
                    'attribute'=>'is_responsible',
                'filter' =>  [1=>"Да",0=>"Нет"],
                'value'=>function($m){
                    return $m->is_responsible ?"Да":"Нет";
                }
            ],
            //'sort_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Employee $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
