<?php

/**
 * @package yii2-widgets
 * @subpackage yii2-widget-fileinput
 * @version 1.0.6
 */

namespace kartik\file;

use kartik\base\AssetBundle;

/**
 * DomPurify asset bundle for FileInput Widget
 *
 * @since 1.0
 */
class DomPurifyAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath('@vendor/kartik-v/bootstrap-fileinput');
        $this->setupAssets('js', ['js/plugins/purify']);
        parent::init();
    }
}