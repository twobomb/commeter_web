<?php

use kartik\detail\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="item-view">

    <?PHP
    $attributes = [
        [
            'group'=>true,
            'label'=>'<i class="fa fa-info-circle" style="font-size: 18px"></i> Основная информация',
            'rowOptions'=>['class'=>'table-info']
        ],
        [
            'columns' => [
                [
                    'attribute'=>'name',
                ]
            ],
        ],
        [
            'columns' => [
                [
                    'attribute'=>'id',
                    'valueColOptions'=>['style'=>'width:30%']
                ],
                [
                    'attribute'=>'date_change',
                    'format'=>'raw',
                    'value'=>Yii::$app->formatter->asDatetime($model->date_change),
                    'valueColOptions'=>['style'=>'width:30%'],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute'=>'inv_num',
                    'valueColOptions'=>['style'=>'width:200px'],
                    'format'=>'raw',
                    'value'=>"<h6><span class='badge bg-secondary'>$model->inv_num</span></h6>"
                ],
                [
                    'attribute'=>'responsible_employee_id',
                    'labelColOptions'=>['style'=>'width:300px'],
                    'format'=>'raw',
                    'value'=>(function($model){
                        if($model->responsibleEmployee == null)
                            return '<span class="not-set">(не задано)</span>';

                        return Html::a($model->responsibleEmployee->fullName,"/employee/view?id=".$model->responsible_employee_id);
                    })($model)
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute'=>'department_id',
                    'format'=>'raw',
                    'value'=>(function($model){
                        if($model->department == null)
                            return '';

                        return $model->department->name;
                    })($model)
                ],
                [
                    'attribute'=>'category_id',
                    'format'=>'raw',
                    'value'=>(function($model){
                        if($model->category == null)
                            return '';

                        return $model->category->name;
                    })($model)
                ],
                [
                    'attribute'=>'is_written_off',
                    'label'=>"Списан?",
                    'format'=>'raw',
                    'value'=>(function($model){
                        if($model->is_written_off)
                            return " <span class='bg-danger text-white p-2'><i class='fa fa-check'></i> Списан</span>";
                        return "Нет";
                    })($model)
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute'=>'employee_id',
                    'format'=>'raw',
                    'valueColOptions'=>['style'=>'width:30%'],
                    'value'=>(function($model){
                        if($model->employee == null)
                            return '<span class="not-set">(не задано)</span>';

                        return Html::a($model->employee->fullName,"/employee/view?id=".$model->employee_id);
                    })($model)
                ],
                [
                    'attribute'=>'workspace',
                    'valueColOptions'=>['style'=>'width:30%'],
                    'format'=>'raw',
                    'value'=>(function($model){
                        if(empty($model->workspace))
                            return '<span class="not-set">(не задано)</span>';
                        return $model->workspace;
                    })($model)
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label'=>"Теги",
                    'format'=>'raw',
                    'value'=>(function($model){
                        if(count($model->tags) == 0)
                            return '<span class="not-set">(нет тегов)</span>';

                        $html = "";
                        foreach ($model->tags as $tg)
                            $html.="<a  href='/tag/view?id=$tg->id' class='text-primary'>$tg->name</a>";

                            return $html;

                    })($model)
                ]
            ],
        ],
        [
            'group'=>true,
            'label'=>'<i class="fa fa-tasks" style="font-size: 18px"></i> Свойства',
            'rowOptions'=>['class'=>'table-info'],
            //'groupOptions'=>['class'=>'text-center']
        ]
    ];


    foreach ($model->featureValues as $fv){
        array_push($attributes,
            [
                'columns' => [
                    [
                        'label'=>$fv->feature->name,
                        'format'=>'raw',
                        'value'=>$fv->displayValue
                    ]
                ],
            ]);
    }

    if(count($model->transfers)> 0){
        array_push($attributes,
            [
                'group'=>true,
                'label'=>'<i class="fa fa-map" style="font-size: 18px"></i> Перемещения ['.count($model->transfers).']',
                'rowOptions'=>['class'=>'table-info'],
                //'groupOptions'=>['class'=>'text-center']
            ]);
        ob_start();
        ?>
        <ul class="list-group">
            <?PHP
            foreach ($model->transfers as $t):?>
                <li class="list-group-item"><?= "<b>Из</b> <span class='badge bg-secondary'>{$t->departmentFrom->name} ($t->workplace_from)</span>  <b>в</b>  <span class='badge bg-primary'>{$t->departmentTo->name} ($t->workplace_to)</span> " ?> <a class="btn btn-primary btn-sm" href="/item/transfer-list?TransferSearch[id]=<?=$t->id?>">Подробнее</a></li>
            <?PHP endforeach;
            ?>
        </ul>
        <?PHP
        $data = ob_get_contents();
        ob_clean();
        array_push($attributes,[
            'columns' => [
                [
                    'label'=>false,
                    'labelColOptions'=>['style'=>'display:none'],
                    'format'=>'raw',
                    'value'=>$data
                ]
            ],
        ]);
    }

    if(count($model->repairs)> 0){
        array_push($attributes,
            [
                'group'=>true,
                'label'=>'<i class="fa fa-wrench" style="font-size: 18px"></i> Обслуживания ['.count($model->repairs).']',
                'rowOptions'=>['class'=>'table-info'],
                //'groupOptions'=>['class'=>'text-center']
            ]);
        ob_start();
        ?>
        <ul class="list-group">
            <?PHP
            foreach ($model->repairs as $t):?>
                <li class="list-group-item"><?= $t->DisplayTypeName() ." ". Yii::$app->formatter->asDate($t->date)   ?> <a class="btn btn-primary btn-sm" href="/item/repair-list?RepairSearch[id]=<?=$t->id?>">Подробнее</a></li>
            <?PHP endforeach;
            ?>
        </ul>
        <?PHP
        $data = ob_get_contents();
        ob_clean();
        array_push($attributes,[
            'columns' => [
                [
                    'label'=>false,
                    'labelColOptions'=>['style'=>'display:none'],
                    'format'=>'raw',
                    'value'=>$data
                ]
            ],
        ]);
    }
    // View file rendering the widget
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        "mode"=>DetailView::MODE_VIEW,
        'bordered' => false,
        'enableEditMode'=>false,
        "buttons1"=>" {delete}",
        'condensed' => true,
        'responsive' => true,
        'hover' => true,
        'hAlign'=>"right",
        'vAlign'=>"middle",
        'panel' => [
            'type' => "primary",
            'heading' => '<i class="fa fa-archive"></i> '.$model->name,
            'footer' => '<div class="text-center text-muted"><hr></div>'
        ],
        'deleteOptions'=>[ // your ajax delete parameters
            "url"=>"/item/delete?id=$model->id",
        ],
        'container' => ['id'=>'itemDetail'],
//        'formOptions' => ['action' =>"/item/delete?id=$model->id"] // your action to delete
    ]);

    echo \yii\bootstrap5\Html::a("<i class='fa fa-history'></i> Показать историю","/item/history?id=$model->id",["class"=>"mt-2 btn btn-warning"])
    ?>

</div>
