<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\privileges
 * @category   CategoryName
 */

namespace lispa\amos\privileges\widgets;

use lispa\amos\core\helpers\Html;
use lispa\amos\core\views\ListView;
use lispa\amos\privileges\AmosPrivileges;
use lispa\amos\privileges\assets\AmosPrivilegesAsset;
use lispa\amos\privileges\utility\PrivilegesUtility;
use yii\helpers\ArrayHelper;


class UserPrivilegesWidget extends \yii\base\Widget
{
    public $userId = null;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->userId)) {
            throw new \Exception(\lispa\amos\core\module\BaseAmosModule::t('amosreport', 'Missing userId'));
        }
    }


    /**
     * @return mixed
     */
    public function run()
    {
        $id = $this->userId;

        $utility = new PrivilegesUtility(['userId' => $id]);
        $array = $utility->getPrivilegesArray( true);

        AmosPrivilegesAsset::register(\Yii::$app->view);
        $html = Html::tag('div',
            Html::a(AmosPrivileges::t('amosprivileges', 'manage'),
            ['/privileges/privileges/manage-privileges', 'id' => $id],
            ['class' => 'btn btn-navigation-primary pull-right'] ),
            ['class' => 'col-xs-12']
        );
        $count = 0;
        foreach ($array as $label => $data) {
            if($count == 0){
                $html .= '<h3 style="text-decoration: underline; text-transform: uppercase">' . AmosPrivileges::t('amosprivileges', $label) . '</h3>';
                $label = 'User categories';
            } else {
               if($count == 1){
                   $html .= '<br/>';
                   $html .= '<h3 style="text-decoration: underline; text-transform: uppercase;">' . AmosPrivileges::t('amosprivileges', 'Plugins') . '</h3>';
               }
            }
            $html .= '<div class="col-xs-12"><h4 class="col-sm-3 nop">' . AmosPrivileges::t('amosprivileges', $label) . '</h4>';
            $html .= ListView::widget([
                'dataProvider' => $data,
                'options' => [
                    'id' => $label,
                    'class' => 'col-sm-8',
                    'style' => 'margin: 18px 0 15px 0;'
                ],
                'itemView' =>  function ($model, $key, $index, $widget) {
                    $item = '';
                    if($model['can']) {
                        $item = AmosPrivileges::t('amosprivileges', $model['description']);
                        if ($model['isCwh']) {
                            $domains = ArrayHelper::map(\lispa\amos\cwh\models\CwhNodi::find()->andWhere([
                                'in',
                                'id',
                                explode(',', $model['domains'])
                            ])->all(), 'id', 'text');
                            $domainsCount = count($domains);
                            $item .= ' ' . AmosPrivileges::t('amosprivileges', 'in') . ' ';
                            $item .= Html::a(
                                Html::tag('span', $domainsCount, [
                                    'class' => 'badge',
                                ]),
                                null,
                                ['data-toggle' => 'tooltip', 'title' => implode(', ', $domains)]
                            );
                            $item .= ' ' . AmosPrivileges::t('amosprivileges', 'scopes');
                        } else {
                            if(!empty($model['parents'])){
                                foreach ($model['parents'] as $parent){
                                    if($parent == 'FACILITATOR'){
                                        $item = AmosPrivileges::t('amosprivileges', 'Facilitator role');
                                    }
                                }
                            }
                        }
                    }
                    return $item;
                }
            ]);

            $html .= '</div><br/>';

            $count++;
        }
        return $html;
    }

}