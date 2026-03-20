<?php

use app\models\Category;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */


\app\assets\TreeAsset::register($this);
\app\assets\InputFieldAsset::register($this);

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;


?>
<h1><?=$this->title?></h1>


<div class="fluid_menu_right mt-2">
    <button id="addBtn" class="treebtn btn-md btn-primary"><i class="fa fa-plus"></i> Добавить</button>
    <button id="renameBtn" class="treebtn btn-md btn-secondary"><i class="fa fa-edit"> Переименовать</i></button>
    <button id="deleteBtn" class="treebtn btn-md btn-danger"><i class="fa fa-trash"> Удалить</i></button>
</div>

<div class="row">

    <div id="ajax"  class="col-6"></div>
    <div class="col-6">

        <h3 class="labelFeat">Привязанные свойства:</h3>

        <input type="hidden" id="currentCat">
    <?= Select2::widget([
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Feature::find()->all(),"id","name"),
        'name'=>'features',
        'id'=>"select2_feature",
        'options' => ['placeholder' => 'Выберите связанные свойства ...','multiple'=>true],
        'pluginOptions' => [
                'closeOnSelect' => false,
            'allowClear' => true,

        ],
    ]);
    ?>
        <button class="btn btn-success mt-2" id="btn_save_feat">Сохранить измениния свойств</button>

        <div class="form-check form-switch p-0 mt-3 fs-6">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
            <label class="form-check-label ml-2" style="position:relative;top:-6px;left:5px;" for="flexSwitchCheckChecked">Разрешено добавление средств в категорию</label>
        </div>
    </div>
</div>

<script>
    $(function () {

        $("#flexSwitchCheckChecked").on("change",function (e){
            $.ajax({
                url:"/category/set-can-add",
                data:{
                    id:$("#flexSwitchCheckChecked").val(),
                    val:$("#flexSwitchCheckChecked").prop("checked")?"1":"0"
                },
                error:function(err){
                    alert("Ошибка изменения");
                },
                success:function (data) {
                    if(data != "OK")
                        alert("Ошибка изменения");
                }
            });
        });
        ///ДОКУМНТАЦИЯ https://www.jstree.com/api/

        $("#btn_save_feat").on("click",function() {
            let id = $("#currentCat").val();
            if(!confirm("Вы уверены что хотите изменить свойства? Если у средств были введены какие-то значения для свойств которые например вы удалили, эти значения навсегда будут утеряны. Продолжить?")){
                return;
            }

            $.ajax({
                url:"/category/link-features",
                method:"post",
                data:{
                    id:id,
                    feats:$("#select2_feature").val()
                },
                error:function(err){
                    alert("Ошибка изменения свойств");
                },
                success:function (data) {
                    if(data === "OK"){
                        $(".select2-selection").css("background-color", "#d2ffbc").fadeTo("fast", 0.5).fadeTo("fast", 1,function (){
                            $(".select2-selection").css("background-color", "#FFF");
                        });
                    }else{
                        $(".select2-selection").css("background-color", "red").fadeTo("fast", 0.5).fadeTo("fast", 1,function (){
                            $(".select2-selection").css("background-color", "#FFF");
                        });
                    }
                }
            });
        });
        $("#deleteBtn").on("click",function(){

            var ref = $('#ajax').jstree(true),
                sel = ref.get_selected();
            if(!sel.length  || sel[0] == -1) { return false; }
            sel = sel[0];
            if(!confirm("Вы уверены что хотите удалить категорию '"+ref.get_selected(true)[0].text+"'?"))return;
            var win = window.open('/category/delete?id='+sel, '_blank');
        });
        $("#renameBtn").on("click",function(){

            var ref = $('#ajax').jstree(true),
                sel = ref.get_selected();

            if(!sel.length || sel[0] == -1) { return false; }

            sel = sel[0];
            ref.edit(sel);
        });

        $("#addBtn").on("click",function(){
            var ref = $('#ajax').jstree(true);

            var sel = ref.get_selected();
            var _t = ref.create_node(sel, {"id":-2,"text":'test',"type":"print"});
            if(!_t){alert("Максимальный уровень вложения - 3");return;}
            else
                ref.delete_node(_t);

            if(sel.length > 0){
                sel = sel[0];

                pos = ref.get_node(sel,false).children.length;
                if(!pos)
                    pos = 1;

                $.ajax({
                    url:"/category/add",
                    data:{
                        id:sel,
                        name:"новая категория",
                        position:pos
                    },
                    error:function(err){
                        alert("Ошибка создания категории");
                    },
                    success:function (data) {
                        data = JSON.parse(data);
                        var s = ref.create_node(sel, {"id":data.id,"text":data.name,"type":"print"});
                        if(s)
                            ref.edit(s);
                    }
                });
            }
        });
        // ajax demo333
        $('#ajax').jstree({
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
            "plugins" : [ "search", "types","state","dnd"]


        })
            .on("select_node.jstree", function (obj,c) {
                let id = c.node.id;

                $("#currentCat").val(id);
                $("#flexSwitchCheckChecked").val("");
                if(id < 0){
                    $(".labelFeat").html("Привязанные свойства:");
                    $("#select2_feature").val( []).trigger('change');
                    return;
                }
                $.ajax({
                    url:"/category/get-features",
                    data:{
                        id:id
                    },
                    error:function(err){
                        alert("Ошибка получения привязанных свойств");
                    },
                    success:function (data) {
                        $("#select2_feature").val( data).trigger('change');

                    }
                });
                $.ajax({
                    url:"/category/get-info",
                    data:{
                        id:id
                    },
                    error:function(err){
                        alert("Ошибка получения информации");
                    },
                    success:function (data) {
                        $("#flexSwitchCheckChecked").prop("checked",data.is_can_add);
                        $("#flexSwitchCheckChecked").val(data.id);
                        $(".labelFeat").html("Привязанные свойства '"+data.name+"':");

                    }
                });

            })
            .on("move_node.jstree", function (obj, newpar) {
                //console.log(newpar)
                var moved_node = newpar.node.id;
                var parent = newpar.parent;
                var position = newpar.position;
                $.ajax({
                    url:"/category/change-parent",
                    data:{
                        parent:parent,
                        category:moved_node,
                        position:position
                    },
                    error:function(err){
                        // alert("Ошибка перемещения категории.");
                        // window.location =window.location;
                    }
                });

            }).on("changed.jstree", function (e, data) {

        }).on("rename_node.jstree", function (e, data) {
                $.ajax({
                    url:"/category/rename",
                    data:{
                        id:data.node.id,
                        name:data.text
                    },
                    error:function(err){
                        alert("Ошибка создания категории");
                        window.location =window.location;
                    }
                });
            }
        );


        //$('#ajax').jstree(true).set_theme("mytheme","../../dist/themes/default-dark/style.min.css");
    });

</script>
