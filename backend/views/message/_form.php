<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'message_subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'message_creator_id')->textInput() ?>

    <?= $form->field($model, 'message_body')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'message_parent_id')->textInput() ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <?= $form->field($model, 'createdby')->textInput() ?>

    <?= $form->field($model, 'createddate')->textInput() ?>

    <?= $form->field($model, 'updatedby')->textInput() ?>

    <?= $form->field($model, 'updateddate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
