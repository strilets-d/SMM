<?php
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 18.05.2019
 * Time: 22:52
 */
use yii\bootstrap\Alert;
echo '<div class="fullscreen">';

foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    if(Yii::$app->getSession()->hasFlash($key)){
        echo Alert::widget([
            'options' => [
                'class' => (in_array($key, ['success', 'info', 'warning', 'danger']) ? 'alert-' . $key : 'alert-info'),
            ],
            'body' => $message,
        ]);
    }


}

echo '</div>';