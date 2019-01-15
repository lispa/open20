<?php

use lispa\amos\core\helpers\Html;
use lispa\amos\core\views\grid\ActionColumn;
use lispa\amos\cwh\AmosCwh;

/**
 *
 * @var $this \yii\web\View
 * @var $contentsDataProvider \yii\data\ArrayDataProvider
 * @var $networksDataProvider \yii\data\ArrayDataProvider
 * @var $lastProcessDateTime \DateTime
 *
 */

$this->title = AmosCwh::t('amoscwh', '#cwh_wizard_title{appName}', [
    'appName' => Yii::$app->name
]);

?>


<div class="">
    <?php
    \yii\bootstrap\Alert::begin([
        'closeButton' => false,
        'options' => [
            'class' => 'alert alert-info',
        ],
    ]);
    ?>
    <p><?= AmosCwh::t('amoscwh', '#cwh_wizard_introduction') ?> </p>
    <p><?= AmosCwh::t('amoscwh', '#cwh_wizard_introduction_2') ?>
    </p>
    <p><?= AmosCwh::t('amoscwh', '#cwh_wizard_introduction_3{lastProcessDateTime}{appName}',
            [
                'lastProcessDateTime' => $lastProcessDateTime,
                'appName' => Yii::$app->name,
            ]
        ) ?>
    </p>
    <?php
    \yii\bootstrap\Alert::end();
    ?>

    <div class="row">
        <div class="col-xs-12">
            <h2><?= AmosCwh::t('amoscwh', '#cwh_wizard_section_network_title') ?></h2>
            <h4><?= AmosCwh::t('amoscwh', '#cwh_wizard_section_network_description') ?></h4>
            <?= \lispa\amos\core\views\AmosGridView::widget([
                'dataProvider' => $networksDataProvider,
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if ($model->isConfigured()) {
                        return ['class' => 'alert alert-success'];
                    }
                    return [];
                },
                'columns' => [
                    /*
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return [
                                'id' => \yii\helpers\StringHelper::basename($model['classname']),
                                'value' => $model['classname'],
                                'checked' => $model->isConfigured()
                            ];
                        },
                    ],

                    'classname',
                     'base_url_config',
                    'config_class',
                    */
                    'label',
                    'module_id',
                    'configured' => [
                        'attribute' => 'configured',
                        'format' => 'boolean',
                        'value' => function ($model) {
                            return $model->isConfigured();
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'header' => AmosCwh::t('amoscwh', '#manage_config'),
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a(
                                    \lispa\amos\core\icons\AmosIcons::show('edit', [
                                        'alt' => AmosCwh::t('amoscwh', '#edit_config')
                                    ]),
                                    $model->composeUrl()
                                );
                            },
                        ],

                    ],
                ]

            ]) ?>


        </div>

        <div class="col-xs-12">
            <h2><?= AmosCwh::t('amoscwh', '#cwh_nodi_regeneration_title') ?></h2>
            <h4><?= AmosCwh::t('amoscwh', '#cwh_nodi_regeneration') ?></h4>
            <?= Html::a(AmosCwh::t('amoscwh', '#cwh_nodi_regeneration_btn'), '/cwh/configuration/wizard?regenerateView=1', ['class' => 'btn btn-primary']) ?>
        </div>

        <div class="col-xs-12">
            <h2><?= AmosCwh::t('amoscwh', '#cwh_wizard_section_content_title') ?></h2>
            <h4><?= AmosCwh::t('amoscwh', '#cwh_wizard_section_content_description') ?></h4>

            <?= \lispa\amos\core\views\AmosGridView::widget([
                'dataProvider' => $contentsDataProvider,
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if ($model->isConfigured()) {
                        return ['class' => 'alert alert-success'];
                    }
                    return [];
                },
                'columns' => [
                    //'classname',
                    'label',
                    'tablename',
                    'module_id',
                    //'base_url_config',
                    //'config_class',
                    'configured' => [
                        'attribute' => 'configured',
                        'format' => 'boolean',
                        'value' => function ($model) {
                            return $model->isConfigured();
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'header' => AmosCwh::t('amoscwh', '#manage_config'),
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a(
                                    \lispa\amos\core\icons\AmosIcons::show('edit', [
                                        'alt' => AmosCwh::t('amoscwh', '#edit_config')
                                    ]),
                                    $model->composeUrl()
                                );
                            },
                        ],

                    ],
                ]
            ]) ?>

        </div>
    </div>

    <div class="row">
        <?php $form = \lispa\amos\core\forms\ActiveForm::begin([

        ]); ?>

        <div class="bk-btnFormContainer col-sm-12">
            <?= \lispa\amos\core\helpers\Html::a(AmosCwh::t('amoscwh', 'Chiudi'),
                Yii::$app->urlManager->createUrl('dashboard'), [
                    'class' => 'btn btn-secondary pull-left',
                    'name' => 'close',
                ]) ?>
            <?= \lispa\amos\core\helpers\Html::submitButton(AmosCwh::t('amoscwh', 'Salva'), [
                'class' => 'btn btn-primary pull-right',
                'name' => 'save_config',
            ]) ?>
            <?= \lispa\amos\core\helpers\Html::submitButton(AmosCwh::t('amoscwh', 'Ricarica'), [
                'class' => 'btn btn-danger pull-right',
                'name' => 'delete_cache',
            ]) ?>
        </div>
        <?php \lispa\amos\core\forms\ActiveForm::end() ?>
    </div>
</div>

