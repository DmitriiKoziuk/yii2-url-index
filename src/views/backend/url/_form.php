<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2UrlIndex\forms\UrlUpdateForm|DmitriiKoziuk\yii2UrlIndex\forms\UrlCreateForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="url-index-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'redirect_to_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'module_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'controller_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'entity_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), [
            'id' => 'save-url',
            'class' => 'btn btn-success',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
