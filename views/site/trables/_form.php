<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Trables $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="trables-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>


    <?= $form->field($model, 'who')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
