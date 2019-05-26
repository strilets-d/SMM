<?php

namespace app\controllers;


use app\models\MarkeredPosts;
use app\models\User;
use Facebook\Facebook;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Signup;
use app\models\Login;
use app\components\AuthHandler;

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
        if(!Yii::$app->user->isGuest)
        {
            Yii::$app->user->logout();
            return $this->redirect(['login']);
        }
    }

    public function actionSignup()
    {
        $model = new Signup();
        $message = "";
        if(isset($_POST['Signup']))
        {

            $model->attributes = Yii::$app->request->post('Signup');
            if($model->validate() && $model->signup())
            {
                return $this->goHome();
            }
            else Yii::$app->getSession()->setFlash('info', 'Вибачте. Щось пішло не так.');
        }

        return $this->render('signup',[
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

    public function actionSetMarker(){
        $model = new MarkeredPosts();
        $model->id_post = Yii::$app->request->get('id_post');
        $model->id_page = Yii::$app->request->get('id_page');
        $model->id_user = Yii::$app->request->get('id_user');
        $model->id_marker = Yii::$app->request->get('id_marker');
        $model->source = Yii::$app->request->get('source');
        if($model->save()){
            return $this->goHome();
        }
    }

    public function actionDeleteMarker(){
        $model = MarkeredPosts::find()->where(['id_post' => Yii::$app->request->get('id_post'),'id_marker' => Yii::$app->request->get('id_marker')])->one();
        if($model->delete()){
            return $this->goHome();
        }
    }

}
