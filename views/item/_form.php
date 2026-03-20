<?php

use app\assets\AppAsset;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Item $model */
/** @var yii\widgets\ActiveForm $form */


AppAsset::register($this);
?>
<div class="item-form">

    <?php $form = ActiveForm::begin(["id"=>"add_item_form"]); ?>
        <?=  $form->errorSummary($model); ?>
    <?= $form->field($model, 'category_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'department_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inv_num')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'responsible_employee_id')->widget(Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map($model->department->responsibles,"id","fullName"),
        'options' => ['placeholder' => 'Выберите материально-отвественного ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($model, 'employee_id')->widget(Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Employee::find()->where(["department_id"=>$model->department_id])->all(),"id","fullName"),
        'options' => ['placeholder' => 'Выберите сотрудника ...',"id"=>"emp_sel"],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label("Сотрудник (не обязательно)"); ?>


    <?= $form->field($model, 'workspace',["template"=>"{label}\n<div style='display: flex'>{input}<button type='button' ".($model->isNewRecord?"":"style='display:none'")." class='btn btn-secondary btnAuto' title='Автоподставновка местонахождение по кабинету сотрудника'>А</button></div>\n{hint}\n{error}"])->textInput(["disabled"=>!$model->isNewRecord]) ?>
    <?PHP
        if(!$model->isNewRecord):?>

            <div class="alert alert-warning" role="alert">
                <i class="fa fa-exclamation-square"></i> Используйте функционал <b><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"></path>
                    </svg> перемещение</b>, чтобы изменить местонахождение!
            </div>
        <?PHP endif;

    ?>
    <?= $form->field($model, 'is_written_off')->checkbox() ?>
    <?PHP

    foreach ($model->category->features as $feat):
        $required = $feat->is_required == 1;
        $label = $feat->name . ($required?"(обязательное)":"");
        switch ($feat->type){
        /*
          "string"=>"Строка",
          "text"=>"Текст",
          "int"=>"Целое число",
          "double"=>"Дробное число",
          "list"=>"Список из словаря",
          "date"=>"Дата",
          "bool"=>"Булево значение(да\нет)"*/
            case "text":
                echo $form->field($model,"feats[$feat->id]")->label($label)->textarea(["required"=>$required]);
                break;
            case "string":
                echo $form->field($model,"feats[$feat->id]")->label($label)->textInput(["required"=>$required]);
                break;
            case "int":
                echo $form->field($model,"feats[$feat->id]")->label($label)->input("number",["required"=>$required,"step"=>1,"oninput"=>"this.value = parseInt(this.value);"]);
                break;
            case "double":
                echo $form->field($model,"feats[$feat->id]")->label($label)->input("number",["required"=>$required]);
                break;
            case "bool":
                echo $form->field($model,"feats[$feat->id]")->checkbox(["label"=>$label,"required"=>$required]);
                break;
            case "date":
                echo $form->field($model,"feats[$feat->id]")->widget(DatePicker::classname(), [
                'options' => ['placeholder' => "Введите $feat->name ...","required"=>$required],
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ])->label($feat->name);
                break;
            case "list":
                $data = [];
                if($feat->dictinary != null)
                    $data = \yii\helpers\ArrayHelper::map($feat->dictinary->dictinaryItems,"id","value");
                echo $form->field($model,"feats[$feat->id]")->label($label)->widget(Select2::classname(), [
                    'data' => $data,
                    'options' => ['placeholder' => 'Выберите '.$feat->name],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                break;

        }

    endforeach;
    echo $form->field($model,"edittags")->label("Теги")->widget(Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(Yii::$app->user->identity->tags,"id","name"),
        "name"=>"tags",
        'options' => ['placeholder' => 'Выберите теги...',"multiple"=>true],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
    <?PHP
        if(!$is_partial):
    ?>
    <div class="form-group mt-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
            <?PHP
        endif;
            ?>
    <?php ActiveForm::end(); ?>

</div>


<?php
if($is_partial){
    $this->registerJs(<<<'JS'
$(function (){
    $("#add_item_form").on("submit",function (ev){
            ev.preventDefault();
        });
});
JS
);
}

$this->registerJs(<<< 'JS'
    $(function (){        
        $("#emp_sel").on("change",function (){            
            if($("#item-workspace").val() != "")
                return;
            let id = $("#emp_sel").val();
            if(!id)return;
           autoPlace(id); 
        });
        
    });
$(".btnAuto").on("click",function (){
    
            let id = $("#emp_sel").val();
            if(!id)return;
           autoPlace(id); 
})
function autoPlace(id){
    $.ajax({
                url:"/employee/get-info",
                data:{
                    id:id
                },
                error:function(err){                    
                },
                success:function (data) {
                    if(data.cabinet == null || data.cabinet == "")return;
                        $("#item-workspace").val("каб." +data.cabinet);
                }
            });
}
JS
);
