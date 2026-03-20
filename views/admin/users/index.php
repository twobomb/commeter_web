<?php

use app\models\User;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать пользователя', ['create-user'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width:50px'],
            ],
            'name',
            'login',
            [
                'attribute' => 'role',
                'filter' => \app\models\DataExt::getRolesInp(),
                'value'=>function($m){
                    return \app\models\DataExt::getRolesInp()[$m->role];
                }

            ],
            [
                'attribute' => 'block',
                "label"=>"Блокировка",
                'filter' => [0=>"Разблокирован",1=>"Заблокирован"],
                'value'=>function($m){
                    return $m->block?"Заблокирован":"Разблокирован";
                }
            ],
            [
                'attribute' => 'last_activity',
                "label"=>"Последняя активность",
                'value'=>function($m){
                    if($m->last_activity == null)
                        return "";
                    return getTimeAgo($m->last_activity);
                }
            ],
            //'accessToken',
            //'authKey',
            [
                'class' => ActionColumn::className(),
                'template'=>'{update}{block}',
                'buttons' => [
                         'update' => function ($url, $model) {
                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"  class="svgIcon" viewBox="0 0 16 16">
  <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
</svg>', "/admin/update-user?id=$model->id", [
                        ]);
                        },
                    'block' => function ($url, $model) {
                        $blocked = $model->block;

                        if($blocked)
                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"  class="svgIcon" viewBox="0 0 16 16">
  <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2M3 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1z"/>
</svg>', "/admin/block-user?id=$model->id&val=0", [
                        ]);
                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="svgIcon" viewBox="0 0 16 16">
  <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
</svg>', "/admin/block-user?id=$model->id&val=1", [
                        ]);
                        }
                        ],
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
