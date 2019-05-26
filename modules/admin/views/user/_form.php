<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?php
    $items = [
        '0' => 'STATUS_INSERTED',
        '1' => 'STATUS_ACTIVE',
        '2' => 'STATUS_BLOCKED',
        '3' => 'STATUS_ADMIN'
    ];
    ?>
    <?= $form->field($model, 'status')->dropDownList($items) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'admin')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
