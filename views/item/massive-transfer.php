<?php

use app\assets\AppAsset;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Transfer $model */

$this->title = "Массовое перемещение средств";
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
AppAsset::register($this);
?>
<div class="item-view">

    <?php $form = ActiveForm::begin(["id"=>"massiveTransferForm"]); ?>
    <?=  $form->errorSummary($model); ?>
        <div class="form-group">

            <?PHP
            $items = \Yii::$app->session->get("SELECTED_TRANSFER_ITEMS",[]);
            if(count($items)== 0 ):?>

                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-ban"></i> Не выбрано ни одного средства
                </div>
                <div class="alert alert-info" role="alert">
                    <i class="fa fa-exclamation-square"></i> Для выбора средств, нажмите кнопку ниже [<i class="fa fa-check-circle"></i> Перейти к выбору средств'], далее выберите нужные средства (кнопка выбора в самой правой колонке). После того как выберите все средства вернитесь в это окно [Дополнительно => Массовое перемещение]
                </div>
            <?PHP
            else:?>
            <h5>Список перемещаемых средств:</h5>
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
            <a href="/item/index?mode=select" class="mt-2 btn btn-primary"><i class="fa fa-check-circle"></i> Перейти к выбору средств</a>
            <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>

            <?= $form->field($model, 'department_id_from')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(\app\models\Department::find()->all(),"id","name"),
                'options' => ['placeholder' => 'Выберите подразделение ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'disabled' => true
                ],
            ]); ?>


            <?= $form->field($model, 'department_id_to')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(\app\models\Department::find()->all(),"id","name"),
                'options' => ['placeholder' => 'Выберите подразделение ...','id'=>"depToSelect"],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]); ?>

            <?PHP
            $resps = [];
            if($model->department_id_to != null)
                $resps =\yii\helpers\ArrayHelper::map($model->departmentTo->responsibles,"id","fullName")
            ?>

            <?= $form->field($model, 'responsible_employee_id_to')->widget(Select2::classname(), [
                'data' => $resps,
                'options' => ['placeholder' => 'Выберите нового мат. ответственного ...','id'=>'respsSelect'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            <?PHP
                if(\app\models\User::isAdmin()):?>

                    <div class="bg-danger p-2"><input type='checkbox' name="adminTmpOption" value="1" id="tmpOpt" class="ml-2"> <label class="d-inline" for="tmpOpt"><span class="text-white">Оставить текущего мат.ответственного (Это временная администраторская опция)<small>В дальнейшем у каждого подразеделения должен быть мат.ответсвенный сотрудник которого нужно выбирать при перемещении</small></span></label></div>
                    <?PHP
                endif;
            ?>
            <?= $form->field($model, 'date')->widget(\kartik\date\DatePicker::className(),[

            ]) ?>
            <?= $form->field($model, 'description')->textarea(["placeholder"=>"Можете ввести дополнительную информацию, например номер приказа и дату приказа..."]) ?>



            <?= Html::submitButton('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
</svg> Переместить', ['class' => 'btn btn-success']) ?>
        </div>
    <?php
    ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<'JS'
$(function (){
    $(document).on('change',"#depToSelect",function (){           
            $("#respsSelect option").remove();
            $.ajax({
              url: "/item/get-list-responsibles",
              data:{
                  id: $("#depToSelect").val()
              },              
              type: 'GET',
              success: function(response) {
                  for(let id in response){
                      $("#respsSelect").append($(`<option value="${id}" >${response[id]}</option>`));                      
                  }
                  $("#respsSelect").val($("#respsSelect option:first").val());
              }
            });
    });
})
JS
);