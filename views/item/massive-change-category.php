<?php

use app\assets\AppAsset;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Transfer $model */

$this->title = "Массовое изменение категории";
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
AppAsset::register($this);
?>
<div class="item-view">

    <?php $form = ActiveForm::begin(["id"=>"massiveCategoryForm"]); ?>
        <div class="form-group">

            <?PHP
            $items = \Yii::$app->session->get("SELECTED_TRANSFER_ITEMS",[]);
            if(count($items)== 0 ):?>

                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-ban"></i> Не выбрано ни одного средства
                </div>
                <div class="alert alert-info" role="alert">
                    <i class="fa fa-exclamation-square"></i> Для выбора средств, нажмите кнопку ниже [<i class="fa fa-check-circle"></i> Перейти к выбору средств'], далее выберите нужные средства (кнопка выбора в самой правой колонке). После того как выберите все средства вернитесь в это окно [Дополнительно => Смена категории средств]
                </div>
            <?PHP
            else:?>
            <h5>Список средств для изменения категории:</h5>
            <table class="containerTrans">
            <?PHP
            $n = 1;
                foreach (\app\models\Item::find()->where(["in",'id',$items])->all() as $it):
                    ?>
                <tr class="transItem"><td><?= $n++?> <input type="hidden" name="items[]" value="<?= $it->id ?>" ></td><td><a href="/item/view?id=<?= $it->id ?>" target="_blank"><?= $it->name ?></a></td><td ><?= $it->inv_num?></td><td ><?= $it->category->name?></td><td ><?= $it->department->name?></td>  <td>
                        <a href="/item/select-item?id=<?=$it->id?>&val=0"><i class="fa fa-trash"></i></a> </td> </tr>
                <?PHP endforeach;?>
            </table>
                <?PHP
            endif;
            ?>
            <a href="/item/index?mode=select&backtype=change_cat" class="mt-2 btn btn-primary"><i class="fa fa-check-circle"></i> Перейти к выбору средств</a>
            <br><br>
            <span>Выберите категорию в которую переместить средства:</span>
            <?= Select2::widget([
                'name'=>'category_id',
                'data' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->all(),"id","name"),
                'options' => ['placeholder' => 'Выберите категорию ...','id'=>"catToSelect"],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]); ?>
            <br><br>
            <?= Html::submitButton('<i class="fa fa-save"></i> Сменить', ['class' => 'btn btn-success']) ?>
        </div>
    <?php
    ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<'JS'


JS
);