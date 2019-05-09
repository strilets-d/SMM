<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class EditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.css',
    ];
    public $js = [
        'js/ColorFilter.js',
        'js/ColorMatrixFilter.js',
        'js/ConvolutionFilter.js',
        'js/jquery-1.7.1.min.js',
        'js/jquery-ui-1.8.18.custom.min.js',
        'js/easel.js',
        'js/main.js',
        'js/ui.js',
        'js/file.js',
        'js/tools.js',
        'js/layer.js',
        'js/image.js',
        'js/filters.js',
        'js/scripts.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
