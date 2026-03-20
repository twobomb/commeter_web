<?php
namespace app\assets;


use yii\web\AssetBundle;

class InputFieldAsset extends AssetWrapper
{

    public $js = [
        "/js/fileinput/js/plugins/sortable.js",
        "/js/fileinput/js/fileinput.min.js",
        "/js/fileinput/js/locales/ru.js"
    ];

    public $jsOptions = [
        'position'=> \yii\web\View::POS_HEAD
    ];
    public $css = [
        "/css/fileinput/css/fileinput.min.css"
    ];
    public $depends = [
        //  'app\assets\FontAwesomeAsset'

        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapPluginAsset'
    ];
}