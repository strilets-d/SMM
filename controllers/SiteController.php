<?php

namespace app\controllers;


use app\models\Auth;
use app\models\MarkeredPosts;
use app\models\Post;
use app\models\PostsForComparison;
use app\models\User;
use DateTime;
use Facebook\Facebook;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Signup;
use app\models\Login;
use app\components\AuthHandler;
use app\components\Functions;
use yii\web\Response;
use kartik\report\Report;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogout()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect(['login']);
        }
    }

    public function actionSignup()
    {
        $model = new Signup();
        $message = "";
        if (isset($_POST['Signup'])) {

            $model->attributes = Yii::$app->request->post('Signup');
            if ($model->validate() && $model->signup()) {
                return $this->goHome();
            } else Yii::$app->getSession()->setFlash('info', 'Вибачте. Щось пішло не так.');
        }

        return $this->render('signup', [
            'model' => $model,
            'message' => $message
        ]);
    }


    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $login_model = new Login();

        if (Yii::$app->request->post('Login')) {
            $login_model->attributes = Yii::$app->request->post('Login');

            if ($login_model->validate()) {
                Yii::$app->user->login($login_model->getUser());
                return $this->goHome();
            }
        }

        return $this->render('login', ['login_model' => $login_model]);
    }

    public function actionSetMarker()
    {
        $model = new MarkeredPosts();
        $model->id_post = Yii::$app->request->get('id_post');
        $model->id_page = Yii::$app->request->get('id_page');
        $model->id_user = Yii::$app->request->get('id_user');
        $model->id_marker = Yii::$app->request->get('id_marker');
        $model->source = Yii::$app->request->get('source');
        if ($model->save()) {
            return $this->goHome();
        }
        return $this->goHome();
    }

    public function actionDeleteMarker()
    {
        $model = MarkeredPosts::find()->where(['id_post' => Yii::$app->request->get('id_post'), 'id_marker' => Yii::$app->request->get('id_marker')])->one();
        if ($model->delete()) {
            return $this->goHome();
        }
        return $this->goHome();
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAdd()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('site/login');
        }
        $message = "";
        $error = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post_id = $_POST['post-id'];
        $comparator = Yii::$app->user->identity->id;
        $user_id = $_POST['user-id'];
        $source = $_POST['source'];
        $post = PostsForComparison::find()->where(['id_post' => $post_id, 'comparator' => $comparator])->all();
        $sources = PostsForComparison::find()->where(['comparator' => $comparator])->one()->source;
        if ($post == NULL) {
            if ($sources != null) {
                if ($source == $sources) {
                    $model = new PostsForComparison();
                    $model->id_post = $post_id;
                    $model->id_user = $user_id;
                    $model->source = $source;
                    $model->comparator = $comparator;
                    $model->save();
                } else {
                    $message = 'Можна порівнювати пости тільки однієї соцмережі.(' . $sources . ').';
                    $error = true;
                }
            } else {
                $model = new PostsForComparison();
                $model->id_post = $post_id;
                $model->id_user = $user_id;
                $model->source = $source;
                $model->comparator = $comparator;
                $model->save();
            }
        } else {
            $message = 'Ви вже додали цей пост у порівняння.';
            $error = true;
        }
        $count = PostsForComparison::find()->where(['comparator' => $comparator])->count();
        return [
            'success' => true,
            'count' => $count,
            'message' => $message,
            'error' => $error,
        ];
    }

    public function actionInstagram()
    {
        $code = $_GET['code'];
        $header = 0;
        $method = 0;
        $url = "https://api.instagram.com/oauth/access_token";
        $data = array(
            "client_id" => "532eb0817c3f4f29b6dfcd6cddf1196f",
            "client_secret" => "3a5eb5538ab34c13996a91651d4a26fc",
            "redirect_uri" => "https://smm.strilets/site/instagram",
            "grant_type" => "authorization_code",
            "code" => $code
        );
        $json = 1;
        $get_access_token = (new Functions)->getDataHTTP($method, $url, $header, $data, $json);
        $access_token = $get_access_token['access_token'];
        $get = file_get_contents("https://api.instagram.com/v1/users/self/?access_token=$access_token");
        $json = json_decode($get, true);
        $id = $json['data']['id'];
        $username = $json['data']['username'];
        $image = $json['data']['profile_picture'];
        $auth = Auth::find()->where([
            'source' => "instagram",
            'source_id' => $id,
        ])->one();
        if ($auth) { // login
            /* @var User $user */
            $user = $auth->user;
            $user->username = $username;
            $user->image = $image;
            if ($user->status == User::STATUS_BLOCKED) {
                $mes = 'Ви не можете увійти.Ваш аккаунт заблоковано.';
                Yii::$app->getSession()->setFlash('danger', $mes);
            } else {
                Yii::$app->user->login($user);
                return $this->goHome();
            }
        } else {
            $email = "none";
            $password = Yii::$app->security->generateRandomString(6);
            $user = new User();
            $user->username = $username;
            $user->email = $email;
            $user->password_hash = $password;
            $user->status = User::STATUS_ACTIVE;
            $user->image = $image;
            if ($user->save()) {
                $auth = new Auth();
                $auth->user_id = $user->id;
                $auth->source = "instagram";
                $auth->source_id = (string)$id;
                $auth->access_token = $access_token;
                if ($auth->save()) {

                    Yii::$app->user->login($user);
                    return $this->goHome();
                } else {
                    Yii::$app->getSession()->setFlash('error',
                        Yii::t('app', 'Unable to save {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ])
                    );
                }
            } else {
                Yii::$app->getSession()->setFlash('error',
                    Yii::t('app', 'Unable to save user: {errors}', [
                        'client' => $this->client->getTitle(),
                        'errors' => json_encode($user->getErrors()),
                    ])
                );
            }
        }
    }

    public function actionCompare()
    {
        if (isset($_GET['id_post']) && isset($_GET['comparator'])) {
            $id_post = $_GET['id_post'];
            $comparator = $_GET['comparator'];
            $model = PostsForComparison::find()->where(['id_post' => $id_post, 'comparator' => $comparator])->one();
            $model->delete();
        }
        $posts = PostsForComparison::find()->where(['comparator' => Yii::$app->user->identity->id])->all();
        $post = PostsForComparison::find()->where(['comparator' => Yii::$app->user->identity->id])->one();
        $source = $post->source;
        return $this->render('compare', [
            'posts' => $posts,
            'source' => $source,
        ]);
    }

    public function actionPostStat()
    {
        $id_post = $_GET['id_post'];
        $source = $_GET['source'];
        $id_user = $_GET['id_user'];
        $export = new Post();
        if ($source == "instagram") {
            $access_token = Auth::findOne(['user_id' => $id_user, 'source' => 'instagram'])->access_token;
            if ($access_token != null) {
                $user_id = Auth::findOne(['user_id' => $id_user, 'source' => 'instagram'])->source_id;
                $header = 0;
                $method = 0;
                $url = "https://api.instagram.com/v1/users/" . $user_id . "/media/recent/?access_token=" . $access_token;
                $data = 0;
                $json = 1;
                $media = (new Functions)->getDataHTTP($method, $url, $header, $data, $json);
                foreach ($media['data'] as $post) {
                    $post_data = new DateTime();
                    $post_data->setTimestamp($post['created_time']);
                    $dateone = $post_data->format('m.d.Y H:i:s');
                    if ($post['id'] == $id_post) {
                        $export->id = $post['id'];
                        $export->user = $post['user']['full_name'];
                        $export->likes = $post['likes']['count'];
                        $export->comments = $post['comments']['count'];
                        $export->date = $dateone;
                    }
                }
            }
            $username = User::find()->where(['id' => $id_user])->one()->username;
            $report = Yii::$app->report;

// set your template identifier (override global defaults)
            $report->templateId = 1744;
            $report->outputFileName = $id_post . '.docx';

// If you want to override the output file type, uncomment line below
            $report->outputFileType = Report::OUTPUT_DOCX;

// If you want to override the output file action, uncomment line below
// $report->outputFileAction = Report::ACTION_GET_DOWNLOAD_URL;
// Configure your report data. Each of the keys must match the template
// variables set in your MS Word template and each value will be the
// evaluated to replace the Word template variable. If the value is an
// array, it will treated as tabular data.
            $report->templateVariables = [
                'id' => $id_post,
                'insta_page' => $export->user,
                'insta_name' => $username,
                'insta_hearts' => $export->likes,
                'comentari' => $export->comments,
                'public_date' => $export->date,
            ];
            return $report->generateReport();
        }
    }
}
