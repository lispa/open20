<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\views\layouts\parts
 * @category   CategoryName
 */

use lispa\amos\core\controllers\CrudController;
use lispa\amos\core\forms\CreateNewButtonWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\views\ChangeView;
use lispa\amos\core\views\grid\ActionColumn;
use lispa\amos\slideshow\models\Slideshow;
use kartik\export\ExportMenu;
use yii\base\Event;
use yii\helpers\Inflector;
use yii\web\View;

/**
 * @var \yii\web\View $this
 */

/** @var CrudController $appController */
$appController = Yii::$app->controller;

?>

<div class="row nom btn-tools-container">
    <div class="col-xs-12 nop">
        <?php if (isset($this->params['forceCreateNewButtonWidget']) || $appController->can('CREATE')): ?>
            <?= CreateNewButtonWidget::widget((isset($this->params['createNewBtnParams']) && is_array($this->params['createNewBtnParams']))
                ? $this->params['createNewBtnParams'] : []);
            ?>
        <?php endif; ?>
        <div class="pull-right">
            <?php
            //ORDER ENABLED?
            if (
                isset(\Yii::$app->controller->module) &&
                isset(\Yii::$app->controller->module->params) &&
                isset(\Yii::$app->controller->module->params['orderParams']) &&
                isset(\Yii::$app->controller->module->params['orderParams'][\Yii::$app->controller->id]) &&
                isset(\Yii::$app->controller->module->params['orderParams'][\Yii::$app->controller->id]['enable']) &&
                \Yii::$app->controller->module->params['orderParams'][\Yii::$app->controller->id]['enable']
            ) {
                echo AmosIcons::show('unfold-more',
                    [
                        'class' => 'btn-tools-primary show-hide-element',
                        'data-toggle-element' => 'form-order',
                    ]);
            }

            //INTRODUCTION ENABLED?
            if (
                isset(Yii::$app->controller->module) &&
                isset(Yii::$app->controller->module->params) &&
                isset(Yii::$app->controller->module->params['introductionParams']) &&
                isset(Yii::$app->controller->module->params['introductionParams'][Yii::$app->controller->id]) &&
                isset(Yii::$app->controller->module->params['introductionParams'][Yii::$app->controller->id]['enable']) &&
                Yii::$app->controller->module->params['introductionParams'][Yii::$app->controller->id]['enable'] &&
                Yii::$app->getModule('slideshow') &&
                isset(Yii::$app->params['slideshow']) &&
                Yii::$app->params['slideshow'] === true
            ) {
                $slideshow = new Slideshow;
                $route = "/" . Yii::$app->request->getPathInfo();
                $idSlideshow = $slideshow->hasSlideshow($route);
                if ($idSlideshow) {
                    echo AmosIcons::show('triangle-up',
                        [
                            'class' => 'btn-tools-primary rotate-right-90',
                            'id' => 'plugin-introduction-slideshow'
                        ]);
                    $js = "
                        $('#plugin-introduction-slideshow').on('click', function (event) {
                            $('#amos-slideshow').modal('show');
                        });
                    ";
                    $this->registerJs($js);
                }
            }

            //DOWNLOAD ENABLED?
            if (isset(Yii::$app->request->queryParams['download'])) {
                echo Html::tag('div', '', ['id' => 'change-view-download-btn', 'class' => 'pull-left m-r-3 hidden']);
                Event::on(View::className(), View::EVENT_END_BODY,
                    function ($event) {
                        $controller = \Yii::$app->controller;
                        if ($controller instanceof CrudController) {
                            $columns = $controller->getGridViewColumns();
                            if (is_array($columns)) {
                                $actionColumnsIndex = false;
                                // Setting dataProvider as variable. It can be overwritten if necessary.
                                $dataProvider = $controller->getDataProvider();

                                foreach ($columns as $index => $column) {
                                    if (is_array($column) && isset($column['class']) && ($column['class'] == ActionColumn::className())) {
                                        $actionColumnsIndex = $index;
                                    }
                                }

                                if (isset(Yii::$app->request->queryParams['exportConfig']) &&
                                    is_array(Yii::$app->request->queryParams['exportConfig'])) {

                                    if (array_key_exists('dataProvider', Yii::$app->request->queryParams['exportConfig']) &&
                                        Yii::$app->request->queryParams['exportConfig']['dataProvider'] instanceof \yii\data\DataProviderInterface) {

                                        $dataProvider = Yii::$app->request->queryParams['exportConfig']['dataProvider'];
                                    }
                                }

                                /** Create a different dataProvider for the export menu */
                                /** @var \yii\data\ActiveDataProvider $exportAllDataProvider */
                                if(empty(\Yii::$app->params['disableExportAll']) || !empty(\Yii::$app->params['disableExportAll'] && \Yii::$app->params['disableExportAll'] == false)) {
                                    $confAllDataProv = [
                                        'query' => $dataProvider->query,
                                        'pagination' => false
                                    ];
                                    if (!empty($controller->getDataProvider()->getSort())) {
                                        $confAllDataProv['sort'] = $dataProvider->getSort();
                                    }
                                    $exportAllDataProvider = new \yii\data\ActiveDataProvider($confAllDataProv);
                                    $exportDataProvider = $exportAllDataProvider;
                                }
                                else {
                                    $exportDataProvider = $dataProvider;
                                }

                                $exportMenuParams = [
                                    'dataProvider' => $exportDataProvider,
                                    'batchSize' => 0,
                                    'columns' => $columns,
                                    'showColumnSelector' => false,
                                    'showConfirmAlert' => false,
                                    'filename' => Yii::$app->view->title,
                                    'clearBuffers' => true,
                                    'exportConfig' => [
                                        ExportMenu::FORMAT_HTML => false,
                                        ExportMenu::FORMAT_CSV => false,
                                        ExportMenu::FORMAT_TEXT => false,
                                        ExportMenu::FORMAT_PDF => false
                                    ],
                                    'noExportColumns' => [
                                        $actionColumnsIndex
                                    ],
                                    'dropdownOptions' => [
                                        'class' => 'btn-tools-primary',
                                        'icon' => AmosIcons::show('download')
                                    ]
                                ];

                                if (isset(Yii::$app->request->queryParams['exportConfig']) &&
                                    is_array(Yii::$app->request->queryParams['exportConfig'])) {

                                    if (array_key_exists('emptyText', Yii::$app->request->queryParams['exportConfig']) &&
                                        (Yii::$app->request->queryParams['exportConfig']['emptyText'] != '')) {

                                        $exportMenuParams["emptyText"] = Yii::$app->request->queryParams['exportConfig']['emptyText'];
                                    }
                                }

                                // Renders a export dropdown menu
                                echo Html::beginTag('div', ['id' => 'change-view-dropdown-download', 'class' => 'hidden']);
                                echo ExportMenu::widget($exportMenuParams);
                                echo Html::endTag('div');
                            }
                        }
                    });

                $js = "
                $('#change-view-dropdown-download').appendTo('#change-view-download-btn').removeClass('hidden');
                $('#change-view-download-btn').removeClass('hidden');
                ";
                $this->registerJs($js, View::POS_READY);
            }

            //SEARCH ENABLED?
            $paramsSearch = false;
            $searchActive = false;
            if (
                isset(\Yii::$app->controller->module) &&
                isset(\Yii::$app->controller->module->params) &&
                isset(\Yii::$app->controller->module->params['searchParams']) &&
                isset(\Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id]) &&
                (
                    (
                        is_array(\Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id]) &&
                        isset(\Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id]['enable']) &&
                        \Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id]['enable']
                    ) ||
                    (
                        is_bool(\Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id]) &&
                        \Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id]
                    )
                )
            ) {
                //check if the controller is istance of CrucController to retrieve the setted searchModel
                if (\Yii::$app->controller instanceof CrudController) {
                    //retrieve the form name of current modelSearch
                    $modelSearch = \Yii::$app->controller->getModelSearch();
                    $classSearch = $modelSearch->formName();
                } else {
                    //use the previous mode to calculate the modelSearch name
                    $classSearch = Inflector::id2camel(\Yii::$app->controller->id, '-') . 'Search';
                }

                $paramsSearch = \Yii::$app->controller->module->params['searchParams'][\Yii::$app->controller->id];
                if (
                    isset(Yii::$app->request->queryParams[$classSearch]) &&
                    isset(Yii::$app->request->queryParams['enableSearch']) &&
                    Yii::$app->request->queryParams['enableSearch']
                ) {
                    $searchActive = TRUE;
                }
            }
            if ($paramsSearch) {
                if ($searchActive) {
                    echo AmosIcons::show('search', [
                        'class' => 'btn-tools-primary show-hide-element active',
                        'data-toggle-element' => 'form-search'
                    ]);
                } else {
                    echo AmosIcons::show('search', [
                        'class' => 'btn-tools-primary show-hide-element',
                        'data-toggle-element' => 'form-search'
                    ]);
                }
            }
            ?>
            <?= ChangeView::widget([
                'dropdown' => $appController->getCurrentView(),
                'views' => $appController->getAvailableViews(),
            ]); ?>
        </div>
    </div>
</div>
