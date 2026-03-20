<?php
use kartik\select2\Select2;
use app\models\Category;
use yii\helpers\ArrayHelper;
?>

<script src="/js/xlsx.full.min.js"></script>
<input type="file" id="fileInput"  />

<div class="alert alert-info" role="alert">
  Excel файл должен содержать в 1 колонка инв.номер, 2 наименование, 3 сумма, 4 местоположение, 5 кол-во. Поля могут быть пустыми
</div>
<button class="btn btn-primary addBtn"><i class="fa fa-plus"></i> Добавить из excel файла</button>
<button class="btn btn-primary addBtnRow"><i class="fa fa-plus"></i> Добавить пустую строку</button>

<?php
\kartik\form\ActiveForm::begin();
?>

<h6>Выберите подразделение в которое добавить:</h6>
<?php
			echo \kartik\select2\Select2::widget([
			"name"=>"department_id",
			"options"=>["id"=>"depToSelect",'required'=>true],
			"data"=>ArrayHelper::map( \Yii::$app->user->identity->getAccessDepartments(false),"id","name")
			]);
			?>
    <h6>Выберите мат.ответственного:</h6>
<?php

			echo \kartik\select2\Select2::widget([
			"name"=>"responsible_employee_id",
			'options' => ['placeholder' => 'Выберите нового мат. ответственного ...','id'=>'respsSelect','required'=>true],
			"data"=>ArrayHelper::map( \Yii::$app->user->identity->getAccessDepartments(false),"id","name")
			]);
			?>
			<br>

<div class="panelHeader">
    <span><b>Выделено: <span class="countSel">0</span></b></span>
    <span class="selectContainer" style="display: none">
        <button type="button" class="btn btn-danger btnDeleteSel"><i class="fa fa-trash"> </i> Удалить выделенные</button>
    <span class="changecatcont">
        <b>Изменить категорию выделенных</b>
        <?php
        echo \kartik\select2\Select2::widget([
            "name"=>"change__category",
            'options' => ['placeholder' => 'Выберите категорию ...','id'=>'selectChangeCat'],
            'pluginOptions'=>["allowClear"=>true],
            "data"=>ArrayHelper::map( Category::find()->where(["is_can_add"=>1])->all(),"id","name")
        ]);
        ?>
        <button type="button" class="btn btn-success btnCangeCatSel">Применить</button>
    </span>
    </span>
</div>
<table class="t-table">
	<tr class="t_row t-header">
        <th class="t_col t_num"><span>#</span></th>
		<th class="t_col t_check">
			<div class="form-check fs-5">
			  <input class="form-check-input" type="checkbox" >
			</div>
		</th>
		<th class="t_col t_cat">Категория</th>
		<th class="t_col t_inv">Инв.номер</th>
		<th class="t_col t_name">Наименование</th>
		<th class="t_col t_sum">Сумма</th>
		<th class="t_col t_work">Местоположение</th>
		<th class="t_col t_cnt">Кол-во</th>
		<th class="t_col" style="text-align: center"><i class="fa fa-trash"></i></th>
	</tr>
	<tr class="t_row">
        <td class="t_col t_num"><span>1</span></td>
		<td class="t_col t_check">
			<div class="form-check fs-5">
			  <input class="form-check-input" type="checkbox" >
			</div>
		</td>
		<td class="t_col t_cat">
            <select name="category[]" class="selectCat">
                <?PHP
                    foreach (ArrayHelper::map( Category::find()->where(["is_can_add"=>1])->all(),"id","name") as $id=>$name)
                        echo "<option value='$id'>$name</option>";
                ?>
            </select>

		</td>
		<td class="t_col t_inv"><input  type="text"  name="inv[]"> </td>
		<td class="t_col t_name"><input  type="text"  name="name[]"> </td>
		<td class="t_col t_sum"><input  type="number" step="any"  name="sum[]"> </td>
		<td class="t_col t_work"><input  type="text"  name="work[]"> </td>
        <td class="t_col t_cnt"><input  type="number" value="1" name="count[]" > </td>
		<td class="t_col t_delete"><span class="delItem"><i class="fa fa-times"></i></span></td>
	</tr>
</table>

<button type="submit" class="btn btn-success mt-4 m-auto"><i class="fa fa-check"></i> Добавить</button>
<?php
\kartik\form\ActiveForm::end();
?>
<?php

//Поля
//inv_num, category_id, workspace, name,  sum

$this->registerCSS(<<<'CSS'
.changecatcont{

    display: inline-block;
    background: #cdcdcd;
    padding: 5px 10px;
    border-radius: 10px;
}
.panelHeader{
    height: 68px;
    padding: 10px;
    background: #e4e4e4;
}
#selectChangeCat + .select2{
width: 250px;
display: inline-block;
}
#selectChangeCat{
display: none;
}
.t-table .t_num{
    width: 30px;
}
.t-table .t_num span{
    display: inline-block;
}
.t-table .t_check {
    width: 50px;
}
.t-table .t_check .form-check{
    
    margin: 0px 29px 0 0;
    padding: 0px 0px 0px 0px;
    position: relative;
    left: 40px;
}
.t-table tr:first-child {
background: #cdcdcd;
}
.t-table tr:first-child th{
padding-bottom: 10px;
text-align: center;
}
.t-table {
    border-collapse: collapse;
    width: 100%;
    background: #dfdfdf;
}

.t-table  .t_name input,
.t-table  .t_inv input,
.t-table  .t_sum input,
.t-table  .t_cnt input,
.t-table  .t_work input{
    border: none;
    height: 38px;
    width: 100%;
    background: transparent;
}
.t-table  .t_cnt{
    width: 50px;
}
.t-table  .t_sum{
    width: 100px;
}
.t-table  tr:not(.t-header):hover {
    background: #eaeaea;
}
.t-table  .t_delete:hover{
    background: #ef4343;
    cursor: pointer;
    color: white;
}
.t-table  .t_delete{
    width: 30px;
    text-align: center;
}
.t-table  .t_inv{
width: 200px;
}
.t-table  .t_name input{
    font-size: 12px;
    font-weight: bold;
}
.t-table  .t_work{
    width: 200px;
}
.t-table  .t_name,
.t-table  .t_inv,
.t-table  .t_sum,
.t-table  .t_work{

}
.t-table td{
    border: 1px solid #8f8f8f;
}
.t_col.t_cat .select2{
	width:250px !important;
}
.t_col.t_cat{
	width:250px;
}
#fileInput{
display: none;
}
.addBtn{
margin: 20px;
}
CSS);


$this->registerJS(<<<'JS'
let uid = 1;
let rowPrefab = "";
function addRow(arrData){
	let  row = $(rowPrefab).clone();
	$(".t-table").append(row);
	$(row).find("select").select2();
	$(row).find("td:nth-child(4) input").val(arrData[0]);
	$(row).find("td:nth-child(5) input").val(arrData[1]);
	$(row).find("td:nth-child(6) input").val(arrData[2]);
	$(row).find("td:nth-child(7) input").val(arrData[3]);
	$(row).find("td:nth-child(8) input").val(arrData[4]);
}

function updateChecks(){
    $(document).off("change",".t-header .t_check input",checkFunc);
    let c = $(".t_row:not(.t-header) .t_check input:checked").length;
    $(".countSel").html(c);
    $(".selectContainer").css("display",(c === 0 ? "none":"inline"));
    let nc = $(".t_row:not(.t-header) .t_check input:not(:checked)").length;
    if(c == 0){
        $(".t-header .t_check input").prop("indeterminate",false);
        $(".t-header .t_check input").prop("checked",false);
    }
    else if(c > 0 && nc > 0){
        $(".t-header .t_check input").prop("indeterminate",true);
        $(".t-header .t_check input").prop("checked",true);
    }else{
        $(".t-header .t_check input").prop("indeterminate",false);
        $(".t-header .t_check input").prop("checked",true);
    }
    $(document).on("change",".t-header .t_check input",checkFunc);
}
function updateNums(){
    let i = 1;
    $(".t-table .t_row:not(.t-header)").each(function (arr,el){
        $(el).find(".t_num span").html(i++);
    })    
}

function checkFunc(e){
    $(".t_row:not(.t-header) .t_check input").prop("checked", $(".t-header .t_check input").is(":checked"));
    let c = $(".t_row:not(.t-header) .t_check input:checked").length;
    $(".countSel").html(c)
    $(".selectContainer").css("display",(c === 0 ? "none":"inline"));
}
$(function(){
    $(".btnCangeCatSel").on("click",function (){
        if($("#selectChangeCat").val() == null || $("#selectChangeCat").val() == "") 
                return;
         $(".t_row:not(.t-header) .t_check input:checked").each(function (arr,el){
             $(el).parents(".t_row").find("select").val($("#selectChangeCat").val());
             $(el).parents(".t_row").find("select").trigger("change");
         });
         updateNums();
         updateChecks();
    });
    
    
    $(".btnDeleteSel").on("click",function (){
        
        $(".t_row:not(.t-header) .t_check input:checked").each(function (arr,el){
             $(el).parents(".t_row").remove();
         });
             updateNums();
             updateChecks();
    });
    
    $(document).on("change",".t-header .t_check input",checkFunc);
    $(document).on("change",".t_row:not(.t-header) .t_check input",function (e){
         updateChecks();        
    });
    $(document).on("click",".t_delete",function (e){
        let del = $(e.currentTarget).parent();
       del.fadeOut(function (e){
           $(del).remove();
           updateNums()
           updateChecks()
       }) 
    });
    rowPrefab = $(".t_row:last-child").clone();
    $(".t_row:last-child").remove();
    
	$(".addBtn").on("click",function (){
	    $("#fileInput").trigger("click")
	})
	$(".addBtnRow").on("click",function (){
	    addRow(["","","","",1])
	})
	document.getElementById('fileInput').addEventListener('change', function(event) {
		const file = event.target.files[0];
		const reader = new FileReader();
		reader.onload = function(e) {
			const data = new Uint8Array(e.target.result);
			const workbook = XLSX.read(data, { type: 'array' });
			const sheetName = workbook.SheetNames[0];
			const sheet = workbook.Sheets[sheetName];
			const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

			for (let i  in rows){
			    let len = Math.min(5,rows[i].length);
			    let data = ["","","","",1];
			    for (let j = 0; j < len;j++){
			        data[j] = rows[i][j];
			        if(j == 4 && isNaN(parseInt(data[j])))
			            data[j]= 1;
			    }
			    if(data[0] == "" && data[1] == "" && data[2] == "" && data[3] == "")
			        continue;
			    addRow(data);
			}
			updateNums()
			updateChecks()
			$("#fileInput").val('')
		};

		reader.readAsArrayBuffer(file);
	});
	
	//$(".js-example-basic-single").select2()
	
	
	
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
	
	$("#depToSelect").trigger('change');
	
	
	
});

JS);



