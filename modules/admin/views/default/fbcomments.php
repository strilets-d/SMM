<?php
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 13.06.2019
 * Time: 16:46
 */

use app\models\Auth;
use app\models\Comments;
use app\models\User;
use Facebook\Facebook;
use yii\helpers\Html;
use yii\web\JqueryAsset;

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
            echo '<div class="in-row" style="width:100%; margin-top:20px;">';
            echo '<div style="width:10%;"><img src="https://graph.facebook.com/' . $ac['id'] . '/picture?access_token=' . $ac['access_token'] . '" class="post-header-photo"></div>';
            $username = User::find()->where(['id' => $user->id])->one()->username;
            echo '<div style="width:80%;"><h3>' . $ac['name'] . '(' . $username . ')</h3></div></div>';
            foreach ($posts as $post) {
                $resd = $fb->get('/' . $post['id'] . '/comments?fields=from,id,parent,message&filter=stream&access_token=' . $ac['access_token'] . '&filter=stream');
                $comments = $resd->getGraphEdge();
                if ($comments != null) {
                    foreach ($comments as $comment) {
                        $see = Comments::find()->where(['source_id' => $comment['id']])->one()->see;
                        if( $see == 1){
                            $see=true;
                        }else $see=false;
                        if ($see) {
                            if ($comment['parent']['id']) {
                                echo '<div class="box-shadow" style="margin-left:50px; margin-top:10px; margin-bottom:10px; padding:20px;">';

                            } else {
                                echo '<div class="box-shadow" style=" margin-top:10px; margin-bottom:10px; padding:20px;">';
                            }
                            echo '<div class="post-page">' . $comment['from']['name'] . '</div><div class="in-row" style="width:100%;"><div style="width:95%;">' . $comment['message'] . '</div><div style="width:5%;"><a href="javascript:void(0)" class="eye-button" id="' . $comment['id'] . '" source="facebook" comment-id="' . $comment['id'] . '">';
                            echo '<i class="fas fa-eye-slash"></i></a></div></div></div>';
                        } else {
                            if ($comment['parent']['id']) {
                                echo '<div class="box-shadow" style="margin-left:50px; margin-top:10px; margin-bottom:10px; padding:20px;">';
                            } else {
                                echo '<div class="box-shadow" style="margin-top:10px; margin-bottom:10px; padding:20px;" >';
                            }
                                echo '<div class="post-page">' . $comment['from']['name'] . '</div>';
                                    echo '<div class="in-row" style="width:100%;">';
                                        echo '<div style="width:95%;">' . $comment['message'] . '</div>';
                                        echo '<div style="width:5%;">';
                                            echo '<a href="javascript:void(0)" class="eye-button" id="' . $comment['id'] . '" source="facebook" comment-id="' . $comment['id'] . '">';
                                            echo '<i class="fas fa-eye"></i></a>';
                                        echo '</div>';
                                    echo '</div>';
                            echo '</div>';
                        }
                    }

                }
                $difference = $first_date->diff($post['created_time']);
            }
        }
    }
}
?>
<?php $this->registerJsFile('@web/js/comment.js', [
    'depends' => JqueryAsset::className(),
]);
?>
