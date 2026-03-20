<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

$this->title = $title;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= $message ?>
    </div>

    <p>
        Приведенная выше ошибка произошла, когда веб -сервер обрабатывал ваш запрос.
    </p>
    <p>Пожалуйста, свяжитесь с нами, если вы думаете, что это ошибка сервера. Спасибо.
    </p>

</div>
