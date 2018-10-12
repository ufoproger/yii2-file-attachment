<?php

namespace ufoproger\fileattachment;

class Asset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/lpology/simple-ajax-uploader';
    public $css = [];
    public $js = [
        'SimpleAjaxUploader.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}