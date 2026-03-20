<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput()->label("Пароль(Оставьте пустым если не нужно изменять)") ?>

    <?= $form->field($model, 'role')->dropDownList([\app\models\DataExt::getRolesInp()]) ?>

    <?= $form->field($model, 'departmentsAccess')->widget(Select2::classname(), [
        'data' => \app\models\Department::getListDepartments(),
        'value'=>$model->departmentsAccess,
        'options' => ['placeholder' => 'Выберите подразделение ...','multiple' => true],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>



    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
