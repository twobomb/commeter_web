<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Feature $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="feature-form">

    <?php $form = ActiveForm::begin(["options"=>["id"=>"form_feature"]]); ?>


    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList(\app\models\Feature::getTypesList()) ?>

    <?= $form->field($model, 'dictinary_id')->widget(Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Dictinary::find()->all(),"id","name"),
        'options' => ['placeholder' => 'Выберите словарь ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>


    <?= $form->field($model, 'is_required')->checkbox() ?>


    <?= $form->field($model, 'sort_id')->input("number") ?>

    <?= Html::tag("h4","Выберите категории содержащие это свойство:")?>

    <?= Select2::widget([
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->all(),"id","name"),
        'name'=>'pinned_categories',
        'value'=>\yii\helpers\ArrayHelper::getColumn($model->categories,"id"),
        'options' => ['placeholder' => 'Выберите связанные категории ...','multiple'=>true],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs(<<<'JS'
    $("#form_feature").on("submit",function (e){
        if($("#feature-type").val() == "list" && !$("#feature-dictinary_id").val()){
            alert("Выберите словарь или измените тип поля!")
            e.preventDefault();        
            return false;   
        }
    })
    $("#feature-type").on("change",update);
    function update(){
        if($("#feature-type").val() == "list")
            $(".field-feature-dictinary_id").show("fast");
        else
            $(".field-feature-dictinary_id").hide("fast");
    }
    update()
JS
);