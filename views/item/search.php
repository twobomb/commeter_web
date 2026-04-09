<?php

use kartik\growl\GrowlAsset;
use kartik\base\AnimateAsset;
use app\models\Item;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use kartik\select2\Select2;
use kartik\sidenav\SideNav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\web\JsExpression;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\ItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Средства';


\app\assets\TreeAsset::register($this);
\app\assets\InputFieldAsset::register($this);
GrowlAsset::register($this);
AnimateAsset::register($this);

$resps = [];
if(\app\models\User::isAdmin())
    $resps = \app\models\Employee::find()->where(["is_responsible"=>true])->all();
else {
    $deps = \Yii::$app->user->identity->getAccessDepartments(false);
    foreach ($deps as $d) {
        $resps = array_merge($resps, $d->unit->responsibles);
    }
}
$resps = ArrayHelper::map($resps,"id","fullName");



$tagsAll = \yii\helpers\ArrayHelper::map(Yii::$app->user->identity->tags,"id","name");

$fields = [

"searchTags"=>"Теги",
"employee_id"=>"Сотрудник",
  "id"=>"ID",
  "name"=>"Наименование",
  "inv_num"=>"Инвентарный номер",
  "workspace"=>"Местоположение",
  "responsible_employee_id"=>"Мат.отвественный",
  "category_id"=>"Категория",
  "department_id"=>"Подразделение",
  "stateTech"=>"Текущее состояние",
];
$defaults = ["name","inv_num","workspace",'responsible_employee_id','category_id','department_id'];
$activeFiedls = $defaults;
if(isset($_GET['ViewField']) && is_array($_GET['ViewField']))
    $activeFiedls = $_GET['ViewField'];

ob_start();
?>


<div class="viewFields"><span>Поля просмотра:</span>
    <?PHP


echo Select2::widget([
    'data' => $fields,
    'name'=>'features',
    'value'=>$activeFiedls,
    'id'=>"select2_field_view",
    'options' => ['placeholder' => 'Выберите поля которые будут отображать в таблице ...','multiple'=>true],
    'pluginOptions' => [
        'closeOnSelect' => false,
        'allowClear' => true,

    ],
]);?>
</div>

<div class="mt-2 mb-2" style="height: 40px;display: flex;flex-direction:row;    justify-content: space-between;">
   <div >
       <span style="margin-right: 10px">Выделено: <span class="countSel">0</span></span>
       <div class="btn-group selectContainer"  style="display:none;" role="group" aria-label="Button group with nested dropdown">
           <button type="button" class="btn btn-danger btnDelete"><i class="fa fa-trash"></i> Удалить выбранные</button>

           <div class="btn-group" role="group">
               <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                   <i class="fa fa-plus"></i>  Добавить в список ...
               </button>
               <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                   <li>
                       <a href="##" class="dropdown-item btnAddToTransfer">Добавить выбранные в список перемещения</a>
                   </li>
                   <a href="##" class="dropdown-item btnAddToReplaceCats">Добавить выбранные в список замены категории</a>
                   </li>
               </ul>
           </div>
       </div>
   </div>

	<div class="form-check showDepsCont">
	  <input class="form-check-input" type="checkbox" value="" id="showAllDeps" <?= $searchModel->showWithDeps != null?"checked":"" ?>>
	  <label class="form-check-label" for="showAllDeps">
		Показывать средства всех департаментов подразделния
	  </label>
	</div>

    <div class="emplNot">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" value="null" id="flexRadioDefault1" <?= $searchModel->typeShow == null?"checked":"" ?>>
            <label class="form-check-label" for="flexRadioDefault1">
                Показывать все
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" value="issued" id="flexRadioDefault2"  <?= $searchModel->typeShow == "issued"?"checked":"" ?> >
            <label class="form-check-label" for="flexRadioDefault2">
                Только выданные
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="flexRadioDefault" value="notissued" id="flexRadioDefault3"  <?= $searchModel->typeShow == "notissued"?"checked":"" ?> >
            <label class="form-check-label" for="flexRadioDefault3">
                Только не выданные
            </label>
        </div>
    </div>
</div>
<?PHP

$panelBefore =ob_get_contents();
ob_clean();


$employees = ArrayHelper::map((\app\models\Employee::find()->where(["in","department_id",\Yii::$app->user->identity->getAccessDepartments()])->all()),"id","fullName");


?>

<div class="item-search">
<?PHP
$techStateFeatId = -1;
if(\app\models\Feature::find()->where(["name"=>"Текущее состояние"])->one() != null)
    $techStateFeatId = \app\models\Feature::find()->where(["name"=>"Текущее состояние"])->one()->id;

$stateTechList = ArrayHelper::map(\app\models\DictinaryItem::find()->where(["dictinary_id"=>\app\models\Dictinary::find()->where(["name"=>"Текущее состояние"])->one()->id])->all(), 'id', 'value');
?>
            <?= GridView::widget([

                'panel' => [
                    'after' => false,
                    'heading' => '<i class="fas fa-archive"></i> Средства связи',
                    'type' => 'primary',
                    'before' => $panelBefore,
                ],
                'options'=>['class'=>'grid_items'],
                'hover' => true,
                'export' => [
                    'fontAwesome' => false
                ],
                'responsive' => true,
                'floatHeader' => true,
                'condensed'=>true,
                //'headerContainer' => ['class' => 'kv-table-header', 'style' => 'top: 50px'],
                'exportConfig' => [
                    'xls' => [],
                    'csv' => [],
                    'txt' => [],
                    'json' => [],
                ],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,

                'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
                'toolbar' =>  [
                    [
                        'content' =>
                            Html::a('<i class="fas fa-redo"></i>', ['/item/search'], [
                                'class' => 'btn btn-outline-secondary',
                                'title'=>"Сбросить фильтры",
                                'data-pjax' => 0,
                            ])
                            /*Html::a('<i class="fas fa-plus"></i> Добавить',"/item/create", [
                                'class' => 'btn btn-success'
                            ])*/,
                        'options' => ['class' => 'btn-group mr-2 me-2']
                    ],
                    '{export}',
                    '{toggleData}',
                ],
                'columns' => [
                    [
                        'header'=>'<div class="form-check fs-5 checkAll">
      <input class="form-check-input" type="checkbox" >
    </div>',
                        'format'=>'raw',
                        'value'=>function ($model, $key, $index,$column) {
                            return <<< EOF
    <div class="form-check fs-5 checkOne" data-id="$model->id">
      <input class="form-check-input" type="checkbox" >
    </div>
EOF;
                        },
                    ],
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'class' => '\kartik\grid\ExpandRowColumn',
                        'value'=>function ($model, $key, $index,$column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detailUrl'=>'/item/detail-item'
                    ],
                    [
                        'attribute' => 'id',
                        "hidden"=>!in_array('id',$activeFiedls),
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                            'attribute'=>'name',
                        "hidden"=>!in_array('name',$activeFiedls),
                            'format'=>'raw',
                        'value'=>function($m){
                            return Html::a("<i class='fa fa-edit'></i>",["/item/update","id"=>$m->id]).' '.$m->name;
                        }
                    ],
                    [
                            'attribute'=>'inv_num',
                        "hidden"=>!in_array('inv_num',$activeFiedls),
                        'headerOptions' => ['style' => 'width:180px'],
                    ],					
                    [
                            'attribute'=>'workspace',
                            'format'=>'raw',
                        "hidden"=>!in_array('workspace',$activeFiedls),
                        'value'=>function($m){
                            $text = $m->workspace;
                            if($m->employee != null)
                                $text = "<small style='color: #8d8d8d;'><i>{$m->employee->shortName}</i></small><br>".$text;
                            return $text;
                        },
                        'headerOptions' => ['style' => 'width:150px'],
                    ],
                    [
                        'attribute' => 'responsible_employee_id',
                        'filter' => $resps,
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => ''],
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        "value"=>function($v){
                            return $v->responsibleEmployee == null?null:$v->responsibleEmployee->fullName;
                        },
                        "hidden"=>!in_array('responsible_employee_id',$activeFiedls),
                    ],
                    [
                        'attribute' => 'employee_id',
                        'filter' => $employees,
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => ''],
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        "value"=>function($v){
							
                            return $v->employee === null ?null:$v->employee->fullName;
                        },
                        "hidden"=>!in_array('employee_id',$activeFiedls),
                    ],
                    [
                        'attribute' => 'category_id',
                        'filter' =>  ArrayHelper::map(\app\models\Category::find()->asArray()->all(), 'id', 'name'),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => ''],
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        "value"=>function($v){
                            return $v->category->name;
                        },
                        "hidden"=>!in_array('category_id',$activeFiedls),
                    ],
                    [
                        'attribute' => 'department_id',
                        'filter' =>  ArrayHelper::map(\Yii::$app->user->identity->getAccessDepartments(false), 'id', 'name'),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => ''],
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        "value"=>function($v){
                            return $v->department->name;
                        },
                        "hidden"=>!in_array('department_id',$activeFiedls),

                    ],
                    [
                        'attribute'=>'searchTags',
                        'label'=>"Теги",
                        'filter' =>  $tagsAll,
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ["multiple"=>true],
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'value'=>function($m){
                            return implode(",",ArrayHelper::getColumn($m->tags,"name"));
                        },
                        "hidden"=>!in_array('searchTags',$activeFiedls),
                        "filterOptions"=>["id"=>"fieldSearchTags"]
                    ],
                        [
                                'attribute' => 'stateTech',
                                'label'=>"Текущее состояние",
                                'filter' =>  $stateTechList,
                                'filterType' => GridView::FILTER_SELECT2,
                                'filterWidgetOptions' => [
                                        'options' => ['prompt' => ''],
                                        'pluginOptions' => ['allowClear' => true],
                                ],
                                "value"=>function($v) use ($techStateFeatId,$stateTechList) {
                                    $arr =  ArrayHelper::map($v->featureValues,"feature_id","value");

                                    if(isset($arr[$techStateFeatId]))
                                        return $stateTechList[$arr[$techStateFeatId]];
                                    return "";
                                },
                                "hidden"=>!in_array('stateTech',$activeFiedls),

                        ],

                ],
            ]); ?>

    <?PHP
$this->registerJs(<<<'JS'


function updateChecks(){
    $(document).off("change",".checkAll input",checkFunc);
    let c = $(".checkOne input:checked").length;
    $(".countSel").html(c);
    $(".selectContainer").css("display",(c === 0 ? "none":"inline"));
    let nc = $(".checkOne input:not(:checked)").length;
    if(c == 0){
        $(".checkAll input").prop("indeterminate",false);
        $(".checkAll input").prop("checked",false);
    }
    else if(c > 0 && nc > 0){
        $(".checkAll input").prop("indeterminate",true);
        $(".checkAll input").prop("checked",true);
    }else{
        $(".checkAll input").prop("indeterminate",false);
        $(".checkAll input").prop("checked",true);
    }
    $(document).on("change",".checkAll input",checkFunc);
}

function checkFunc(e){
    $(".checkOne input").prop("checked", $(".checkAll input").is(":checked"));
    let c = $(".checkOne input:checked").length;
    $(".countSel").html(c)
    $(".selectContainer").css("display",(c === 0 ? "none":"inline"));
}

$(function (){
    
    $("input[name=flexRadioDefault]").on("change",function (e){
        let url = new URL(window.location.href);
        let searchParams = new URLSearchParams(url.search);
        if(e.currentTarget.value != "issued" && e.currentTarget.value != "notissued")
            searchParams.delete("ItemSearch[typeShow]");
        else
            searchParams.set('ItemSearch[typeShow]', e.currentTarget.value);
        url.search = searchParams.toString();
        window.location.href =url.toString();
    });
	
    $("#showAllDeps").on("change",function (e){
        let url = new URL(window.location.href);
        let searchParams = new URLSearchParams(url.search);
        if(!e.currentTarget.checked)
            searchParams.delete("ItemSearch[showWithDeps]");
        else
            searchParams.set('ItemSearch[showWithDeps]', 1);
		
        url.search = searchParams.toString();
        window.location.href =url.toString();
    });
    
    $(document).on("click",".btnDelete",function (e){
        let c = $(".checkOne input:checked").length;
        if(c == 0)return;
        if(!confirm("Вы точно хотите удалить "+c + " выбранных средства?"))
            return;
        let ids = [];
        $(".checkOne input:checked").each((arr,el) =>{  ids.push($(el).parent().attr("data-id"))});
        $('body').append($('<div id="wait"><div><i class="fa fa-spinner"></i> Ждите...</div></div>'));
        $.ajax({
          type: 'POST',
          url: "/item/delete-selected",
          data: {"ids":ids},
          error:function (){
              alert("Ошибка");location.reload();
          },
          success:function (){
              location.reload();
          }
        });
    });
    $(document).on("click",".btnAddToTransfer, .btnAddToReplaceCats",function (e){
        e.preventDefault();
        let c = $(".checkOne input:checked").length;
        if(c == 0)return;
        let ids = [];
        $(".checkOne input:checked").each((arr,el) =>{  ids.push($(el).parent().attr("data-id"))});
        $('body').append($('<div id="wait"><div><i class="fa fa-spinner"></i> Ждите...</div></div>'));
        $.ajax({
          type: 'POST',
          url: "/item/add-selected-items",
          data: {"ids":ids},
          error:function (){
              alert("Ошибка");location.reload();
          },
          success:function (){
              location.reload();
          }
        });
    });
    $(document).on("change",".checkAll input",checkFunc);
    $(document).on("change",".checkOne input",function (e){
         updateChecks();        
    });
    $(document).on("change","#select2_field_view",function (){
        let opts = $("#select2_field_view").val();
        const url = new URL(window.location);
        url.searchParams.delete("ViewField[]");
        for (let i in opts)
            url.searchParams.append("ViewField[]", opts[i]); 
        window.history.pushState({}, '', url);
        location.reload();
    });
    
    
    
    
})
JS);
