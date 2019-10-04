<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm */

$this->title = Yii::t('app', 'Update Url Index Entity: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t(UrlIndexModule::TRANSLATE, 'Url Index Entities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="url-index-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
