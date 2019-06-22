<?php

use yii\helpers\Url;

?>
<div class="admin-default-index">
    <h1>Можливості:</h1>
    <table class="table table-hover table-responsive" style="vertical-align:middle; text-align:left;">
        <tbody>
        <tr>
            <td>
                <a href="<?= Url::toRoute('user/index') ?>" style="color:black; text-decoration: none;"><i class="far fa-user" style="margin-right:5px;"></i>Користувачі</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="<?= Url::toRoute('marker/index') ?>" style="color:black; text-decoration: none;"><i class="far fa-bookmark" style="margin-right:5px;"></i>Маркери</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="<?= Url::toRoute('markered/index') ?>" style="color:black; text-decoration: none;"><i class="far fa-image" style="margin-right:5px;"></i>Маркеровані пости</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="<?= Url::toRoute('session/index') ?>" style="color:black; text-decoration: none;"><i class="far fa-clock" style="margin-right:5px;"></i>Сессії користувачів у редакторі</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="<?= Url::toRoute('default/statistic') ?>" style="color:black; text-decoration: none;"><i class="far  fa-chart-bar" style="margin-right:5px;"></i>Статистика</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="<?= Url::toRoute('default/fbcomments') ?>" style="color:black; text-decoration: none;"><i class="fab fa-facebook-square" style="margin-right:5px;"></i>Коментарі</a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
