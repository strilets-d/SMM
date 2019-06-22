<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Markers */

$this->title = 'Create Markers';
$this->params['breadcrumbs'][] = ['label' => 'Markers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
