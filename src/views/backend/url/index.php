<?php

use yii\helpers\Html;
use yii\grid\GridView;
use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;
use DmitriiKoziuk\yii2UrlIndex\forms\UrlSearchForm;
use DmitriiKoziuk\yii2UrlIndex\entities\UrlEntity;

/**
 * @var $this yii\web\View
 * @var $searchModel UrlSearchForm
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t(UrlIndexModule::TRANSLATE, 'Urls');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-index-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t(UrlIndexModule::TRANSLATE, 'Create Url Index Entity'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'url',
            'redirect_to_url',
            [
                  'label' => 'Module name',
                  'content' => function (UrlEntity $model) {
                      return $model->moduleEntity->module_name;
                  },
            ],
            [
                'label' => 'Controller name',
                'content' => function (UrlEntity $model) {
                    return $model->moduleEntity->controller_name;
                },
            ],
            [
                'label' => 'Action name',
                'content' => function (UrlEntity $model) {
                    return $model->moduleEntity->action_name;
                },
            ],
            'entity_id',
            //'created_at:datetime',
            //'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
