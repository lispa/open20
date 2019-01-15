<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\widgets;

use lispa\amos\organizzazioni\AmosOrganizzazioni;
use lispa\amos\organizzazioni\models\base\Aziende;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\organizzazioni\models\Profilo;
use lispa\amos\organizzazioni\widgets\JoinProfiloWidget;
use lispa\amos\core\helpers\Html;
use Yii;
use yii\base\Widget;

class ProfiloCardWidget extends Widget
{
    /**
     * @var Profilo $model
     */
    public $model;

    /**
     * @var bool|false $imgStyleDisableHorizontalFix - do not use class full-height and dynamic margin calculation in case of horizontal img
     */
    public $imgStyleDisableHorizontalFix = false;

    /**
     * @var bool|true $onlyLogo displays only the img (logo) of organization, no card tooltip
     */
    public $onlyLogo = true;

    public $enableLink = true;

    public $absoluteUrl = false;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->model)) {
            throw new \Exception(AmosOrganizzazioni::t('amosorganizzazioni', '#missing_model'));
        }

}

    /**
     * @return mixed
     */
    public function run()
    {
        $model = $this->model;
        $html = '';

        $url = '/img/img_default.jpg';
        if (!is_null($model->logoOrganization)) {
            if($this->absoluteUrl) {
                $url = $model->logoOrganization->getWebUrl('original');
            } else {
                $url = $model->logoOrganization->getUrl('original');
            }
        }
        Yii::$app->imageUtility->methodGetImageUrl = 'getUrl';
        $roundImage = Yii::$app->imageUtility->getRoundImage($model->logoOrganization);

        $class = $roundImage['class'];
        if($this->absoluteUrl){
            if($class == 'full-width'){
                $style = "width: 100%; height: auto; margin-top:". $roundImage['margin-top']  . "%;";
            } elseif ($class == 'full-height') {
                $style = "height: 100%; width: auto; margin-left: " . $roundImage['margin-left'] . "%;";
            } else {
                $style = " width: 100%; height: auto;";
            }
            $htmlOptions = [
                'style' => $style,
                'alt' => $model->getAttributeLabel('logoOrganization')
            ];
        } else {
            $htmlOptions = [
                'class' => !empty($class) ? $class : 'square-img',
                'style' => ((!$this->imgStyleDisableHorizontalFix) ? "margin-left: " . $roundImage['margin-left'] . "%;" :  "") . "margin-top: " . $roundImage['margin-top'] . "%;",
                'alt' => $model->getAttributeLabel('logoOrganization')
            ];
        }
        $htmlTag = Html::img($url, $htmlOptions);
        $img = ($this->absoluteUrl) ? $htmlTag : Html::tag('div',$htmlTag, ['class' => 'container-round-img-sm']);

        if($this->onlyLogo){
            $link = null;
            if($this->enableLink){
                $link = '/organizzazioni/profilo/view?id='.$model->id;
                if($this->absoluteUrl) {
                    $link = Yii::$app->getUrlManager()->createAbsoluteUrl($link);
                }
            }
            $html .=  Html::a( $img, $link, ['title' => $model->name]);
        }else {
            $img = Html::tag('div', $img, ['class' => 'container-round-img-sm']);
            $modals = JoinProfiloWidget::widget([
                'model' => $this->model,
                'onlyModals' => true
            ]);
            $html = $modals . Html::a(
                    $img,
                    null,
                    [
                        'data' => [
                            'toggle' => 'tooltip',
                            'html' => 'true',
                            'placement' => 'right',
                            'delay' => ['show' => 100, 'hide' => 5000],
                            'trigger' => 'hover',
                            'template' => '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="background-color:transparent;min-width: 200px;"></div></div>'
                        ],
                        'title' => $this->getHtmlTooltip(),
                        'style' => 'border-color:transparent;'
                    ]
                );
        }
        return $html;
    }

    /**
     * @return string
     */
    private function getHtmlTooltip()
    {
        $model = $this->model;

        $viewUrl = "/organizzazioni/profilo/view?id=" . $model->id;
        $url = '/img/img_default.jpg';
        if (!is_null($model->logoOrganization)) {
            $url = $model->logoOrganization->getUrl('original', [
                'class' => 'img-responsive'
            ]);
        }
        Yii::$app->imageUtility->methodGetImageUrl = 'getUrl';
        $roundImage = Yii::$app->imageUtility->getRoundImage($model->logoOrganization);
        $logo = Html::img($url, [
            'class' => $roundImage['class'],
            'style' => ((!$this->imgStyleDisableHorizontalFix) ? "margin-left: " . $roundImage['margin-left'] . "%;" :  "") . "margin-top: " . $roundImage['margin-top'] . "%;",
            'alt' => $model->getAttributeLabel('logoOrganization')
        ]);
        $tooltip = '<div class="icon-view"><div class="card-container col-xs-12 nop">' .
            ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/organizzazioni/profilo/update?id=" . $model->id,
                'optionsModify' => [
                    'class' => 'community-modify',
                    'data-target' => '',
                    'data-toggle' => 'modal'
                ],
                'mainDivClasses' => '',
                'disableDelete' => true
            ])
            . '<div class="icon-header grow-pict">
                         <div class="container-round-img">' .
        Html::a($logo, $viewUrl, ['title' => $model->name])  . '</div>';
        $tooltip .= '</div><div class="icon-body">';
        $newsWidget = \lispa\amos\notificationmanager\forms\NewsWidget::widget([
            'model' => $model,
        ]);
        $tooltip .= $newsWidget . '<h3>' . Html::a($model->name, $viewUrl, ['title' => $model->name]) . '</h3>';

        $tooltip .= '</div></div></div>';

        return $tooltip;
    }
}