<?php

$this->title = "Истории";
$filters = Yii::$app->request->get();
$actionsMap = ["1"=>"Любое","2"=>"Удаление","3"=>"Изменение","4"=>"Добавление","5"=>"Перемещение","6"=>"Обслуживание"];
$hasFilters = false;
foreach (['dep', 'cat', 'name', 'inv', 'act', 'date_from', 'date_to', 'dt_from', 'dt_to'] as $filterKey) {
    if (!empty($filters[$filterKey]) && !($filterKey === 'act' && $filters[$filterKey] === '1')) {
        $hasFilters = true;
        break;
    }
}

if (!function_exists('parseHistoryDateInput')) {
    function parseHistoryDateInput($value, $isEndOfDay = false)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim((string)$value);
        $formats = ['d.m.Y H:i:s', 'd.m.Y', 'Y-m-d H:i:s', 'Y-m-d'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt instanceof \DateTime) {
                if ($format === 'd.m.Y' || $format === 'Y-m-d') {
                    $dt->setTime($isEndOfDay ? 23 : 0, $isEndOfDay ? 59 : 0, $isEndOfDay ? 59 : 0);
                }
                return $dt->format('Y-m-d H:i:s');
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }

        return date($isEndOfDay ? 'Y-m-d 23:59:59' : 'Y-m-d 00:00:00', $timestamp);
    }
}

?>
    <form action="/admin/history" method="get">
        <h5>Поиск по истории</h5>
        <div class="alert alert-info" role="alert">
            Все поля не являются обязательными, можно сделать частичный ввод искомого значения
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text" >Подразделение</span>
            <input type="text" class="form-control" placeholder="Подразделение" name="dep" value="<?= $filters["dep"] ?? "" ?>"  aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Категория</span>
            <input type="text" class="form-control" placeholder="Категория" value="<?= $filters["cat"] ?? "" ?>" name="cat" aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Название</span>
            <input type="text" class="form-control" placeholder="Название" value="<?= $filters["name"] ?? "" ?>" name="name" aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Инвентарный номер</span>
            <input type="text" class="form-control" placeholder="Инв.номер" value="<?= $filters["inv"] ?? "" ?>" name="inv" aria-label="Username" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" >Действие</span>
            <?PHP
            echo \kartik\select2\Select2::widget([
                    "name"=>"act",
                    "data"=>$actionsMap,
                    "value"=>$filters["act"] ?? "1"
            ]);
            ?>
        </div>

        <div class="input-group mb-3" style="width: 700px;">
            <span class="input-group-text" >С</span>
            <span class="form-control" style="width: 200px">
        <?PHP
        echo \kartik\date\DatePicker::widget([
            'class' => "",
            'name' => "date_from",
            'value' => $filters["date_from"] ?? null,
            'pluginOptions' => [
                'format' => 'dd.mm.yyyy',
                'autoclose' => true,
                'todayHighlight' => true,
            ],
        ]);
        ?>
        </span>
            <span class="input-group-text" >по</span>
            <span class="form-control" style="width: 200px">
        <?PHP
        echo \kartik\date\DatePicker::widget([
            'class' => "",
            'name' => "date_to",
            'value' => $filters["date_to"] ?? null,
            'pluginOptions' => [
                'format' => 'dd.mm.yyyy',
                'autoclose' => true,
                'todayHighlight' => true,
            ],
        ]);
        ?>
        </span>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Поиск </button>
    </form>

<?php

$histories = [];
if($hasFilters){

    echo "<hr>";

    echo "<h4><i class='fa fa-search'></i> Результат</h4>";
    echo "Атрибуты поиска:";
    foreach ($filters as $k=>$v){
        if(empty($v))continue;
        if(in_array($k, ["r","dt_from","dt_to"])) continue;
        if($k == "act")
            $v = $actionsMap[$v] ?? $v;

        echo "<span style='display:inline-block;' class='badge bg-secondary p-2 m-1'>$v</span>";
    }
    $q = \app\models\History::find();


    $dateFromInput = $filters["date_from"] ?? ($filters["dt_from"] ?? null);
    $dateToInput = $filters["date_to"] ?? ($filters["dt_to"] ?? null);
    $date_from = parseHistoryDateInput($dateFromInput, false);
    $date_to = parseHistoryDateInput($dateToInput, true);

    $act = null;
    switch ($filters["act"] ?? "1"){
        case "2": $act = "delete";break;
        case "3": $act = "change";break;
        case "4": $act = "create";break;
        case "5": $act = "transfer";break;
        case "6": $act = "repair";break;
    }
    $q->andFilterWhere(["like","action",$act]);
    $q->andFilterWhere(["like","department",$filters["dep"] ?? null]);
    $q->andFilterWhere(["like","category",$filters["cat"] ?? null]);
    $q->andFilterWhere(["like","inv_num",$filters["inv"] ?? null]);
    $q->andFilterWhere(["like","name",$filters["name"] ?? null]);
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