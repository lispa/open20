<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\views\comment-reply
 * @category   CategoryName
 */

use lispa\amos\attachments\components\AttachmentsInput;
use lispa\amos\attachments\components\AttachmentsTableWithPreview;
use lispa\amos\comments\AmosComments;
use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\CloseSaveButtonWidget;
use lispa\amos\core\forms\CreatedUpdatedWidget;
use lispa\amos\core\forms\Tabs;
use yii\redactor\widgets\Redactor;

/**
 * @var \yii\web\View $this
 * @var \lispa\amos\comments\models\CommentReply $model
 * @var \yii\widgets\ActiveForm $form
 * @var string $fid
 */

/** @var AmosComments $commentsModule */
$commentsModule = Yii::$app->getModule(AmosComments::getModuleName());

?>

<div class="comment-reply-form col-xs-12 nop">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'comment_reply_' . ((isset($fid)) ? $fid : 0),
            'data-fid' => (isset($fid)) ? $fid : 0,
            'data-field' => ((isset($dataField)) ? $dataField : ''),
            'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
            'class' => ((isset($class)) ? $class : ''),
            'enctype' => 'multipart/form-data'
        ]
    ]);
    ?>
    <?php // $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
    
    <?php $this->beginBlock('general'); ?>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <?= $form->field($model, 'comment_reply_text')->widget(Redactor::className(), [
                'clientOptions' => [
                    'lang' => substr(Yii::$app->language, 0, 2)
                ]
            ]) ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>
    <?php
    $itemsTab[] = [
        'label' => AmosComments::tHtml('amoscomments', 'General'),
        'content' => $this->blocks['general'],
    ];
    ?>
    
    <?php $this->beginBlock('attachments'); ?>
    <?= $form->field($model, 'commentReplyAttachments')->widget(AttachmentsInput::classname(), [
        'options' => [ // Options of the Kartik's FileInput widget
            'multiple' => true, // If you want to allow multiple upload, default to false
        ],
        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
            'maxFileCount' => $commentsModule->maxCommentAttachments, // Client max files
            'showPreview' => false
        ]
    ])->label(AmosComments::t('amoscomments', '#ATTACHMENTS')) ?>
    <?= AttachmentsTableWithPreview::widget([
        'model' => $model,
        'attribute' => 'commentReplyAttachments'
    ]) ?>
    <div class="clearfix"></div>
    <?php $this->endBlock(); ?>
    <?php
    $itemsTab[] = [
        'label' => AmosComments::t('amoscomments', '#ATTACHMENTS'),
        'content' => $this->blocks['attachments']
    ];
    ?>
    
    <?= Tabs::widget([
        'encodeLabels' => false,
        'items' => $itemsTab
    ]);
    ?>
    
    <?= CreatedUpdatedWidget::widget(['model' => $model]) ?>
    <?= CloseSaveButtonWidget::widget(['model' => $model]); ?>
    <?php ActiveForm::end(); ?>
</div>
