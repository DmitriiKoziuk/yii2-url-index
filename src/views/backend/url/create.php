<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity */

$this->title = Yii::t(UrlIndexModule::TRANSLATE, 'Create Url');
$this->params['breadcrumbs'][] = ['label' => Yii::t(UrlIndexModule::TRANSLATE, 'Url Index Entities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-index-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
