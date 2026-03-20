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

    <?= $form->field($model, 'post')->textInput() ?>

    <?= $form->field($model, 'cabinet')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
