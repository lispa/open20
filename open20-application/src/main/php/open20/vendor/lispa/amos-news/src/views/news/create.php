<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news
 * @category   CategoryName
 */

use lispa\amos\news\AmosNews;

/**
 * @var yii\web\View $this
 * @var lispa\amos\news\models\News $model
 */

/** @var \lispa\amos\news\controllers\NewsController $controller */
$controller = Yii::$app->controller;
$controller->setNetworkDashboardBreadcrumb();
$this->title = AmosNews::t('amosnews', 'Inserisci notizia');
$this->params['breadcrumbs'][] = ['label' => AmosNews::t('amosnews', Yii::$app->session->get('previousTitle')), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>