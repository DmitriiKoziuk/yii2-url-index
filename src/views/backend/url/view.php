<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;

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
        <?= Html::a(Yii::t(BaseModule::TRANSLATE, 'Update'), ['update', 'id' => $model->id], [
            'id' => 'update-url',
            'class' => 'btn btn-primary'
        ]) ?>
        <?= Html::a(Yii::t(BaseModule::TRANSLATE, 'Delete'), ['delete', 'id' => $model->id], [
            'id' => 'delete-url',
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t(BaseModule::TRANSLATE, 'Are you sure you want to delete this item?'),
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
            'module_name',
            'controller_name',
            'action_name',
            'entity_id',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
