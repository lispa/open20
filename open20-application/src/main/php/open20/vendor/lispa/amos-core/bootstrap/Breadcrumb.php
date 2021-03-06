<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\bootstrap
 * @category   CategoryName
 */

namespace lispa\amos\core\bootstrap;

use yii\base\BootstrapInterface;
use yii\base\Controller;
use yii\base\Event;
use yii\base\ViewEvent;
use yii\base\View;
use yii\base\WidgetEvent;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;


class Breadcrumb implements BootstrapInterface
{

    /**
     * @var string
     */
    public $activeBreadcrumbHelperClass = "lispa\\amos\\core\\helpers\\BreadcrumbHelper";

    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(Breadcrumbs::className(), Breadcrumbs::EVENT_BEFORE_RUN, [$this, 'modifyBreadcrumbs']);
    }

    /**
     * @param WidgetEvent $event
     */
    public function modifyBreadcrumbs(WidgetEvent $event)
    {
        /**
         * set up breadcrumb helper
         */
        $helperClass = \Yii::createObject($this->activeBreadcrumbHelperClass);

        /**
         * retrieve label
         */
        $lbl = end($event->sender->links);
        $result = reset($event->sender->links);
        if(is_object($lbl) && method_exists($lbl,'__toString') ) {
            $lbl = $lbl->__toString();
        }
        $label = isset($lbl['label']) ? $lbl['label'] : $lbl ;
        $template = isset($result['template']) ? $result['template'] : null ;
        $remove_action = isset($result['remove_action']) ? $result['remove_action'] : null ;

        /**
         * add crumb and generate cache again
         */
        $helperClass::add($label,Url::current(),$template,$remove_action);

        $trail = $helperClass->renderCrumbs();

        /**
         * if label is empty log in into breadcrumbs log
         */
        if(empty($label)) {
            /**
             * set logger stack info to zero
             */
            \Yii::getLogger()->traceLevel = 0;
            /**
             * log missing label
             */
            \Yii::info($trail, 'Breadcrumb');
        }

        /**
         * send cache to breadcrumb widget
         */
        //pr($trail,"Trail");
        $event->sender->links = $trail;
    }


}