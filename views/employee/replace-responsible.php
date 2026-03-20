<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \app\models\Employee $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = "Замена мат.ответственного";

$this->params['breadcrumbs'][] = ['label' => 'Сотрудники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>
    <h5>Замена мат.ответственного <?= $model->fullName?></h5>
    <?PHP
    $items = $model->itemsResponsible;
    if(count($items) > 0 || $model->is_responsible): ?>
    <div class="card cardRespList mt-5">
        <div class="card-header">
            <h6>Список сресдтв у которых будет проведена замена [<?=count($items)?>]: </h6>
        </div>
        <?PHP

        echo "<div class=\"card-body\"><ol>";

        if(count($items) > 0):
            foreach ($items as $it):
                ?>
                <li><?= Html::a($it->name." ($it->inv_num)","/item/view?id=$it->id",["class"=>"btn btn-outline-primary mt-1"]) ?></li>
            <?PHP
            endforeach;
        else:
            echo "Список пуст";
        endif;

        echo "</ol></div>";
        endif;
        ?>
    </div>

    <span>Заменить у всех средств мат.ответственного</span>
    <input type="hidden" name="old_resp_id" value="<?=$model->id?>">
    <?= Select2::widget([
        'name'=>'q',
        'data' => [$model->id=>$model->fullName],
        'value'=>$model->id,
        'pluginOptions' => [
            "disabled"=>true,
            'allowClear' => true
        ],
    ]); ?>
    <span>на</span>
    <?= Select2::widget([
        'name'=>'new_resp_id',
        'data' => \yii\helpers\ArrayHelper::map($model->department->responsibles,"id","fullName"),
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'mt-2 btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
