<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Employee $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'department_id')->widget(Select2::classname(), [
            'data' => \yii\helpers\ArrayHelper::map(Yii::$app->user->identity->getAccessDepartments(false),"id","name"),
            'options' => ['placeholder' => 'Выберите подразделение ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>

    <?= $form->field($model, 'first_name')->textInput() ?>

    <?= $form->field($model, 'second_name')->textInput() ?>

    <?= $form->field($model, 'last_name')->textInput() ?>

    <?= $form->field($model, 'is_responsible')->checkbox() ?>


    <?= $form->field($model, 'advancedDepartments',['options'=>['style'=>'display:block;'],'template'=>'{label}{input}{error}{hint}<div class="alert alert-info" role="alert"><i class="fa fa-info-circle"></i> Если вы хотите чтобы другие подразделения могли указать этого мат.ответственного для своих средств связи, выберите эти  подразделения в списке выше. <i><b>Доступа к редактированию этого сотрудника они иметь не будут!</b></i> </div>'])->widget(Select2::classname(), [
            'data' => \yii\helpers\ArrayHelper::map(\Yii::$app->user->identity->getAccessDepartments(false),"id","name") ,//\app\models\Department::getListDepartments(),
            'value'=>$model->advancedDepartments,
            'options' => ['placeholder' => 'Выберите подразделение ...','multiple' => true],
            'pluginOptions' => [
                    'allowClear' => true
            ],
    ]);
    ?>


    <?= $form->field($model, 'post')->textInput() ?>

    <?= $form->field($model, 'cabinet')->textInput() ?>




    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
window.onload = function (){
    $("input[name='Employee[is_responsible]'").on("input",updateVisibleField);
    $("input[name='Employee[is_responsible]'").trigger("input");
    function updateVisibleField(){
        let el = $("input[name='Employee[is_responsible]'");
        if($(el).is(":checked"))
            $(".field-employee-advanceddepartments").show();
        else
            $(".field-employee-advanceddepartments").hide();

    }

}
</script>