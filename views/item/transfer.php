<?php

use app\assets\AppAsset;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = "Перемещение средства '$item->name'";
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
AppAsset::register($this);
?>
<div class="item-view">

    <h5>Перемещение средства '<?= $item->name ?>', инвентарный номер '<?= $item->inv_num ?>' внутри подразделения '<?= $item->department->name ?>'</h5>


    <div class="alert alert-info" role="alert">
        <i class="fa fa-info-circle"></i> Если вам нужно переместить средства в другое подразделение, используйте функционал <b>Дополнительно => Массовое перемещение</b>
    </div>
    <?php $form = ActiveForm::begin(["id"=>"transferForm"]); ?>
    <?=  $form->errorSummary($model); ?>
    <?= $form->field($item, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
    <div class="trasnFrom">
    <?= $form->field($model, 'department_id_from')->widget(Select2::classname(), [
            'data' => [$model->departmentFrom->id=>$model->departmentFrom->name],
            'options' => ['placeholder' => 'Выберите подразделение ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'disabled'=>true
            ],
        ]);?>
    <span>От сотрудника</span>
    <?= Select2::widget([
            'name'=>'employee_from_id',
            'data' => $item->employee==null?[]:[$item->employee->id =>$item->employee->fullName],
            'value'=>$item->employee_id,
            'options' => ['placeholder' => 'Не выбрано'],
            'pluginOptions' => [
                'allowClear' => true,
                'disabled'=>true
            ],
        ]);?>

    <?= $form->field($model, 'workplace_from')->textInput(["readonly"=>true,'disabled'=>true]) ?>

    </div>
    <div class="transArrow">
        <i class="fa fa-arrow-alt-down"></i>
    </div>
    <div class="trasnTo">
    <?PHP
        $deps = \app\models\Department::find()->where(["in",'id',$model->departmentFrom->unit->getIdsAllUnitDepartmentsWithUnit()])->all();
        if(count($deps) > 1) {
            echo $form->field($model, 'department_id_to')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map($deps, "id", "name"),
                'options' => ['placeholder' => 'Выберите подразделение ...','id'=>"transDepTo"],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        }
        else{
            echo $form->field($model, 'department_id_to')->hiddenInput()->label(false);
        }
            ?>
        <?= $form->field($model, 'employee_to_id')->widget(Select2::classname(), [
            'data' =>  \yii\helpers\ArrayHelper::map( $item->department->employees,"id","fullName"),
            'options' => ['placeholder' => 'Выберите сотрудника ...',"id"=>"transEmployee"],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);?>
    <?= $form->field($model, 'workplace_to') ?>
    </div>
    <?= $form->field($model, 'date')->widget(\kartik\date\DatePicker::className(),[

    ]) ?>
    <?= $form->field($model, 'description')->textarea(["placeholder"=>"Можете ввести дополнительную информацию, например номер приказа и дату приказа..."]) ?>


    <?PHP
    if(!$is_partial):
        ?>
        <div class="form-group">
            <?= Html::submitButton('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
</svg> Переместить', ['class' => 'btn btn-success']) ?>
        </div>
    <?php
    endif;
    ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<'JS'
$(function (){
	let _func1 = function (){
        $.ajax({
          url: "/department/get-employees",
          data:{
              id:$("#transDepTo").val()
          },                  
          type: 'GET',
          success: function(response) {
				let cVal = $("#transEmployee").val();
                $("#transEmployee").val("");
                $("#transEmployee option").remove();
                for (let id in response){                    
                    $("#transEmployee").append($(`<option value="${id}">${response[id]}</option>`));                
                }
                $("#transEmployee").val(cVal);                
          }
        });
    };
    $(document).on('change',"#transEmployee",function (){
        $.ajax({
          url: "/employee/get-info",
          data:{
              id:$("#transEmployee").val()
          },                  
          type: 'GET',
          success: function(response) {
			  if($("#transDepTo").val() == "" && response != null){
				  $("#transDepTo").val(response.department_id);
				  $("#transDepTo").trigger("change");
			  }
			  
              $("#transfer-workplace_to").val('');
              if(response.cabinet != null && response.cabinet != "")
                $("#transfer-workplace_to").val("каб."+response.cabinet);
                
          }
        });
    });
    $(document).on('change',"#transDepTo",_func1);
})
JS);