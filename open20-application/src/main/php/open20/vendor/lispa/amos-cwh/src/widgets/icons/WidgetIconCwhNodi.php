<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\widgets\icons;

use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\cwh\AmosCwh;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * TODO SOLO abbozzata per le regole e migration
 * Class WidgetIconCwhNodi
 *
 * @package lispa\amos\cwh\widgets\icons
 */
class WidgetIconCwhNodi extends WidgetIcon
{
    /**
     * Init of the class, set of general configurations
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosCwh::t('amoscwh', 'CWH nodi'));
        $this->setDescription(AmosCwh::t('amoscwh', 'CWH gestione Nodi'));
        $this->setIcon('cwh');
        $this->setUrl(Yii::$app->urlManager->createUrl(['cwh/cwh-nodi']));
        $this->setCode('CWH_NODI');
        $this->setModuleName('cwh-nodi');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
    }
}