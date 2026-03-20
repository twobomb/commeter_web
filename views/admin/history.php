<?php

$this->title = "Истории";

?>
    <form action="/admin/history" method="post">
        <h5>Поиск по истории</h5>

        <input type="hidden" name="_csrf" id="" value="<?= Yii::$app->request->getCsrfToken() ?>">
        <div class="alert alert-info" role="alert">
            Все поля не являются обязательными, можно сделать частичный ввод искомого значения
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text" >Подразделение</span>
            <input type="text" class="form-control" placeholder="Подразделение" name="dep" value="<?=Yii::$app->request->post("dep","")?>"  aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Категория</span>
            <input type="text" class="form-control" placeholder="Категория" value="<?=Yii::$app->request->post("cat","")?>" name="cat" aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Название</span>
            <input type="text" class="form-control" placeholder="Название" value="<?=Yii::$app->request->post("name","")?>" name="name" aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Инвентарный номер</span>
            <input type="text" class="form-control" placeholder="Инв.номер" value="<?=Yii::$app->request->post("inv","")?>" name="inv" aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Действие</span>
            <?PHP
            echo \kartik\select2\Select2::widget([
                    "name"=>"act",
                    "data"=>["1"=>"Любое","2"=>"Удаление","3"=>"Изменение","4"=>"Добавление","5"=>"Перемещение","6"=>"Обслуживание"],
                    "value"=>Yii::$app->request->post("act","1")
            ]);
            ?>
        </div>

        <div class="input-group mb-3" style="width: 700px;">
            <span class="input-group-text" >С</span>
            <span class="form-control" style="width: 200px">
        <?PHP
        echo \kartik\date\DatePicker::widget(['class'=>"","name"=>"date_from","value"=>Yii::$app->request->post("date_from",null)]);
        ?>
        </span>
            <span class="input-group-text" >по</span>
            <span class="form-control" style="width: 200px">
        <?PHP
        echo \kartik\date\DatePicker::widget(['class'=>"","name"=>"date_to","value"=>Yii::$app->request->post("date_to",null)]);
        ?>
        </span>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Поиск </button>
    </form>

<?php

$histories = [];
if(Yii::$app->request->isPost){

    echo "<hr>";

    echo "<h4><i class='fa fa-search'></i> Результат</h4>";
    echo "Атрибуты поиска:";
    foreach ($_POST as $k=>$v){
        if(empty($v))continue;
        if($k == "_csrf")continue;
        if($k == "act")
            $v = ["1"=>"Любое","2"=>"Удаление","3"=>"Изменение","4"=>"Добавление","5"=>"Перемещение","6"=>"Обслуживание"][$v];

        echo "<span style='display:inline-block;' class='badge bg-secondary p-2 m-1'>$v</span>";
    }
    $q = \app\models\History::find();


    $date_from  = empty($_POST["date_from"])?null:date("Y-m-d",strtotime($_POST["date_from"]));
    $date_to  = empty($_POST["date_to"])?null:date("Y-m-d",strtotime($_POST["date_to"]));

    $act = null;
    switch ($_POST["act"]){
        case "2": $act = "delete";break;
        case "3": $act = "change";break;
        case "4": $act = "create";break;
        case "5": $act = "transfer";break;
        case "6": $act = "repair";break;
    }
    $q->andFilterWhere(["like","action",$act]);
    $q->andFilterWhere(["like","department",$_POST["dep"]]);
    $q->andFilterWhere(["like","category",$_POST["cat"]]);
    $q->andFilterWhere(["like","inv_num",$_POST["inv"]]);
    $q->andFilterWhere(["like","name",$_POST["name"]]);
    $q->andFilterWhere([">=","date",$date_from]);
    $q->andFilterWhere(["<=","date",$date_to]);
    $q->orderBy(["date"=>SORT_DESC]);

    $histories = $q->all();

    if(count($histories) === 0):
        echo "<h6> <i class='fa fa-ban'></i> Ничего не найдено...</h6>";
    else:

        $uniq =[];
        foreach ($histories as $h) {
            if (in_array($h->id, $uniq)) continue;
            else $uniq[] = $h->id;
        }
    ?>
        <h6>Найдено <?= count($uniq)?>:</h6>
        <ul class="mt-3">
            <?PHP
            $uniq =[];
            foreach ($histories as $h){
                if(in_array($h->id,$uniq))continue;
                else $uniq[] =  $h->id;
                ?>
                <li><a target="_blank" href="/item/history?id=<?=  $h->id ?>"><?= "[$h->id] $h->name ($h->inv_num) $h->category , $h->department" ?></a></li>
                    <?PHP
            }
            ?>
        </ul>
<?php
    endif;
}