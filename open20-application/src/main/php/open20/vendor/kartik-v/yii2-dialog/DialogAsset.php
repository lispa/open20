<?php

/**
 * @package   yii2-dialog
 * @version   1.0.3
 */

namespace kartik\dialog;

use yii\web\View;
use kartik\base\AssetBundle;

/**
 * Asset bundle that provides a polyfill for javascript native alert, confirm, and prompt boxes. The BootstrapDialog
 * will be used if available or needed, else the javascript native dialogs will be rendered.
 *
 * @since 1.0
 */
class DialogAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => View::POS_HEAD];

    /**
     * @inheritdoc
     */
    public $depends = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/dialog']);
        parent::init();
    }
}
