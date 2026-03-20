<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Item $model */

$this->title = 'История средства ID: '.$id ;
$this->params['breadcrumbs'][] = ['label' => 'Средства', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'История';
?>
<div class="item-history">

    <h3><?= Html::encode($this->title) ?></h3>
    <div class="historyContainer">

    <?PHP
        foreach (\app\models\History::find()->where(["id"=>$id])->orderBy(["date"=>SORT_DESC])->all() as $v):?>
            <div class="historyData">
                <span><?= date("d.m.Y H:i",strtotime($v->date)) ?></span> <span><?PHP
                    if($v->action == "create" )echo "Создание";
                    else if($v->action == "change") echo "Изменение";
                    else if($v->action == "transfer") echo "Перемещение";
                    else if($v->action == "repair") echo "Обслуживание";
                    else if($v->action == "delete") echo "Удаление";
                    else echo $v->action;
                    ?></span>
            </div>
            <div class="historyChanges">
                <ul >
                    <?PHP
                    $data =json_decode($v->data);
                    foreach ($data as $k=>$val):?>
                        <li ><span><?= $k ?>:</span> <span><?= $val ?></span></li>
                    <?PHP endforeach;

                    ?>
                </ul>
            </div>

        <?PHP endforeach;

    ?>
    </div>

</div>
