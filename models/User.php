<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $admin
 *
 * @property Auth[] $auths
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    const STATUS_INSERTED  = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_ADMIN = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['status',  'admin'], 'integer'],
            [['username','password_hash','created_at', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Ім\'я користувача',
            'password_hash' => 'Хеш паролю',
            'email' => 'Електронна пошта',
            'status' => 'Статус',
            'created_at' => 'Створений',
            'admin' => 'Адмін'
        ];
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->password_hash = sha1($this->password_hash);
        }
        return parent::beforeSave($insert); //
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }
    public function setPassword($password)
    {
        $this->password_hash = sha1($password);
    }

    public function validatePassword($password)
    {
        return $this->password_hash === sha1($password);
    }

    //=============================================
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {

    }

    public function getAuthKey()
    {

    }

    public function validateAuthKey($authKey)
    {

    }
}
