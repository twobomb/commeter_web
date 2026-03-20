<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Trables $model */

$this->title = "Неисправность #$model->id от ". Yii::$app->formatter->asDate($model->create_date);
$this->params['breadcrumbs'][] = ['label' => 'Список неисправностей', 'url' => ['/site/trables-list']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    #main{
        background: #f9f9f9;
    }
</style>
<div class="trables-view">

    <?PHP

    ?>
    <p><a class="btn btn-lg btn-info" href="<?=Yii::$app->getUrlManager()->createAbsoluteUrl(Yii::$app->request->url) == \Yii::$app->request->referrer?\yii\helpers\Url::to("/site/trables-list"):\Yii::$app->request->referrer ?>"><svg style="margin-bottom: 4px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
            </svg> Назад</a></p>

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'description:ntext',
            [
                    'attribute'=>'from_user_id',
                    'label'=>'Пользователь',
                    'format'=>'raw',
                    'value'=>function($m){
                        if(\app\models\User::isAdmin())
                            return Html::a($m->fromUser->name,"/admin/users-list?UserSearch[id]={$m->fromUser->id}",['data-pjax' => 0]);
                        return $m->fromUser->name;
                    }
            ],
            [
                    'attribute'=>'status_id',
                    'format'=>'raw',
                    'value'=>function($m){
        $curs =$m->status->name;
        $sid =$m->status->id;
                        if(!\app\models\User::isAdmin())
                            return $curs;
                        $s=  <<<EOF
<div class="dropdown">
  <button class="btn btn-info dropdown-toggle status$sid" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
    $curs
  </button>
  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="statusDropdown">
EOF;
                        foreach (\app\models\Statuses::find()->all() as $st):
                            $s .="<li><a data-id=\"$st->id\" class=\"dropdown-item  ".  ($st->id == $m->status_id?"active":"") ." href=\"#\"> $st->name </a></li>";
                        endforeach;

                        $s.=<<<EOF
  </ul>
</div>
EOF;
                        return $s;
                    }
            ],
            [
                    'attribute'=>'create_date',
                    'format'=>'raw',
                    'value'=>function($m){
                        return Yii::$app->formatter->asDatetime($m->create_date);
                    }
            ],
            'who',
            'phone',
        ],
    ]) ?>

</div>
<script>
    Array.from(document.querySelectorAll('#statusDropdown +.dropdown-menu .dropdown-item')).forEach(e=>{
      e.addEventListener('click', function (ee) {
          var xhr = new XMLHttpRequest();
          xhr.open('GET', '/admin/set-status?id=<?= $model->id ?>&status='+ee.currentTarget.getAttribute("data-id"), false);
          xhr.send();
          if (xhr.status == 200 && xhr.responseText == "OK") {
              document.querySelector("#statusDropdown").textContent = ee.currentTarget.textContent;
              document.querySelector("#statusDropdown").classList.remove(Array.from(document.querySelector("#statusDropdown").classList).find(s=>s.includes("status"))) ;
              document.querySelector("#statusDropdown").classList.add("status"+ee.currentTarget.getAttribute('data-id'));
              document.querySelector('#statusDropdown +.dropdown-menu .dropdown-item.active').classList.remove("active");
              ee.currentTarget.classList.add("active");
              $.pjax.reload('#history_pjax', {timeout : false});
          }
      })
    })
</script>

<h3>История</h3>

<?php yii\widgets\Pjax::begin(['id' => 'history_pjax']) ?>



    <form action="/site/add-trable-solution" method="post">
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">Добавить запись</label>
            <textarea class="form-control" name="text" id="exampleFormControlTextarea1" rows="3"></textarea>
            <input type="hidden" name="trable_id" value="<?= $model->id ?>">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Отправить</button>
        </div>
    </form>

<?php

    $sols= \app\models\TrableSolutionHistory::find()->where(["trable_id"=>$model->id])->orderBy(["id"=>SORT_DESC])->all();
    if(count($sols ) == 0):?>

    <h5 class="noSols">Пока-что записи отсутствуют</h5>
<?php
    else:
echo "<div id=\"solMainCont\">";
        foreach ($sols as $solution):
        ?>
            <div class="solContainer solclass<?=$solution->type?>">
                <div class="solText"><?php if ($solution->type == "user"):?><svg style="margin-right: 10px;    color: #f9a40f;    position: relative;    top: -2px;}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat-left-quote-fill" viewBox="0 0 16 16">
            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793zm7.194 2.766a1.7 1.7 0 0 0-.227-.272 1.5 1.5 0 0 0-.469-.324l-.008-.004A1.8 1.8 0 0 0 5.734 4C4.776 4 4 4.746 4 5.667c0 .92.776 1.666 1.734 1.666.343 0 .662-.095.931-.26-.137.389-.39.804-.81 1.22a.405.405 0 0 0 .011.59c.173.16.447.155.614-.01 1.334-1.329 1.37-2.758.941-3.706a2.5 2.5 0 0 0-.227-.4zM11 7.073c-.136.389-.39.804-.81 1.22a.405.405 0 0 0 .012.59c.172.16.446.155.613-.01 1.334-1.329 1.37-2.758.942-3.706a2.5 2.5 0 0 0-.228-.4 1.7 1.7 0 0 0-.227-.273 1.5 1.5 0 0 0-.469-.324l-.008-.004A1.8 1.8 0 0 0 10.07 4c-.957 0-1.734.746-1.734 1.667 0 .92.777 1.666 1.734 1.666.343 0 .662-.095.931-.26z"/>
        </svg><?php endif;if ($solution->type == "system"):?>
                        <svg  style="margin-right: 10px;    color: #70aaff;    position: relative;    top: -2px;}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-square-fill" viewBox="0 0 16 16">
                            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.93 4.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                        </svg><?php endif ?><?= htmlspecialchars($solution->description) ?></div>
                <div class="solDate"><?= Yii::$app->formatter->asDatetime($solution->date_create) ?></div>
                <div class="solUser"><?= $solution->user->name ?></div>
                <?PHP if($solution->canDelete() && $solution->user_id == Yii::$app->user->id && $solution->type == "user"):?>
                <div class="solDelete"  data-id="<?= $solution->id ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                    </svg></div>
                <?PHP endif; ?>
            </div>

<?php
    endforeach;
    echo "</div>";
        endif;

?>
    <script>
        document.querySelectorAll("#solMainCont .solDelete").forEach(e=>{
            e.addEventListener("click",function (ev){
                if(confirm("Удалить?") ) {

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', '/site/delete-comment?id='+ev.currentTarget.getAttribute("data-id"), false);
                    xhr.send();
                    if (xhr.status == 200) {
                        if(xhr.responseText == "OK")
                            $.pjax.reload('#history_pjax', {timeout : false});
                        else
                            alert(xhr.responseText )
                    }
                }
            })
        })
    </script>


<?php yii\widgets\Pjax::end() ?>