<?php
namespace app\components;

use app\models\Auth;
use app\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        if(Yii::$app->user->isGuest){
            Yii::$app->user->logout();
        }
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');
        $username = ArrayHelper::getValue($attributes, 'name');
        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();
        if (Yii::$app->user->isGuest){
            if ($auth) { // login
                /* @var User $user */
                $user = $auth->user;
                $this->updateUserInfo($user);
                if($user->status == User::STATUS_BLOCKED){
                    $mes = 'Ви не можете увійти.Ваш аккаунт заблоковано.';
                    Yii::$app->getSession()->setFlash('danger', $mes);
                }else {
                    Yii::$app->user->login($user);
                }
            } else { // signup
                if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $this->client->getTitle()]),
                    ]);
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new User();
                        $user->username = $username;
                        $user->email = $email;
                        $user->password_hash = $password;
                        $user->status = User::STATUS_ACTIVE;
                    if ($user->save()) {
                        $auth = new Auth();
                        $auth->user_id = $user->id;
                        $auth->source = $this->client->getId();
                        $auth->source_id = (string)$id;
                        if ($auth->save()) {

                            Yii::$app->user->login($user);
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
        }
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {

    }
}