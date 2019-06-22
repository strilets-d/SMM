<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Markers */

$this->title = 'Update Markers: ' . $model->id_marker;
$this->params['breadcrumbs'][] = ['label' => 'Markers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_marker, 'url' => ['view', 'id' => $model->id_marker]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="markers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
