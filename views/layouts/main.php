<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use kartik\growl\Growl;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$isMain = Yii::$app->controller->id == "item" && Yii::$app->controller->action->id == "index";
$isSearch = Yii::$app->controller->id == "item" && Yii::$app->controller->action->id == "search";
if(!$isSearch)
	$isSearch = Yii::$app->controller->id == "site" && Yii::$app->controller->action->id == "excel-prepare";


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'id'=>"topMenu",
        'brandLabel' => "<i class='fa fa-wifi'></i>  ".Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    $isAdmin = \app\models\User::isAdmin();
    $items = [];

    if(!Yii::$app->user->isGuest){
        array_push($items,
            ['label' => 'Поиск', 'url' => ['/item/search']],
            ['label' => 'Средства', 'url' => ['/item/index']],
        );
        array_push($items,
            ['label' => 'Сотрудники', 'url' => ['/employee/index']]
        );
        array_push($items,
            [
                'label' => 'Дополнительно',
                'items' => [
                    ['label' => 'Теги', 'url' => '/tag/index'],
                    ['label' => 'Список перемещений', 'url' => '/item/transfer-list'],
                    ['label' => 'Список обслуживаний', 'url' => '/item/repair-list'],
                    ['label' => 'Массовое перемещение', 'url' => '/item/massive-transfer'],
                    ['label' => 'Замена категории средств', 'url' => '/item/massive-change-category'],
                    ['label' => 'Смена пароля', 'url' => '/site/change-password'],
                ],
            ]);
    if($isAdmin)
        array_push($items,
            [
                'label' => 'Администрирование',
                'items' => [
                    ['label' => 'Пользователи', 'url' => '/admin/users-list'],
                    ['label' => 'Дашборд подразделений', 'url' => '/admin/departments-dashboard'],
                    ['label' => 'Категории', 'url' => '/category'],
                    ['label' => 'Подразделения', 'url' => '/department'],
                    ['label' => 'Свойства', 'url' => '/feature'],
                    ['label' => 'Словари', 'url' => '/dictinary'],
                    ['label' => 'Поиск по истории', 'url' => '/admin/history'],
                    ['label' => 'Добавление из файла', 'url' => '/site/excel-prepare'],
                ],
            ]);

    }
    array_push($items,
        Yii::$app->user->isGuest
            ? ['label' => 'Авторизация', 'url' => ['/site/login']]
            : '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                'Выход (' . Yii::$app->user->identity->name . ')',
                ['class' => 'nav-link btn btn-link logout']
            )
            . Html::endForm()
            . '</li>');


    array_push($items,
        ['label' => Html::tag("i","",["class"=>"fa fa-question-circle"]),'url' => ['/site/help'],'options' => ['class'=>'help-btn']]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $items,
        'encodeLabels' => false
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container <?= $isMain?"isMain":"" ?>">

        <?php if (!$isMain && !empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>


        <?PHP
        $alerts = \Yii::$app->session->getFlash("alert",[],true);

        if(is_array($alerts)) {
            $delay = 0;
            for ($i = 0 ; $i < count($alerts);$i++) {
                $alert = $alerts[$i];
                echo Growl::widget([
                    'type' => $alert["type"],
                    'title' => $alert["title"],
                    'closeButton' => ['style' => 'background:none;    float: right;    border: none;    font-weight: bold; font-size:22px;    margin-top: -9px;'],
                    'icon' => 'fas fa-check-circle',
                    'body' => $alert["message"],
                    'showSeparator' => true,
                    'delay' => $delay,//Задрежка перед показом
                    'pluginOptions' => [
                        'delay' => $alert["delay"],//Задержка показа
                        'showProgressbar' => true,
                        'placement' => [
                            'from' => 'top',
                            'align' => 'right',
                        ]
                    ]
                ]);
                $delay+=$alert["delay"];
            }
        }
        ?>
        <?PHP
        if(!$isMain && !$isSearch)
            echo $content;

        ?>
    </div>
    <?PHP
    if($isMain ){ ?>
        <div class="container-flex">
            <?= $content ?>
        </div>
    <?PHP
    }
    ?>
    <?PHP
    if($isSearch ){ ?>
        <div class="container-flex-search">
            <?= $content ?>
        </div>
    <?PHP
    }
    ?>
</main>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
