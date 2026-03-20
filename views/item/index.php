<?php

use kartik\growl\GrowlAsset;
use kartik\base\AnimateAsset;
use app\models\Item;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use kartik\select2\Select2;
use kartik\sidenav\SideNav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\web\JsExpression;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\ItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Средства';
$this->params['breadcrumbs'][] = $this->title;


\app\assets\TreeAsset::register($this);
\app\assets\InputFieldAsset::register($this);
GrowlAsset::register($this);
AnimateAsset::register($this);



$mode = "view";

$GLOBALS["selectedItems"] = [];
if(isset($_GET["mode"]) && $_GET["mode"] == "select"){
    $mode = "select";
    $GLOBALS["selectedItems"] = \Yii::$app->session->get("SELECTED_TRANSFER_ITEMS",[]);
}

echo Dialog::widget([
    'libName' => 'dlgSelectDep', // a custom lib name
    'options' => [  // customized BootstrapDialog options
        'size' => Dialog::SIZE_LARGE, // large dialog text
        'type' => Dialog::TYPE_PRIMARY, // bootstrap contextual color
        'title' => 'Выбор подразделения',
        'id'=>"dlgSelectDep",
        'nl2br' => false,
        'buttons' => [
            [
                'id' => 'btnCancelSelectDep',
                'label' => 'Отмена',
                'cssClass' => 'btn-outline-secondary',
                'action' => new JsExpression("function(dialog) {
                    if (typeof dialog.getData('callback') === 'function' && dialog.getData('callback').call(this, false) === false) {
                        return false;
                    } 
                    return dialog.close();
                }")
            ],
            [
                'id' => 'btnConfirmSelectDep',
                'label' => 'Подтвердить',
                'cssClass' => 'btn-success'
            ]
        ]
    ]
]);

echo Dialog::widget([
    'libName' => 'dlgTransfer', // a custom lib name
    'options' => [  // customized BootstrapDialog options
        'size' => Dialog::SIZE_LARGE, // large dialog text
        'type' => Dialog::TYPE_PRIMARY, // bootstrap contextual color
        'title' => 'Перемещение средства',
        'id'=>"dlgTransfer",
        'nl2br' => false,
        'buttons' => [
            [
                'id' => 'btnConfirmTransfer',
                'label' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
</svg> Переместить',
                'cssClass' => 'btn-success'
            ]
        ]
    ]
]);
echo Dialog::widget([
    'libName' => 'dlgRepair', // a custom lib name
    'options' => [  // customized BootstrapDialog options
        'size' => Dialog::SIZE_LARGE, // large dialog text
        'type' => Dialog::TYPE_PRIMARY, // bootstrap contextual color
        'title' => 'Обслуживание средства',
        'id'=>"dlgRepair",
        'nl2br' => false,
        'buttons' => [
            [
                'id' => 'btnConfirmRepair',
                'label' => "<i class='fa fa-save'></i> Сохранить",
                'cssClass' => 'btn-success'
            ]
        ]
    ]
]);


echo Dialog::widget([
    'libName' => 'dlgAddEditItem', // a custom lib name
    'options' => [  // customized BootstrapDialog options
        'size' => Dialog::SIZE_LARGE, // large dialog text
        'type' => Dialog::TYPE_PRIMARY, // bootstrap contextual color
        'title' => 'Добавление\редактирование средства',
        'id'=>"dlgAddEditItem",
        'nl2br' => false,
        'buttons' => [
            [
                'id' => 'btnConfirmAddItem',
                'label' => "<i class='fa fa-save'></i> Сохранить",
                'cssClass' => 'btn-success'
            ]
        ]
    ]
]);



?>
<div class="item-index">


    <div class="showLeftMenu">
        <i class="fa fa-bars"></i>
        <i class="fa fa-caret-right"></i>
    </div>
    <div class="itemWrapper">
        <div class="leftMenuHint">
        </div>
        <div class="leftMenu">
            <div class="catsHeader">
                <h5><i class="fa fa-list-alt"></i> Категории</h5>
                <div class="hideLeftMenu">
                    <i class="fa fa-caret-left"></i>
                    <i class="fa fa-bars"></i>
                </div>
            </div>
            <div id="menuCats"  ></div>
        </div>
        <div class="item-content">


            <?php
            $tagsAll = \yii\helpers\ArrayHelper::map(Yii::$app->user->identity->tags,"id","name");
            Pjax::begin(["id"=>"pjax_items"]);
                $selCat = getSelectedCategory();
                $selDep = getSelectedDepartment();
                $lbl = "Подразделение";
                $selUnit = null;
                if($selDep != null && $selDep->unit->id != $selDep->id) {
                    $selUnit = $selDep->unit;
                    $lbl = "Департамент";
                }
                $panelBefore = '<div class="depPanel">

<span class="depCont">';
if($selUnit != null)
    $panelBefore .='
    <span class="curUnit" >Подразделение: '.($selUnit == null?"Не выбрано":$selUnit->name).'</span>';


     $panelBefore .='
    <span class="curDepLabel">'.$lbl.": ".($selDep == null?"Не выбрано":$selDep->name).'</span>
</span>
';
    $panelBefore .='
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="btnIsShowAll"  '.(isShowAllItems()?"checked":"").'>
      <label class="form-check-label" for="btnIsShowAll">Отображать средства всех департаментов подразделения</label>
    </div>';

$panelBefore.= '
<div class="containerDeppanel">
<button class="btn btn-primary selectDep"><i class="fa fa-building"></i> Выбрать подразделение</button>'.
                     \kartik\select2\Select2::widget([
                        'data' => $tagsAll,
                        'id'=>'tagSearchSelect',
                        'value'=>$searchModel->searchTags,
                        'name'=>"ItemSearch[searchtags]",
                        'options' => ['placeholder' => 'Выберите теги...','multiple'=>true],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
.'</div></div>';
            ?>
            <?= GridView::widget([

                'panel' => [
                    'after' => false,
                    'heading' => '<i class="fas fa-archive"></i> '.($selCat == null?"Средства связи":$selCat->name),
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
                            ($mode == "select"?
                                \yii\bootstrap5\Html::a('<i class="fas fa-backward"></i> Вернуться',getUrlBack(), [
                                    'class' => 'btn btn-warning'
                                ])  :
                            \yii\bootstrap5\Html::button('<i class="fas fa-plus"></i> Добавить', [
                                'class' => 'btn btn-success btnAddItem'
                            ])). ' '.
                            Html::a('<i class="fas fa-redo"></i>', ['/item/index'], [
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
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    'name',
                    [
                            'attribute'=>'inv_num',
                        'headerOptions' => ['style' => 'width:180px'],
                    ],
                    [
                            'attribute'=>'workspace',
                            'format'=>'raw',
                        'value'=>function($m){
                            $text = $m->workspace;
                            if($m->employee != null)
                                $text = "<small style='color: #8d8d8d;'><i>{$m->employee->shortName}</i></small><br>".$text;
                            return $text;
                        },
                        'headerOptions' => ['style' => 'width:150px'],
                    ],
                    [
                            'attribute'=>'searchTags',
                            'filter' =>  $tagsAll,
                            'filterType' => GridView::FILTER_SELECT2,
                            'filterWidgetOptions' => [
                                'options' => ["multiple"=>true],
                                'pluginOptions' => ['allowClear' => true],
                            ],
                            "hidden"=>true,
                            "filterOptions"=>["id"=>"fieldSearchTags"]
                    ],

                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, Item $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        },
                        'buttons'=>[
                            'update' => function ($url, $model, $key) {
                                return '<a class="linkEditItem" href="/item/update?id='.$model->id.'"  data-id="'.$model->id.'" title="Редактировать" aria-label="Редактировать" data-pjax="0"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg></button>';
                            },
                            'duplicate' => function ($url, $model, $key) {
                                return '<a class="duplicate" href="/item/duplicate?id='.$model->id.'"  data-id="'.$model->id.'" title="Дублирование" aria-label="Дублирование" data-pjax="0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
</svg></button>';
                            },
                            'move' => function ($url, $model, $key) {
                                return '<a class="move" href="/item/transfer?id='.$model->id.'"  data-id="'.$model->id.'" title="Перемещение" aria-label="Перемещение" data-pjax="0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
</svg></button>';
                            },
                            'repair' => function ($url, $model, $key) {
                                return '<a class="repair" href="/item/repair?id='.$model->id.'"  data-id="'.$model->id.'" title="Обслуживание" aria-label="Обслуживание" data-pjax="0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wrench" viewBox="0 0 16 16">
  <path d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z"/>
</svg></button>';
                            },
                            'checkbox' => function ($url, $model, $key) {
                                return '<div class="form-check form-switch">
                                      <input class="form-check-input fs-5 checkItem" '.(in_array($model->id,$GLOBALS["selectedItems"])?"checked":"").' type="checkbox" data-id="'.$model->id.'">
                                    </div>';
                            },
                        ],
                        'template' => ($mode == "select"?"{checkbox}":'<div class="btnCont">{duplicate} {move} {view}<br>{repair} {update} {delete}</div>')
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?PHP
$selDep = getSelectedDepartment();
$selCat = getSelectedCategory();

$selCat= $selCat  == null?"null": json_encode(["id"=>$selCat->id,"name"=>$selCat->name]);
$selDep= $selDep  == null?"null": json_encode(["id"=>$selDep->id,"name"=>$selDep->name]);

$jsCode  = <<<JS
let SELECTED_DEPARTMENT= $selDep;
let SELECTED_CATEGORY= $selCat;
JS;
$jsCode .= <<<'JS'

    let _countData = null;
    function updateCounts(refreshData = false){
        
        let updateFunc = function (){            
            $("#menuCats").find(".jstree-node").each((arr,el)=>{
                let id = $(el).attr("id");
                let cont = $(el).find("#"+id+"_anchor");
                $(cont).find("span").remove();
                let count = parseInt(_countData[id]);
                if(isNaN(count))count = 0;
                let empty = count == 0?" empty":"";
                $(cont).find(" i:first-child").after($("<span class='countSpan"+empty+"'>"+count+"</span>"));
            });
        }
        
        if(refreshData || _countData == null){
              $.ajax({
                  url: "/item/get-count-all",                  
                  type: 'GET',
                  success: function(response) {
                        _countData = response;
                        updateFunc();
                  }
                });
        }
        else
            updateFunc();
    }

$(function (){
    
    $(document).on("change",".checkItem",function (e){            
            $.ajax({
              url: "/item/select-item",
              data:{
                  id: $(e.currentTarget).attr("data-id"),
                  val: $(e.currentTarget).prop("checked")?"1":"0"
              },              
              type: 'GET',
              success: function(response) {
              }
            });
    });
    $(document).on("change","#btnIsShowAll",function (e){
        $.ajax({
          url: "/item/set-mode-show",
          data:{
              mode: $("#btnIsShowAll").prop("checked")?"1":"0"
          },
          
          type: 'GET',
          success: function(response) {
                updateItems();   
                updateCounts(true);
          }
        });
    });
    $(document).on("change","#tagSearchSelect",function (e){        
        $("#fieldSearchTags select").val($(e.currentTarget).val());
        $("#fieldSearchTags select").trigger("change");
        //updateItems();
        //$.pjax.reload('#pjax_items' , {timeout : false});
    });
    
    function updateFields(){
       $(".grid_items .kv-grid-table input").each((i,e)=>{
        if($(e).val() !== "" )
            $(e).addClass("inputFilterActive");
        else
            $(e).removeClass("inputFilterActive");
       }) 
    }
    $(document).on('pjax:success',updateFields);
    updateFields();
function notify(msg,type = "success"){
    let el = $.notify(msg, { allow_dismiss: true,type:type,delay:2000 });
         $(el.$ele).find("button").attr('style','background:none;    float: right;    border: none;    font-weight: bold; font-size:22px;    margin-top: -9px;');
}        
         

        $(document).on("click",".linkEditItem",function (e){
                e.preventDefault();
                
                let url = e.currentTarget.getAttribute("href")  + "&partial=1";
                let location = "item/update";
                openDialogAddEditItem(url,location);
        });




        $(document).on("click",".move",function (e){
                e.preventDefault();
                
                let url = e.currentTarget.getAttribute("href")  + "&partial=1";
                let location = "item/transfer";
                
                
      dlgTransfer.alert(
                            `<iframe
                                  id="inlineTransfer"
                                  src="${url}"
                                >
                                </iframe>`,
                            function(result) {}
                        );
               $("#dlgTransfer .bootstrap-dialog-footer-buttons button").eq(1).html("<i class='fa fa-ban'></i> Отмена");
                let iframe = $("#inlineTransfer").get(0);
                $(iframe).on("load",function (e){
                    if(!iframe.contentWindow.location.pathname.includes(location))
                        $("#dlgTransfer .bootstrap-dialog-footer-buttons button").eq(1).trigger("click");
                    else{
                        let form = $(iframe.contentDocument).find("#transferForm");
                        $("#btnConfirmTransfer").on("click",function (){
                            var formData = form.serialize();
                            $.ajax({
                              url: url,
                              type: 'POST',
                              data: formData,
                              success: function(response) {
                                if(response == "OK"){
                                    $(iframe).remove();                                
                                    $(iframe).off("load");
                                    $("#dlgTransfer .bootstrap-dialog-footer-buttons button").eq(1).trigger("click");
                                    notify("Сохранено");
                                    updateItems();
                                    updateCounts(true);
                                    
                                }
                                else{
                                     $("#btnConfirmTransfer").off("click");
                                    $(form).trigger("submit");
                                }
                              }
                            });
                        });
                    }
                })   
                
        });
        
        
        
        $(document).on("click",".repair",function (e){
                e.preventDefault();
                
                let url = e.currentTarget.getAttribute("href")  + "&partial=1";
                let location = "item/repair";
                
                
      dlgRepair.alert(
                            `<iframe
                                  id="inlineRepair"
                                  src="${url}"
                                >
                                </iframe>`,
                            function(result) {}
                        );
               $("#dlgRepair .bootstrap-dialog-footer-buttons button").eq(1).html("<i class='fa fa-ban'></i> Отмена");
                let iframe = $("#inlineRepair").get(0);
                $(iframe).on("load",function (e){
                    if(!iframe.contentWindow.location.pathname.includes(location))
                        $("#dlgRepair .bootstrap-dialog-footer-buttons button").eq(1).trigger("click");
                    else{
                        let form = $(iframe.contentDocument).find("#repairForm");
                        $("#btnConfirmRepair").on("click",function (){
                            var formData = form.serialize();
                            $.ajax({
                              url: url,
                              type: 'POST',
                              data: formData,
                              success: function(response) {
                                if(response == "OK"){
                                    $(iframe).remove();                                
                                    $(iframe).off("load");
                                    $("#dlgRepair .bootstrap-dialog-footer-buttons button").eq(1).trigger("click");
                                    notify("Сохранено");
                                    updateItems();
                                    updateCounts(true);
                                    
                                }
                                else{
                                     $("#btnConfirmRepair").off("click");
                                    $(form).trigger("submit");
                                }
                              }
                            });
                        });
                    }
                })   
                
        });

        $(document).on("click",".btnAddItem",function (){            
                            $.ajax({
                              url: "/item/can-add-to-category",
                              type: 'GET',
                              success: function(response) {
                                if(response !== "OK")
                                    notify("В данную категорию нельзя добавлять!","warning");
                                else
                                    openDialogAddEditItem("/item/create?partial=1","item/create");
                              }
                            });
        });

function openDialogAddEditItem(url,location){
      dlgAddEditItem.alert(
                            `<iframe
                                  id="inlineCreateItem"
                                  src="${url}"
                                >
                                </iframe>`,
                            function(result) {}
                        );
               $("#dlgAddEditItem .bootstrap-dialog-footer-buttons button").eq(1).html("<i class='fa fa-ban'></i> Отмена");
                let iframe = $("#inlineCreateItem").get(0);
                $(iframe).on("load",function (e){
                    if(!iframe.contentWindow.location.pathname.includes(location))
                        $("#dlgAddEditItem .bootstrap-dialog-footer-buttons button").eq(1).trigger("click");
                    else{
                        let form = $(iframe.contentDocument).find("#add_item_form");
                        $("#btnConfirmAddItem").on("click",function (){
                            var formData = form.serialize();
                            $.ajax({
                              url: url,
                              type: 'POST',
                              data: formData,
                              success: function(response) {
                                if(response == "OK"){
                                    $(iframe).remove();                                
                                    $(iframe).off("load");
                                    $("#dlgAddEditItem .bootstrap-dialog-footer-buttons button").eq(1).trigger("click");
                                    notify("Сохранено");
                                    updateItems();
                                    updateCounts(true);
                                    
                                }
                                else{
                                     $("#btnConfirmAddItem").off("click");
                                    $(form).trigger("submit");
                                }
                              }
                            });
                        });
                        
                    }
                })   
}
        dlgSelectDep.options.buttons[1].action = function(dialog) {            
                    if (typeof dialog.getData('callback') === 'function' && dialog.getData('callback').call(this, true) === false) {
                        return false;
                    }
                    
                    
                    let selId = $("#selectedDepInDlg").attr("data-id");
                    
                    let node = $('#treeSelectDep').jstree().get_node(selId);
                    if(node.data == null || node.data.allow != "allow"){
                        alert("Вам заперещено выбирать это подразделение, выберите другое!");
                        return 
                    }
                    if(selId != null){
                        selectDepartment(selId);
                    }
                    return dialog.close();
                 
        }
        $(document).on("click",".selectDep", function() {
                dlgSelectDep.dialog(
                        "<div id='treeSelectDep' ></div><div id='selectedDepInDlg'></div>",
                        function(result) {
                        }
                    );
                
                    $('#treeSelectDep').jstree({
                                'core' : {
                                    "multiple": false,
                                    "check_callback" : true,
                                    
                                    'data' : {
                                        //"url" : "./root.json",
                                        //"dataType" : "json" // needed only if you do not supply JSON headers
                    
                                        "url" : "/department/get-json-collection",
                                        "data" : function (node) {
                                            return { "id" : node.id ,"position":node.position};
                                        }
                                    }
                                },
                                "state": {
                                    "key":"deps"  
                                },
                                "types" : {
                                    "#" : { "max_children" : 1, "max_depth" : 4, "valid_children" : ["root"] },
                                    "root" : {  "valid_children" : ["default","print"] },
                                    "default" : { "valid_children" : ["print"] },
                                    "print" : { "valid_children" : ["print"] }
                                },
                                "plugins" : [ "types","state"]
                    
                    
                            })
                                .on("select_node.jstree", function (obj,c) {
                                    let id = c.node.id;
                                    $("#selectedDepInDlg").html("Выбрано: "+c.node.text);
                                    $("#selectedDepInDlg").attr("data-id",id);
                                })
                                .on("open_node.jstree",function (a,obj){
                                    for (let i in obj.node.children){
                                        let id = obj.node.children[i];
                                        let node = $('#treeSelectDep').jstree().get_node(id);
                                        if(node.data != null && node.data.allow != "allow"){
                                            $("#treeSelectDep #"+node.a_attr.id).css("background","#ffc9c9");
                                        }
                                    }
                                     
                                });
        });
    
        let menuW = 0;
        $(".hideLeftMenu").on("click",function (){
            menuW = $(".leftMenuHint").css("width");
            $(".leftMenuHint").css("width",0);
            $(".showLeftMenu").show("fast");      
            $('.item-content').addClass('menuHided');  
        });
        $(".showLeftMenu").on("click",function (){
            $(".leftMenuHint").css("width",menuW);
            $(".showLeftMenu").hide("fast");    
            $('.item-content').removeClass('menuHided')
        })        
});


function selectDepartment(id_dep){
                if(SELECTED_DEPARTMENT  != null && SELECTED_DEPARTMENT.id == id_dep)return;
                $.ajax({
                url:"/item/select-dep",
                data:{
                    id:id_dep
                },
                error:function(err){
                    alert("Ошибка выбора подразделения");
                },
                success:function (data) {
                    SELECTED_DEPARTMENT = data;
                    updateItems();
                    updateCounts(true);
                }
            });
}
function updateItems(){
    $.pjax.reload('#pjax_items' , {timeout : false});
}
function selectCategory(id_cat){
                if(id_cat == -1 || (SELECTED_CATEGORY != null && SELECTED_CATEGORY.id == id_cat))return;
    
                $.ajax({
                url:"/item/select-category",
                data:{
                    id:id_cat
                },
                error:function(err){
                    alert("Ошибка выбора категории");
                },
                success:function (data) {
                    SELECTED_CATEGORY = data;
                    updateItems();
                }
            });
}

$('#menuCats').jstree({
            'core' : {
                "multiple": false,
                "check_callback" : true,                
                'data' : {
                    //"url" : "./root.json",
                    //"dataType" : "json" // needed only if you do not supply JSON headers

                    "url" : "/category/get-json-collection",
                    "data" : function (node) {
                        return { "id" : node.id ,"position":node.position};
                    }
                }
            },
            "types" : {
                "#" : { "max_children" : 1, "max_depth" : 4, "valid_children" : ["root"] },
                "root" : {  "valid_children" : ["default","print"] },
                "default" : { "valid_children" : ["print"] },
                "print" : { "valid_children" : ["print"] }
            },
            "plugins" : [ "search", "types","state"]


        }).on("open_node.jstree",function (){
                updateCounts()
            })
            .on("select_node.jstree", function (obj,c) {
                let id = c.node.id;
                selectCategory(id);
            });
JS;

$this->registerJs($jsCode);
?>

