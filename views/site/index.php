<?php

use app\models\Auth;
use app\models\MarkeredPosts;
use app\models\Markers;
use app\models\User;
use Facebook\Facebook;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'SMM';
?>
    <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var offset = $('#fixed').offset();
            var topPadding = 0;
            $(window).scroll(function() {
                if ($(window).scrollTop() > offset.top) {
                    $('#fixed').stop().animate({marginTop: $(window).scrollTop() - offset.top + topPadding});
                }
                else {
                    $('#fixed').stop().animate({marginTop: 0});
                }
            });
        });
    </script>
<?php
$fb = new Facebook([
    'app_id' => '2157096511042786',
    'app_secret' => 'f0ab41a0837c965a3a85ff0b5d37f2bf',
]);
function format_interval(DateInterval $interval)
{
    $result = "";
    if ($interval->y) {
        $result .= $interval->format("%y років ");
    }
    if ($interval->m) {
        $result .= $interval->format("%m місяців ");
    }
    if ($interval->d) {
        $result .= $interval->format("%d днів ");
    }
    if ($interval->h) {
        $result .= $interval->format("%h годин ");
    }
    if ($interval->i) {
        $result .= $interval->format("%i хвилин ");
    }
    return $result;
}

function debug($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}
?>
<?= Html::img('@web/img/main.jpg',['class' => 'main-photo']); ?>
<div class="container">
<?php
$pages = [];
$i = 0;
$check = false;
$first_date = new DateTime("now");
$yesterday = date("m/d/Y", strtotime( '-1 days' ) );
$users = User::findAll(['status' => array(User::STATUS_ACTIVE, User::STATUS_ADMIN)]);
foreach ($users as $user) {
    $access_token = Auth::findOne(['user_id' => $user->id])->access_token;
    if ($access_token != null) {
        $response = $fb->get('/me/accounts', $access_token);
        $accounts = $response->getGraphEdge();
        foreach ($accounts as $ac) {
            $resp = $fb->get($ac['id'] . '/feed?fields=id,message,created_time,attachments', $ac['access_token']);
            $posts = $resp->getGraphEdge();
            $check = false;
            foreach ($posts as $post) {
                $difference = $first_date->diff($post['created_time']);
                $dateone = $post['created_time']->format('m/d/Y');
                if ($dateone == $yesterday) {
                    $check = true;
                }
                echo '
            <div class="post box-shadow">
                <div class="post-header in-row" style="width:100%;">
                    <div  style="width:7%;">
                        <img src="https://graph.facebook.com/' . $ac['id'] . '/picture?access_token=' . $ac['access_token'] . '" class="post-header-photo">             
                    </div>
                    <div style="width:83%;">
                        <div class="post-page">
                        ' . $ac['name'] . $post['story'] . '
                        </div>
                        <div class="post-time"><i class="far fa-clock" ></i><text>' .
                            format_interval($difference) . ' тому.</text>
                        </div>
                    </div>
                    <div class="dropdown" style="margin-right:0; margin-left:auto; width:10%;">
                    <button class="my-button dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    ';
                $markers = Markers::find()->all();
                foreach($markers as $marker){
                    $is = MarkeredPosts::find()->where(['id_marker' => $marker['id_marker'], 'id_post' => $post['id']])->one();
                    if($is != null){
                        echo '<a class="dropdown-item" href="'.Url::toRoute(['site/delete-marker','id_post' => $post['id'], 'id_marker' => $marker['id_marker']]).'"><i class="fas fa-check" style="margin-right:5px;"></i>'.$marker['name_marker'].'</a>';
                    }
                    else echo '<a class="dropdown-item" href="'.Url::toRoute(['site/set-marker','id_page' => $ac['id'], 'id_post' => $post['id'], 'source' => 'facebook', 'id_marker' => $marker['id_marker'], 'id_user' => $user['id']]).'">'.$marker['name_marker'].'</a>';
                }
                echo '
                    </div>
                    </div>
                </div>
                <div class="post-story">
                ' . $post['message'] . '</div>';
                $attachments = $post['attachments'];
                echo '<img class="post-photo" src="' . $attachments['0']['media']['image']['src'] . '">';
                foreach ($attachments as $at) {
                    if ($at['type'] == 'share') {
                        echo '<a style="text-decoration: none;" href="' . $at['url'] . '" ><div class="link"><div class="link-text">' . $at['title'] . '<br><i class="fas fa-external-link-alt fa-3x"></i></i></div></div></a>';
                    }
                }
                echo '</div>';
            }
            if (!$check) {
                $pages[$i]['page'] = $ac['name'];
                $pages[$i]['user'] = $user;
                $i = $i + 1;
            }
        }
    }
}
?>
</div>
<?php
echo '<div class="bonus">';
if($pages!=null){
echo '<div class="box-shadow" style="padding:20px;">';
echo '<p>Не наповнені сторінки:</p>';
foreach ($pages as $page){
    echo Html::img('@web/img/facebook.png',['style' => 'width:30px; height:30px; margin-right:5px;']).$page['page'];
}
echo '</div>';
}
echo '</div>';
?>
