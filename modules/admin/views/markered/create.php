<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MarkeredPosts */

$this->title = 'Create Markered Posts';
$this->params['breadcrumbs'][] = ['label' => 'Markered Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markered-posts-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
