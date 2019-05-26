<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Користувачі', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Оновити', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Видалити', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Ви впевнені, що хочете видалити даного користувача?',
                'method' => 'post',
            ],
        ]) ?>
        <?php
        if($model->status == User::STATUS_BLOCKED) echo Html::a('Розблокувати', ['unban', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Ви впевнені, що хочете розблокувати даного користувача?',
                'method' => 'post',
            ],
        ]);
        else echo Html::a('Заблокувати', ['ban', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => 'Ви впевнені, що хочете заблокувати даного користувача?',
                'method' => 'post',
            ],
        ]);
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'password_hash',
            'email:email',
            'status',
            'created_at',
            'admin',
        ],
    ]) ?>

</div>
