<?php

use app\assets\AppAsset;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = "Обслуживание средства '$item->name'";
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $item->name, 'url' => ['view','id'=>$item->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
AppAsset::register($this);
?>
<div class="item-view">

    <h5>Обслуживание средства '<?= $item->name ?>', инвентарный номер '<?= $item->inv_num ?>'</h5>


        <?php $form = ActiveForm::begin(["id"=>"repairForm"]); ?>
          <?=  $form->errorSummary($model); ?>
        <?= $form->field($model, 'type')->dropDownList(\app\models\Repair::getTypes()) ?>
        <?= $form->field($model, 'item_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'description')->textarea(["placeholder"=>"Можете ввести дополнительную информацию..."]) ?>
        <?= $form->field($model, 'sum')->input("number") ?>
        <?= $form->field($model, 'date')->widget(\kartik\date\DatePicker::className(),[]) ?>
    <?PHP
     if(!$is_partial):
    ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php
        endif;
        ActiveForm::end(); ?>
</div>