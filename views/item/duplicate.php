<?php

use kartik\detail\DetailView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = "Дублирвоание средства '$model->name'";
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="item-view">

    <h5>Дублирвоание средства '<?= $model->name ?>'</h5>
    <div class="alert alert-success" role="alert">
        <h5 class="alert-heading"><i class="fa fa-info-circle"></i> Как это работает?</h5>
        <p> Введите начальный и конечный инвентарный номер, если нужно измените подразделение в которое дублировать средство и нажмите дублировать. Если тип дублирования 'По количеству', то будет просто дублировано средство заданное количество раз</p>
        <b> <i><i class="fas fa-exclamation"></i> Если к средству привязан сотрудник, во всех дублируемых средствах он будет отвязан! Теги также будут убраны у новых.</i></b>
        <hr>
        <p> <i>Например если ввести начальный <b>1000</b> и конечный <b>1005</b>, будет создано <b>6</b> таких же средств с аналогичными свойствами и инвентарными номерами <b>1000,1001,1002,1003,1004,1005</b></i></p>
    </div>

    <h5>Инвентарный номер дублируемого <span class="badge bg-primary"><?= $model->inv_num ?></span></h5>
    <form action="/item/duplicate" method="post" id="myForm">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input type="hidden" name="id" value="<?= $model->id ?>">
		
		<span>Тип дублирования:</span>
	  <?= Select2::widget([
            'name'=>'type_duplicate',
			'id'=>'typeDup',
            'value'=>"inv",
            'data' => ["inv"=>"По инвентарным номерам","col"=>"По количеству"],
            'options' => ['placeholder' => 'Выберите тип...'],
            'pluginOptions' => [
            ],
        ]); ?>
    <div class="input-group mb-3 mt-3 inp_inv">
        <span class="input-group-text bg-primary text-white">От</span>
        <input type="number" name="from" class="form-control invFrom" placeholder="Начальный инвентарный номер"  required>
        <span class="input-group-text  bg-primary text-white">до</span>
        <input type="number" name="to" class="form-control invTo" placeholder="Конечный инвентарный номер"  required>
    </div>
    <div class="input-group mb-3 mt-3 inp_col" style="display:none">
        <span class="input-group-text bg-primary text-white">Количество</span>
        <input type="number" name="countDup" class="form-control countDup" placeholder="Введите количество дублирований"  >
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text bg-primary text-white">В какое подразделение добавить: </span>

        <?PHP

            $deps = \app\models\Department::find()->where(["in","id",$model->department->unit->getIdsAllUnitDepartmentsWithUnit()])->all();

            echo Select2::widget([
            'name'=>'department_id',
            'value'=>$model->department_id,
            'data' => \yii\helpers\ArrayHelper::map($deps,"id","name"),
            'options' => ['placeholder' => 'Выберите подразделение ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
    </div>
    <h5 class="countDupleCont" style="display:none;">Будет создано средств: <span class="countDuple badge bg-success"></span></h5>
    <button type="submit" class="btn btn-success mb-5"><i class="fa fa-clone"></i> Дублировать</button>
    </form>
   


    <h5>Информация о свойствах средства '<?= $model->name ?>'</h5>
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
    ?>

</div>
<?PHP

$this->registerCSS(<<<'CSS'

	.fa-spinner{
		 animation: rotate 2s linear infinite;
	}
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
CSS);
$this->registerJS(<<<'JS'
$(function(){
	
	 $("#myForm").on("submit",function(e){
		 $(".item-view").hide();
		 $(".item-view").after("<h3><i  class='fa fa-spinner'></i>Ждите, добавляю...</h6>");
	 });
	 function check(){
        
		if($("#typeDup").val() == "inv"){
        
			let from = parseInt( $(".invFrom").val());
			let to =  parseInt( $(".invTo").val());
			
			if(!isNaN(from) && !isNaN(to) && from <= to){
				
				$(".countDuple").html(to - from +1);
				$(".countDupleCont").show();
			}
			else{            
				$(".countDuple").html("");
				$(".countDupleCont").hide();
			}
		}
		else if($("#typeDup").val() == "col"){
			let v = parseInt($(".countDup").val());
			if(!isNaN(v) ){
				
				$(".countDuple").html(v);
				$(".countDupleCont").show();
			}
			else{            
				$(".countDuple").html("");
				$(".countDupleCont").hide();
			}
			
		}
        
        
    }
	$(".invFrom").on("input",check);    
	$(".invTo").on("input",check);            
	$(".countDup").on("input",check);            
	
	$("#typeDup").on("change",function(){
			if($("#typeDup").val() == "col"){
				$(".inp_col").show()
				$(".inp_inv").hide()
				$(".invFrom").removeAttr("required");
				$(".invTo").removeAttr("required");
				$(".countDup").attr("required",true);
				
			}else if($("#typeDup").val() == "inv"){
				$(".inp_col").hide()
				$(".inp_inv").show()
				$(".invFrom").prop("required",true);
				$(".invTo").prop("required",true);
				$(".countDup").removeAttr("required");
			}				
			check();
	});
	$(".countDup").on("change",function(){
		
	});
})
JS);