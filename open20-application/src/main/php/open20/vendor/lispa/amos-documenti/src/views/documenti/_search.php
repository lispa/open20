<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

use lispa\amos\documenti\AmosDocumenti;
use lispa\amos\documenti\models\Documenti;
use kartik\select2\Select2;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var lispa\amos\documenti\models\search\DocumentiSearch $model
 * @var yii\widgets\ActiveForm $form
 */
$moduleTag = Yii::$app->getModule('tag');
$controller = Yii::$app->controller;
$hidePubblicationDate = $controller->documentsModule->hidePubblicationDate;

?>

<div class="documenti-search element-to-toggle" data-toggle-element="form-search">
    <div class="col-xs-12"><h2><?= AmosDocumenti::tHtml('amosdocumenti', 'Cerca per') ?>:</h2></div>

    <?php $form = ActiveForm::begin([
        'action' => (isset($originAction) ? [$originAction] : ['index']),
        'method' => 'get',
    ]);

    echo Html::hiddenInput("enableSearch", "1");
    echo Html::hiddenInput("currentView", Yii::$app->request->getQueryParam('currentView'));

    if(!is_null(Yii::$app->request->getQueryParam('parentId'))) {
        echo Html::hiddenInput('parentId',  Yii::$app->request->getQueryParam('parentId'));
    }
    ?>

    <?= $form->field($model, 'parent_id' )->hiddenInput(['value' => Yii::$app->request->getQueryParam('parentId')])->label(false) ?>
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'titolo') ?>
    </div>

    <!--
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'sottotitolo') ?>
    </div>

    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'descrizione') ?>
    </div>
    -->
    <?php if(!$hidePubblicationDate) {?>
        <div class="col-sm-6 col-lg-4">
            <?= $form->field($model, 'data_pubblicazione')->widget(DateControl::className(), [
                'type' => DateControl::FORMAT_DATE
            ]) ?>
        </div>
    <div class="col-sm-6 col-lg-4">
        <?= $form->field($model, 'data_rimozione')->widget(DateControl::className(), [
            'type' => DateControl::FORMAT_DATE
        ]) ?>
    </div>
    <?php } ?>
    <div class="col-sm-6 col-lg-4">
        <?=
        $form->field($model, 'created_by')->widget(Select2::className(), [
                'data' => (!empty($model->created_by) ? [$model->created_by => $model->createdUserProfile->getNomeCognome()] : []),
                'options' => ['placeholder' => AmosDocumenti::t('amosdocumenti', 'Cerca ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/admin/user-profile-ajax/ajax-user-list']),
                        'dataType' => 'json',
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                    ],
                ],
            ]
        );
        ?>
		</div>
		 <div class="col-sm-6 col-lg-4">
		<?=
        $form->field($model, 'updated_by')->widget(Select2::className(), [
                'data' => (!empty($model->updated_by) ? [$model->updated_by => $model->updatedUserProfile->getNomeCognome()] : []),
                'options' => ['placeholder' => AmosDocumenti::t('amosdocumenti', 'Cerca ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/admin/user-profile-ajax/ajax-user-list']),
                        'dataType' => 'json',
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                    ],
                ],
            ]
        );
        ?>
    </div>

    <?php if (isset($moduleTag) && in_array(Documenti::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors): ?>
    <div class="col-xs-12">
        <?php
        $params = \Yii::$app->request->getQueryParams();
        echo \lispa\amos\tag\widgets\TagWidget::widget([
            'model' => $model,
            'attribute' => 'tagValues',
            'form' => $form,
            'isSearch' => true,
            'form_values' => isset($params[$model->formName()]['tagValues']) ? $params[$model->formName()]['tagValues'] : []
        ]);
        ?>
    </div>
    <?php endif; ?>

    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::a(AmosDocumenti::t('amosdocumenti', 'Annulla'), [(isset($originAction) ? $originAction : Yii::$app->controller->action->id), 'currentView' => Yii::$app->request->getQueryParam('currentView')],
                ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton(AmosDocumenti::tHtml('amosdocumenti', 'Cerca'), ['class' => 'btn btn-navigation-primary']) ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
