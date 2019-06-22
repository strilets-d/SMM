
<?php
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 12.06.2019
 * Time: 1:20
 */

use app\models\Auth;
use app\models\Post;
use app\models\User;
use Facebook\Facebook;
use yii\web\JqueryAsset;

$fb = new Facebook([
    'app_id' => '2157096511042786',
    'app_secret' => 'f0ab41a0837c965a3a85ff0b5d37f2bf',
]);
?>
<?php
$i = 0;
$counter = 0;
$popular_posts = [];
$check = false;
$first_date = new DateTime("now");
$yesterday = date("m/d/Y", strtotime('-1 days'));
$users = User::findAll(['status' => array(User::STATUS_ACTIVE, User::STATUS_ADMIN)]);
$small_date=null;
$big_date=null;
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

            }
        }
    }
}
?>
<form>
    <div class="form-group">
        <label for="socselector">Соцмережа</label>
        <select class="form-control" id="socselector">
            <option>instagram</option>
            <option>facebook</option>
        </select>
    </div>
    <div class="form-group">
        <label for="datepicker1">З:</label>
        <input type="email" class="form-control" id="datepicker1" placeholder="00/00/0000">
    </div>
    <div class="form-group">
        <label for="datepicker2">По:</label>
        <input type="email" class="form-control" id="datepicker2" placeholder="00/00/0000">
    </div>

</form>

<?php $this->registerJsFile('@web/js/date.js', [
    'depends' => JqueryAsset::className(),
]);
?>