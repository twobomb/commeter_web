<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('TECHNICAL_WORKS') or define('TECHNICAL_WORKS', false);
defined('YII_ENV') or define('YII_ENV', 'prod');//dev prod

if (TECHNICAL_WORKS === true || TECHNICAL_WORKS == 1) {
    require __DIR__ . '/maintenance.php';
    exit;
}



require __DIR__ . '/../functions.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
