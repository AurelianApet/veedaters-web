<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'message_id') ?>

    <?= $form->field($model, 'message_subject') ?>

    <?= $form->field($model, 'message_creator_id') ?>

    <?= $form->field($model, 'message_body') ?>

    <?= $form->field($model, 'message_parent_id') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'createdby') ?>

    <?php // echo $form->field($model, 'createddate') ?>

    <?php // echo $form->field($model, 'updatedby') ?>

    <?php // echo $form->field($model, 'updateddate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
