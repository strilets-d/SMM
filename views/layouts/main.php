<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('//layouts/_messages') ?>
<div class="wrap">
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" href="#">
            <?= Html::img('@web/img/logo.png',['class' => 'd-inline-block align-top', 'style' => 'width:40px; height:30px;']); ?>
            SMM
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="search" placeholder="Пошук" aria-label="Пошук">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Пошук</button>
                </form>
            </ul>
            <ul class="navbar-nav mr-left">
                <?php if(!Yii::$app->user->isGuest){ ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= Yii::$app->user->identity->username ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?=Url::toRoute(['profile/default/']); ?>">Особистий кабінет</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?=Url::toRoute(['/site/logout']); ?>">Вийти</a>
                    </div>
                </li>
                <?php }?>
                <?php if(Yii::$app->user->isGuest){ ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::toRoute(['/site/login']);?>">Sign In</a>
                </li>
                <?php }?>
            </ul>

        </div>
    </nav>
        <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
