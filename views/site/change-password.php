<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = "Смена пароля для пользователя: " . $model->name;
?>
<div class="user-change-password">

    <?php $form = ActiveForm::begin(["options"=>["class"=>"offset-md-3 col-md-6"]]); ?>

    <h4 class="mb-5"><?= Html::encode($this->title) ?></h4>

    <?php // Если нужна проверка старого пароля, раскомментируйте следующую строку ?>
    <?php //= $form->field($model, 'old_password')->passwordInput() ?>

    <?= $form->field($model, 'new_password')->passwordInput(['autocomplete' => 'off']) ?>
    <?= $form->field($model, 'confirm_password')->passwordInput(['autocomplete' => 'off']) ?>

    <div class="form-group">
        <?= Html::submitButton('Изменить пароль', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>