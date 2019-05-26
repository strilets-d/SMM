<h1>Логин</h1>
<div class="container">
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/uk_UA/sdk.js#xfbml=1&version=v3.3&appId=1280074432168219&autoLogAppEvents=1"></script>
<div class="in-row" style="width:100%;">
    <div style="width:30%;">
<?php $form = ActiveForm::begin();?>

<?= $form->field($login_model,'username')->textInput()?>

<?= $form->field($login_model,'password')->passwordInput()?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Увійти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>
    </div>
<div style="width:70%; margin-left:100px;">
<h4>Увійдіть через Facebook:</h4>
    <?= yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['site/auth'],
        'popupMode' => false,
    ]) ?>
</div>
</div>
<div>
    <h4>Ще не зареєстровані? <a href="<?=Url::toRoute(['signup'])?>">Зареєструватись!</a></h4>
</div>
<?php $form = ActiveForm::end();?>
</div>
