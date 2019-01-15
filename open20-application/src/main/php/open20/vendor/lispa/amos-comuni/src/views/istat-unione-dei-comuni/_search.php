<?php

use lispa\amos\core\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var lispa\amos\comuni\models\search\IstatUnioneDeiComuniSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="istat-unione-dei-comuni-search element-to-toggle" data-toggle-element="form-search">
    <div class="col-xs-12"><h2>Cerca per:</h2></div>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'default-form'
        ]
    ]); ?>

    <div class="col-sm-6 col-lg-4">    <?= $form->field($model, 'id') ?></div>
    <div class="col-sm-6 col-lg-4">    <?= $form->field($model, 'nome') ?></div>
    <div class="col-sm-6 col-lg-4">    <?= $form->field($model, 'sito') ?></div>
    <div class="col-sm-6 col-lg-4">    <?= $form->field($model, 'istat_province_id') ?></div>
    <div class="col-xs-12">
        <div class="pull-right">
            <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton('Search', ['class' => 'btn btn-navigation-primary']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <!--a><p class="text-center">Ricerca avanzata<br>
                < ?=AmosIcons::show('caret-down-circle');?>
            </p></a-->
    <?php ActiveForm::end(); ?>

</div>
