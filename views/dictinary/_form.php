<?php

use kartik\sortable\Sortable;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Dictinary $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="dictinary-form">


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>


    <?PHP

    $items = [];
    foreach ($model->dictinaryItems as $v){
        $items[]=['content'=>"<input type='hidden' name='DictinaryItem[$v->id]' value='$v->sort_id'><span>$v->value</span><span class='delBtndi'><i class='fa fa-trash'></i></span>"];
    }


    echo \yii\bootstrap5\Html::button("<i class='fa fa-plus'></i> Добавить значение",["id"=>"addBtn","class"=>"btn btn-primary"]);
    echo Sortable::widget([
        'items'=>$items,
        'options'=>["id"=>"list_dicts","class"=>"mt-3"],
        'pluginEvents' => [
            'sortupdate' => 'function(e) {
                let n = 1;
                $("#list_dicts > li").each(function(e,el){
                    $(el).find("input").val(n++);
                });
              }',
        ]
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

$this->registerJs(<<<'JS'
function updateSort(){
        let n = 1;
        $("#list_dicts > li").each(function(e,el){
            $(el).find("input").val(n++);
        });
    }
    $("#list_dicts").on("click",".delBtndi",function (e){
        let nde = e.currentTarget.parentNode;
        $(nde).hide("slow",function (){
            $(nde).remove();
        });
    });
    $("#addBtn").on("click",function (){
        var val = prompt("Введите значение").trim();
        if(val === "")
            return;

        $el = $("<li class='newDiEl' draggable=\"true\" role=\"option\" aria-grabbed=\"false\"><input type='hidden' name='NewDictinaryItem["+val+"]' value='1'><span >"+val+"</span><span class='delBtndi'><i class='fa fa-trash'></i></span></li>");
        
        $("#list_dicts").prepend($el);
        updateSort();
    })
JS
)
?>
