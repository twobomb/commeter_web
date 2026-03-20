<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\FeatureSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="feature-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <?= $form->field($model, 'is_show_in_quickpanel')->checkbox() ?>

    <?= $form->field($model, 'is_show_in_grid')->checkbox() ?>

    <?= $form->field($model, 'is_required')->checkbox() ?>

    <?= $form->field($model, 'is_hidden')->checkbox() ?>

    <?= $form->field($model, 'type')->dropDownList(\app\models\Feature::getTypesList()) ?>

    <?=  $form->field($model, 'dictinary_id') ?>

    <?php // echo $form->field($model, 'sort_id') ?>

    <?php // echo $form->field($model, 'name') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
