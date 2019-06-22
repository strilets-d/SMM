<h1>Логин</h1>
<div class="container">
    <?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    ?>
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous"
            src="https://connect.facebook.net/uk_UA/sdk.js#xfbml=1&version=v3.3&appId=1280074432168219&autoLogAppEvents=1"></script>
    <div class="in-row" style="width:100%;">
        <div style="width:30%;">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($login_model, 'username')->textInput() ?>

            <?= $form->field($login_model, 'password')->passwordInput() ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Увійти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
        </div>
        <div style="width:70%; margin-left:100px;">
            <h4>Увійдіть через соціальні мережі:</h4>
            <div class="in-row" style="width:130px;">
                <div style="width:80px;">
                    <?= yii\authclient\widgets\AuthChoice::widget([
                        'baseAuthUrl' => ['site/auth'],
                        'popupMode' => false,
                    ]) ?>
                </div>
                <div style="width:50px;">
                    <a href="https://api.instagram.com/oauth/authorize/?client_id=532eb0817c3f4f29b6dfcd6cddf1196f&redirect_uri=<?=urlencode("https://smm.strilets/site/instagram")?>&response_type=code"><?= Html::img("@web/img/instagram.png", ["style" => "width:33px; height:33px;"]) ?></a>
                </div>
            </div>
        </div>
    </div>
    <div>
        <h4>Ще не зареєстровані? <a href="<?= Url::toRoute(['signup']) ?>">Зареєструватись!</a></h4>
    </div>
    <?php $form = ActiveForm::end(); ?>
</div>
