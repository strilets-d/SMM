<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MarkeredPostSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="markered-posts-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_mark_post') ?>

    <?= $form->field($model, 'id_post') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'id_page') ?>

    <?= $form->field($model, 'id_marker') ?>

    <?php // echo $form->field($model, 'id_user') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
