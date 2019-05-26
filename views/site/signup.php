<h1>Реєстрація</h1>
<div class="container">
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use \yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model,'username')->textInput() ?>

<?= $form->field($model,'password')->passwordInput()?>

<div class="form-group">
    <div class="">
        <?= Html::submitButton('Зареєструватись', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>
</div>
<div style="color:red;">
</div>
<?php
ActiveForm::end();
?>
<div>
    <h4>Вже зареєструвались?<a href="<?=Url::toRoute(['login'])?>"> Увійти!</a></h4>
</div>
</div>
