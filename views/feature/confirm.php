<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Feature $model */

$this->title = 'Подтверждение удаления';
$this->params['breadcrumbs'][] = ['label' => 'Список свойств', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-create">

    <h4>Вы точно уверены что хотите удалить свойство "<?=$model->name?>"?</h4>
    <?PHP

    $form = ActiveForm::begin();

    echo "<h6>Это свойство привязано к ".count($model->categories)." следующим категориям:</h6>";

    echo "<ul class='list-group'>";
    foreach ($model->categories as $c){
        echo "<li class='list-group-item-primary list-group-item'>$c->name </li>";
    }
    echo "</ul>";

    if(count($model->featureValues) > 0){
        echo "<h5>Это свойство используется в ".count($model->featureValues)." следующих средствах со следующими значениями:</h5>";
        echo "<ul class='list-group'>";
        foreach ($model->featureValues as $fv){

            echo "<li class='list-group-item-primary list-group-item'>[ID:{$fv->item->id}] {$fv->item->name} ({$fv->item->inv_num}) <span class='badge bg-secondary'>$fv->value</span></li>";
        }
        echo "</ul>";

    }

    ?>
    <input type="hidden" name="confirm" value="1">
<br>
    <h5><span class="bg-danger badge">В итоге все вышеперечисленные значения этого свойства будут отвязаны и безвозвратно удалены, продолжить?</span></h5>
    <div class="form-group">
        <?= Html::a('Я передумал, оставить!',"/feature/index", ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Точно уверен, удалить!', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
