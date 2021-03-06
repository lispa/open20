<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

namespace lispa\amos\chat\assets;

use yii\web\AssetBundle;

/**
 * Class AmosChatAsset
 * @package lispa\amos\chat\assets
 */
class AmosChatAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/lispa/amos-chat/src/assets/web';

    /**
     * @var array
     */
    public $css = [
        'less/chat.less',
    ];

    /**
     * @var array
     */
    public $js = [
        'js/amos_chat.js',
        'js/amos_chat_conversations.js',
        'js/amos_chat_messages.js',
        'js/amos_chat_user_contacts.js',
        'js/amos_chat_forward_message.js',
    ];

    /**
     * @var array
     */
    public $depends = [
    ];

    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');
        if(!empty($moduleL)){
            $this->depends [] = 'lispa\amos\layout\assets\BaseAsset';
        }else{
            $this->depends [] = 'lispa\amos\core\views\assets\AmosCoreAsset';
        }
        parent::init(); // TODO: Change the autogenerated stub
    }
}