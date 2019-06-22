<?php

use app\models\Auth;
use app\models\MarkeredPosts;
use app\models\Markers;
use app\models\Post;
use app\models\User;
use Facebook\Facebook;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Functions;
use yii\web\JqueryAsset;

$this->title = 'SMM';
?>
<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
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
<?= Html::img('@web/img/main.jpg', ['class' => 'main-photo']); ?>
<div class="container">
    <?php
    $pages = [];
    $i = 0;
    $counter = 0;
    $popular_posts = [];
    $check = false;
    $first_date = new DateTime("now");
    $yesterday = date("m/d/Y", strtotime('-1 days'));
    $users = User::findAll(['status' => array(User::STATUS_ACTIVE, User::STATUS_ADMIN)]);
    foreach ($users as $user) {
        $access_token = Auth::findOne(['user_id' => $user->id, 'source' => 'facebook'])->access_token;
        if ($access_token != null) {
            $response = $fb->get('/me/accounts', $access_token);
            $accounts = $response->getGraphEdge();
            foreach ($accounts as $ac) {
                $resp = $fb->get($ac['id'] . '/feed?fields=id,message,created_time,attachments', $ac['access_token']);
                $posts = $resp->getGraphEdge();
                $check = false;
                foreach ($posts as $post) {
                    $resd = $fb->get('/' . $post['id'] . '/likes', $ac['access_token']);
                    $likes = $resd->getGraphEdge();
                    $resd = $fb->get('/' . $post['id'] . '/comments', $ac['access_token']);
                    $comments = $resd->getGraphEdge();
                    $difference = $first_date->diff($post['created_time']);
                    $dateone = $post['created_time']->format('m/d/Y');
                    if ($dateone == $yesterday) {
                        $check = true;
                    }
                    $popular_posts[$counter] = new Post();
                    $popular_posts[$counter]->id = $post['id'];
                    $popular_posts[$counter]->source = "facebook";
                    $popular_posts[$counter]->user = $ac['name'];
                    $popular_posts[$counter]->likes = count($likes);
                    $popular_posts[$counter]->comments = count($comments);
                    echo '
            <div class="post box-shadow" id="'.$post['id'].'">
                <div class="post-header in-row" style="width:100%;">
                    <div  style="width:7%;">
                        <img src="https://graph.facebook.com/' . $ac['id'] . '/picture?access_token=' . $ac['access_token'] . '" class="post-header-photo">             
                    </div>
                    <div style="width:83%;">
                        <div class="post-page">
                            ' . $ac['name'] . Html::img('@web/img/facebook.png', ['style' => 'width:20px; height:20px; margin-left:5px;']) . $post['story'] . '
                        </div>
                        <div class="post-time"><i class="far fa-clock" ></i><text>' .
                        format_interval($difference) . ' тому.</text>
                        </div>
                    </div>
                    <div class="dropdown" style="margin-right:0; margin-left:auto; width:10%;">
                        <button class="my-button dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    if(Yii::$app->user->identity->admin){
                    $markers = Markers::find()->all();
                    foreach ($markers as $marker) {
                        $is = MarkeredPosts::find()->where(['id_marker' => $marker['id_marker'], 'id_post' => $post['id']])->one();
                        if ($is != null) {
                            echo '<a class="dropdown-item" href="' . Url::toRoute(['site/delete-marker', 'id_post' => $post['id'], 'id_marker' => $marker['id_marker']]) . '"><i class="fas fa-check" style="margin-right:5px;"></i>' . $marker['name_marker'] . '</a>';
                        } else echo '<a class="dropdown-item" href="' . Url::toRoute(['site/set-marker', 'id_page' => $ac['id'], 'id_post' => $post['id'], 'source' => 'facebook', 'id_marker' => $marker['id_marker'], 'id_user' => $user['id']]) . '">' . $marker['name_marker'] . '</a>';
                    }
                        echo '<a class="dropdown-item" href="' . Url::toRoute(['site/post-stat', 'id_post' => $post['id'], 'source' => 'facebook', 'id_user' => $user['id']]) . '"><i class="far fa-chart-bar"></i>Статистика</a>';
                    }
                    echo '
                                <a class="dropdown-item compare-button" href="javascript:void(0)" post-id="' . $post['id'] . '" user-id="' . $user['id'] . '" source="facebook">Порівняти</a>
                            </div>
                    </div>
                </div>
                <div class="post-story">
                ' . $post['message'] . '</div>';
                    $attachments = $post['attachments'];
                    if($attachments!=null) {
                        echo '<img class="post-photo" src="' . $attachments['0']['media']['image']['src'] . '">';
                        $popular_posts[$counter]->photo = $attachments['0']['media']['image']['src'];
                        foreach ($attachments as $at) {
                            if ($at['type'] == 'share') {
                                echo '<a style="text-decoration: none;" href="' . $at['url'] . '" ><div class="link"><div class="link-text">' . $at['title'] . '<br><i class="fas fa-external-link-alt fa-3x"></i></i></div></div></a>';
                            }
                        }
                    }
                    echo '<div class="post-stat"><i class="far fa-thumbs-up" style="margin-left:15px; margin-right:5px;"></i>' . count($likes) . '<i class="far fa-comment" style="margin-left:15px; margin-right:5px;"></i>' . count($comments) . '</div>';
                    echo '</div>';
                    $counter++;
                }
                if (!$check) {
                    $pages[$i]['page'] = $ac['name'];
                    $pages[$i]['user'] = $user;
                    $pages[$i]['source'] = "facebook";
                    $i = $i + 1;
                }
            }
        }
    }
    foreach ($users as $user) {
        $access_token = Auth::findOne(['user_id' => $user->id, 'source' => 'instagram'])->access_token;
        if ($access_token != null) {
            $user_id = Auth::findOne(['user_id' => $user->id, 'source' => 'instagram'])->source_id;
            $header = 0;
            $method = 0;
            $url = "https://api.instagram.com/v1/users/" . $user_id . "/media/recent/?access_token=" . $access_token;
            $data = 0;
            $json = 1;
            $media = (new Functions)->getDataHTTP($method, $url, $header, $data, $json);
            //debug($media);
            foreach ($media['data'] as $post) {
                $post_data = new DateTime();
                $post_data->setTimestamp($post['created_time']);
                $difference = $first_date->diff($post_data);
                $dateone = $post_data->format('m/d/Y');
                if ($dateone == $yesterday) {
                    $check = true;
                }
                $popular_posts[$counter] = new Post();
                $popular_posts[$counter]->id = $post['id'];
                $popular_posts[$counter]->source = "instagram";
                $popular_posts[$counter]->user = $post['user']['full_name'];
                $popular_posts[$counter]->likes = $post['likes']['count'];
                $popular_posts[$counter]->comments = $post['comments']['count'];
                echo '
            <div class="post box-shadow" id="'.$post['id'].'">
                <div class="post-header in-row" style="width:100%;">
                    <div  style="width:7%;">
                        <img src="' . $post['user']['profile_picture'] . '" class="post-header-photo">             
                    </div>
                    <div style="width:83%;">
                        <div class="post-page">
                        ' . $post['user']['full_name'] . Html::img('@web/img/instagram.png', ['style' => 'width:20px; height:20px; margin-left:5px;']) . $post['story'] . '
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
                if(Yii::$app->user->identity->admin) {
                    $markers = Markers::find()->all();
                    foreach ($markers as $marker) {
                        $is = MarkeredPosts::find()->where(['id_marker' => $marker['id_marker'], 'id_post' => $post['id']])->one();
                        if ($is != null) {
                            echo '<a class="dropdown-item" href="' . Url::toRoute(['site/delete-marker', 'id_post' => $post['id'], 'id_marker' => $marker['id_marker']]) . '"><i class="fas fa-check" style="margin-right:5px;"></i>' . $marker['name_marker'] . '</a>';
                        } else echo '<a class="dropdown-item" href="' . Url::toRoute(['site/set-marker', 'id_page' => $post['user']['id'], 'id_post' => $post['id'], 'source' => 'instagram', 'id_marker' => $marker['id_marker'], 'id_user' => $user['id']]) . '">' . $marker['name_marker'] . '</a>';
                    }
                }
                echo '<a class="dropdown-item" href="' . Url::toRoute(['site/post-stat', 'id_post' => $post['id'], 'source' => 'instagram', 'id_user' => $user['id']]) . '"><i class="far fa-chart-bar"></i>Статистика</a>';
                echo '
            <a class="dropdown-item compare-button" href="javascript:void(0)" post-id="' . $post['id'] . '" user-id="' . $user->id . '" source="instagram">Порівняти</a>
                    </div>
                    </div>
                </div>
                <div class="post-story">
                ' . $post['caption']['text'] . '</div>';
                echo '<img class="post-photo" src="' . $post['images']['standard_resolution']['url'] . '">';
                $popular_posts[$counter]->photo = $post['images']['thumbnail']['url'];
                echo '<div class="post-stat"><i class="far fa-heart" style="margin-left:15px; margin-right:5px;"></i>' . $post['likes']['count'] . '<i class="far fa-comment" style="margin-left:15px; margin-right:5px;"></i>' . $post['comments']['count'] . '</div>';
                echo '</div>';
                $counter++;
            }
            if (!$check) {
                $pages[$i]['page'] = $post['user']['full_name'];
                $pages[$i]['user'] = $user;
                $pages[$i]['source'] = "instagram";
                $i = $i + 1;
            }
        }
    }
    ?>
</div>
<?php
echo '<div class="bonus">';
if ($pages != null) {
    echo '<div class="box-shadow" style="padding:20px;">';
    echo '<p>Не наповнені сторінки:</p>';
    foreach ($pages as $page) {
        echo '<div style="margin-top:5px;">' . Html::img('@web/img/' . $page['source'] . '.png', ['style' => 'width:30px; height:30px; margin-right:5px;']) . $page['page'] . '</div>';
    }
    echo '</div>';
}
if($popular_posts!= null) {
    for ($i = 0; $i < count($popular_posts) - 1; $i++) {
        for ($j = 0; $j < count($popular_posts) - 1; $j++) {
            if ($popular_posts[$j]->likes < $popular_posts[$j + 1]->likes) {
                $t = new Post();
                $t = $popular_posts[$j];
                $popular_posts[$j] = $popular_posts[$j + 1];
                $popular_posts[$j + 1] = $t;
            }
        }
    }
    echo '<div class="box-shadow" style="padding:20px; margin-top:20px;">';
    echo '<p>Найпопулярніші пости:</p>';
    if (count($popular_posts > 3)) {
        $count = 3;
    } else $count = count($popular_posts);
    for ($i = 0; $i < $count; $i++) {
        echo Html::img('@web/img/' . $popular_posts[$i]->source . '.png', ['style' => 'width:30px; height:30px; margin-right:5px; margin-top:5px;']) . $popular_posts[$i]->user . '<br>';
        echo '<div style="text-align:center;" ><a href="#'.$popular_posts[$i]->id.'">'.Html::img($popular_posts[$i]->photo, ['style' => 'width:150px; height:150px; margin-top:5px;', 'class' => 'grow']).'</a></div><br>';
    }
    echo '</div>';
}
echo '</div>';
?>
<?php $this->registerJsFile('@web/js/compare.js', [
    'depends' => JqueryAsset::className(),
]);
?>
