<?php
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 10.06.2019
 * Time: 17:27
 */

use app\components\functions;
use app\models\Auth;
use app\models\MarkeredPosts;
use app\models\Markers;
use app\models\Post;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use Facebook\Facebook;

?>
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
<?php
if ($posts == NULL) {
    ?>
    <div class="container" style="margin-left:auto; margin-right:auto; text-align:center;">
        <div>Ви не додали жодного поста для порівняння.<i class="far fa-frown"></i>Перейдіть на головну сторінку та
            оберіть пости для порівняння.
        </div>
        <a href="<?= Url::toRoute('site/index'); ?>"><i class="far fa-arrow-alt-circle-left fa-7x"></i></a>
    </div>

    <?php
}
if ($source == "instagram") {
    $first_date = new DateTime("now");
    $showed_posts = [];
    $i = 0;
    foreach ($posts as $posted) {
        $access_token = Auth::findOne(['user_id' => $posted['id_user'], 'source' => 'instagram'])->access_token;
        if ($access_token != null) {
            $user_id = Auth::findOne(['user_id' => $posted['id_user'], 'source' => 'instagram'])->source_id;
            $header = 0;
            $method = 0;
            $url = "https://api.instagram.com/v1/users/" . $user_id . "/media/recent/?access_token=" . $access_token;
            $data = 0;
            $json = 1;
            $media = (new Functions)->getDataHTTP($method, $url, $header, $data, $json);
            foreach ($media['data'] as $post) {
                if ($post['id'] == $posted['id_post']) {
                    $showed_posts[$i] = new Post();
                    $showed_posts[$i]->id = $post['id'];
                    $showed_posts[$i]->user = $post['user']['username'];
                    $showed_posts[$i]->photo = $post['images']['thumbnail']['url'];
                    $showed_posts[$i]->date = $post['created_time'];
                    $showed_posts[$i]->likes = $post['likes']['count'];
                    $showed_posts[$i]->comments = $post['comments']['count'];
                    $markers = MarkeredPosts::find()->where(['id_post' => $posted['id_post']])->all();
                    $j = 0;
                    foreach ($markers as $marker) {
                        $mark = Markers::find()->where(['id_marker' => $marker['id_marker']])->one();
                        $showed_posts[$i]->markers[$j] = $mark['name_marker'];
                        $j++;
                    }
                    $i++;
                }
            }
        }
    }
    echo '<table class="table table-hover table-responsive" style="margin-top:120px; vertical-align:middle; text-align:center;">';
    echo '<thead><tr><th>'.Html::img('@web/img/instagram.png', ['style' => 'width:35px; height:35px;']).'</th></tr></thead>';
    echo '<tbody><tr><th><i class="fas fa-minus-circle"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td><a href="' . Url::toRoute(['site/compare', 'id_post' => $show_post->id, 'comparator' => Yii::$app->user->identity->id]) . '">'.Html::img('@web/img/not-done.png', ['style' => 'width:30px; height:30px;']).'</a></td>';
    }
    echo '<tr><th><i class="far fa-image"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td><img style="margin-left:auto; margin-right:auto;" src="' . $show_post->photo . '"></td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-user"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>' . $show_post->user . '</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-clock"></i></th>';
    foreach ($showed_posts as $show_post) {
        $post_data = new DateTime();
        $post_data->setTimestamp($show_post->date);
        $difference = $first_date->diff($post_data);
        echo '<td>' . format_interval($difference) . ' тому.</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-heart"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>' . $show_post->likes . '</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-comment" ></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>' . $show_post->comments . '</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-bookmark"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>';
        foreach($show_post->markers as $mar){
            echo $mar.'<br>';
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';

}else if($source == "facebook"){
    $first_date = new DateTime("now");
    $i=0;
    $showed_posts = [];
    foreach ($posts as $posted) {
        $access_token = Auth::findOne(['user_id' => $posted->id_user, 'source' => 'facebook'])->access_token;
        if ($access_token != null) {
            $response = $fb->get('/me/accounts', $access_token);
            $accounts = $response->getGraphEdge();
            foreach ($accounts as $ac) {
                $resp = $fb->get($ac['id'] . '/feed?fields=id,message,created_time,attachments', $ac['access_token']);
                $posts = $resp->getGraphEdge();
                $check = false;
                foreach ($posts as $post) {
                    if($post['id'] == $posted['id_post']) {
                        $resd = $fb->get('/' . $post['id'] . '/likes', $ac['access_token']);
                        $likes = $resd->getGraphEdge();
                        $resd = $fb->get('/' . $post['id'] . '/comments', $ac['access_token']);
                        $comments = $resd->getGraphEdge();
                        $difference = $first_date->diff($post['created_time']);
                        $dateone = $post['created_time']->format('m/d/Y');
                        if ($dateone == $yesterday) {
                            $check = true;
                        }
                        $user = User::find()->where(['id' => $posted->id_user])->one()->username;
                        $showed_posts[$i] = new Post();
                        $showed_posts[$i]->id = $post['id'];
                        $showed_posts[$i]->user = $user;
                        $showed_posts[$i]->date = $post['created_time'];
                        $showed_posts[$i]->likes = count($likes);
                        $showed_posts[$i]->comments = count($comments);
                        $attachments = $post['attachments'];
                        $showed_posts[$i]->photo = $attachments['0']['media']['image']['src'];
                        $markers = MarkeredPosts::find()->where(['id_post' => $posted['id_post']])->all();
                        $j = 0;
                        foreach ($markers as $marker) {
                            $mark = Markers::find()->where(['id_marker' => $marker['id_marker']])->one();
                            $showed_posts[$i]->markers[$j] = $mark['name_marker'];
                            $j++;
                        }
                        $i++;
                    }
                }
            }
        }
    }
    echo '<table class="table table-hover table-responsive" style="margin-top:60px; vertical-align:middle; text-align:center;">';
    echo '<thead><tr><th>'.Html::img('@web/img/facebook.png', ['style' => 'width:35px; height:35px;']).'</th></tr></thead>';
    echo '<tbody><tr><th><i class="fas fa-minus-circle"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td><a href="' . Url::toRoute(['site/compare', 'id_post' => $show_post->id, 'comparator' => Yii::$app->user->identity->id]) . '">'.Html::img('@web/img/not-done.png', ['style' => 'width:30px; height:30px;']).'</a></td>';
    }
    echo '<tr><th><i class="far fa-image"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td><img style="margin-left:auto; margin-right:auto; width:150px; height:150px;" src="' . $show_post->photo . '"></td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-user"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>' . $show_post->user . '</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-clock"></i></th>';
    foreach ($showed_posts as $show_post) {
        $difference = $first_date->diff($show_post->date);
        echo '<td>' . format_interval($difference) . ' тому.</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-thumbs-up"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>' . $show_post->likes . '</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-comment" ></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>' . $show_post->comments . '</td>';
    }
    echo '</tr>';
    echo '<tr><th><i class="far fa-bookmark"></i></th>';
    foreach ($showed_posts as $show_post) {
        echo '<td>';
        foreach($show_post->markers as $mar){
            echo $mar.'<br>';
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';

}


