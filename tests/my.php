<?php

namespace tests\my;
use app\models\User;
use app\models\LoginForm;
use app\models\MarkeredPosts;
use DateTime;
use InvalidArgumentException;
use Yii;

require_once 'D:\Program Files (x86)\OSPanel\domains\smm.strilets\models\User.php';
require_once 'D:\Program Files (x86)\OSPanel\domains\smm.strilets\models\MarkeredPosts.php';
require_once 'D:\Program Files (x86)\OSPanel\domains\smm.strilets\models\LoginForm.php';
class MyUnitTest extends\PHPUnit\Framework\TestCase
{
    /**
     * @var array|string the application configuration that will be used for creating an application instance for each test.
     * You can use a string to represent the file path or path alias of a configuration file.
     * The application configuration array may contain an optional `class` element which specifies the class
     * name of the application instance to be created. By default, a [[\yii\web\Application]] instance will be created.
     */

    public function testLogin(){
        $login = new LoginForm();
        $user = array();
        array_push($user,['username'=>'Test1','password'=>'test1111','rememberMe'=>true]);
        $this->assertTrue($login->loginTest($user));
    }

    public function testPass(){
        $user = new User();
        $test['password_hash'] = sha1('123');
        $this->assertTrue($user->checkPassword($test));
    }

    public function testExpectEcho()
    {
        $this->expectOutputString('echo');
        print 'echo';
    }

    public function testCount()
    {
        $posts = array();
        $model = new MarkeredPosts();
        array_push($posts, ['id' => '0','marker' => 'розіграш']);
        array_push($posts, ['id' => '1','marker' => 'розіграш']);
        array_push($posts, ['id' => '1','marker' => 'привітання']);
        $this->assertEquals($model->getKil($posts), 2);
    }
}
