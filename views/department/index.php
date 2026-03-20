<?php

use app\models\Department;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\DepartmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

\app\assets\TreeAsset::register($this);
\app\assets\InputFieldAsset::register($this);

$this->title = 'Подразделения';
$this->params['breadcrumbs'][] = $this->title;


?>
<h1><?=$this->title?></h1>
<div class="fluid_menu_right">
    <button id="addBtn" class="treebtn btn-md btn-primary"><i class="fa fa-plus"></i> Добавить</button>
    <button id="renameBtn" class="treebtn btn-md btn-secondary"><i class="fa fa-edit"> Переименовать</i></button>
    <button id="deleteBtn" class="treebtn btn-md btn-danger"><i class="fa fa-trash"> Удалить</i></button>
</div>
<div class="row">

    <div id="ajax" style="overflow: auto;"  class="col-6"></div>
    <div class="col-6 props">
        <h5>Свойства '<span class="depName"></span>':</h5>

        <div class="alert alert-info" role="alert">
            Если выбранное подразделение является отделом\управлением, поставьте галку это департамент
        </div>

        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
            <label class="form-check-label" for="flexSwitchCheckChecked">Это департамент</label>
        </div>
        <a class="btn btn-primary mt-2 markAsDep" href="/department/mark-children">Пометить все вложенные как департаменты</a>
        <a class="btn btn-primary mt-2 markAsNoDep" href="/department/mark-children">Пометить все вложенные как подразделения</a>
    </div>
</div>
<script>
    $(function () {


        $("#flexSwitchCheckChecked").on("change",function (e){
            $.ajax({
                url:"/department/set-dep",
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

        $("#deleteBtn").on("click",function(){


            var ref = $('#ajax').jstree(true),
                sel = ref.get_selected();
            if(!sel.length  || sel[0] == -1) { return false; }
            sel = sel[0];
            if(!confirm("Вы уверены что хотите удалить подразделение?"))return;
            var win = window.open('/department/delete?id='+sel, '_blank');

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
                    url:"/department/add",
                    data:{
                        id:sel,
                        name:"новое подразделение",
                        position:pos
                    },
                    error:function(err){
                        alert("Ошибка создания подразделения");
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

                    "url" : "/department/get-json-collection",
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
                if(id == "-1") {
                    $(".props").hide();
                    return;
                }
                $(".props").show();
                $.ajax({
                    url:"/department/get-info",
                    data:{
                        id:id
                    },
                    error:function(err){
                        //alert("Ошибка получения привязанных свойств");
                    },
                    success:function (data) {
                        $("#flexSwitchCheckChecked").prop("disabled",data.parent_id == null);
                        $("#flexSwitchCheckChecked").prop("checked",data.is_department);
                        $("#flexSwitchCheckChecked").val(data.id);
                        $(".depName").html(data.name);
                        $(".markAsDep").attr("href","/department/mark-children?id="+data.id+"&is_department=1");
                        $(".markAsNoDep").attr("href","/department/mark-children?id="+data.id+"&is_department=0");
                    }
                });

            })
            .on("move_node.jstree", function (obj, newpar) {
                //console.log(newpar)
                var moved_node = newpar.node.id;
                var parent = newpar.parent;
                var position = newpar.position;
                $.ajax({
                    url:"/department/change-parent",
                    data:{
                        parent:parent,
                        category:moved_node,
                        position:position
                    },
                    error:function(err){
                         alert("Ошибка перемещения категории.");
                        location.reload();
                        // window.location =window.location;
                    }
                });

            }).on("changed.jstree", function (e, data) {

        }).on("rename_node.jstree", function (e, data) {
                $.ajax({
                    url:"/department/rename",
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
