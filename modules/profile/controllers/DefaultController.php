<?php

namespace app\modules\profile\controllers;

use app\components\FBConnection;
use app\models\Auth;
use Facebook\Facebook;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Default controller for the `profile` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionEditor()
    {
        return $this->render('editor');
    }

    public function actionFbToken()
    {
        $fb = new Facebook([
            'app_id' => '2157096511042786',
            'app_secret' => 'f0ab41a0837c965a3a85ff0b5d37f2bf',
            'default_graph_version' => 'v2.4',
        ]);;
        $helper = $fb->getRedirectLoginHelper();
        if (\Yii::$app->request->get('code')) {
            try {
                if (!$accessToken = $helper->getAccessToken()) throw new UserException("No access token");
                $oAuth2Client = $fb->getOAuth2Client();
                $tokenMetadata = $oAuth2Client->debugToken($accessToken);
                $tokenMetadata->validateAppId('2157096511042786');
                $tokenMetadata->validateExpiration();
                if (!$accessToken->isLongLived())
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                $message = $accessToken->getValue();
                $Auths = Auth::findOne(['user_id' => Yii::$app->user->identity->id]);
                $Auths->access_token = $message;
                $Auths->save();
            } catch (FacebookResponseException $e) {
                $message = 'Graph returned an error: ' . $e->getMessage();
            } catch (FacebookSDKException $e) {
                $message = 'Facebook SDK returned an error: ' . $e->getMessage();
            } catch (UserException $e) {
                $message = "UserException " . $e->getMessage();
            }

            $this->goHome();

        } else {
            $login_url = $helper->getLoginUrl(Url::to('profile/default/fb-token', 1), [
                'manage_pages', // !!! крайне важное разрешение чтобы публиковать в свои группы
                'pages_manage_cta',
                'pages_manage_instant_articles',
                'pages_show_list',
                'user_posts',
                'publish_pages',
                'read_insights',

            ]);
            return $this->redirect($login_url);
        }
    }
}

