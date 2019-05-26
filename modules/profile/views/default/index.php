<?php

use app\models\Auth;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
$this->title = 'Особистий кабінет';
?>
<title><?= Html::encode($this->title) ?></title>
<body style="background:none;">
<div class="box-shadow" style="padding:9px; margin-top:60px; width: 100%; height:470px;">
    <div style="text-align:center; width: 100%;">
        <h2 style="margin:auto;" class="main_h ">Особиста інформація</h2>
        <div style="margin-top: 10px;">
            <div style="border-bottom: 2px solid #e37682; width: 50px; margin:auto;"></div>
        </div>
    </div>
    <div style="display: inline-flex; margin-top: 30px; width: 100%;">
        <div style="width:30%;">
            <?php
            $auth = Auth::findOne(['user_id' => Yii::$app->user->identity->id]);
            ?>
            <?= Html::img("https://graph.facebook.com/".$auth->source_id."/picture?type=large", ['style' => 'width: 250px; height: 250px; border-radius:50%;']) ?>
        </div>
        <div style="width:70%; text-align: left;">
            <h4>І'мя користувача: <?=Yii::$app->user->identity->username?></h4>
            <h4>Поштова скринька: <?=Yii::$app->user->identity->email ?></h4>
            <h4>Дата створення аккаунту: <?=Yii::$app->user->identity->created_at ?></h4>
            <?php
             $auth = Auth::findOne(['user_id' => Yii::$app->user->identity->id]);
                if($auth == null){
                    echo "<h4>Підключити аккаунт:</h4>";
                    ?>
                    <?= yii\authclient\widgets\AuthChoice::widget([
                    'baseAuthUrl' => ['site/auth'],
                    'popupMode' => false,
                    ]) ?>
                <?php
                }
            ?>
            <?php $Auths = Auth::findOne(['user_id' => Yii::$app->user->identity->id]);
                if($Auths->access_token == null){
                    echo '<div style="display:inline-flex;">'.Html::img('@web/img/not-done.png', ['style' => 'width:30px; height:30px;']).'
            <h4>Немає дозволу на використання сторінок:</h4></div>
            <div id="main" class="container">
                <a href="'.Url::toRoute(['default/fb-token']).'" class="btn btn-primary btn-large">Дати дозвіл</a>
            </div>';
            } else echo '<div style="display:inline-flex;">'.Html::img('@web/img/done.png', ['style' => 'width:30px; height:30px;']).'<h4>Дозвіл на використання сторінок отримано.</h4></div>'?>
        </div>
    </div>
</div>
</body>
