<?php

namespace app\models;

use yii\base\Model;

class Signup extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [

            [['username','password'],'required', 'message' => 'Заповніть поле {attribute}'],
            ['username','unique','targetClass'=>'app\models\User'],
            ['password','string','min'=>2,'max'=>12]
        ];
    }

    public function signup()
    {
        $user = new User();
        $user->username = $this->username;
        $user->password_hash = $this->password;
        return $user->save();
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логін',
            'password' => 'Пароль'
        ];
    }

}