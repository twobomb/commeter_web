<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var ActiveForm $form */
$this->title = "Добавить пользователя";
?>
<div class="admin-user-create">

    <?php $form = ActiveForm::begin(["options"=>["class"=>"offset-md-3 col-md-6"]]); ?>

    <h3 class="mb-5"><?= Html::encode($this->title) ?></h3>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'login') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'role')->dropDownList([\app\models\DataExt::getRolesInp()]) ?>

        <div class="form-group">
            <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- admin-user-create -->
