<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t(UrlIndexModule::TRANSLATE, 'Url Index Entities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="url-index-entity-view">

    <h1>Url created: <?= Html::encode($model->url) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], [
            'id' => 'update-url',
            'class' => 'btn btn-primary'
        ]) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'id' => 'delete-url',
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'get',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'url:url',
            'redirect_to_url:url',
            [
                'label' => 'Module name',
                'value' => $model->moduleEntity->module_name,
            ],
            [
                'label' => 'Controller name',
                'value' => $model->moduleEntity->controller_name,
            ],
            [
                'label' => 'Action name',
                'value' => $model->moduleEntity->action_name,
            ],
            'entity_id',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
