<?php

namespace eseperio\bigmenu;


use yii\web\AssetBundle;

class BigmenuAsset extends AssetBundle
{

    public $css = [
        'css/main.css',
    ];

    public $js = [
        'js/bigmenu.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets/';
        parent::init();
    }
}