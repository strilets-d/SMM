<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MarkeredPosts */

$this->title = 'Update Markered Posts: ' . $model->id_mark_post;
$this->params['breadcrumbs'][] = ['label' => 'Markered Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_mark_post, 'url' => ['view', 'id' => $model->id_mark_post]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="markered-posts-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
