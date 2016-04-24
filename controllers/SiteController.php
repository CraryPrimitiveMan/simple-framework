<?php
namespace app\controllers;

use Sf;
use sf\web\Controller;
use app\models\User;

class SiteController extends Controller
{
    public function actionTest()
    {
        // $user = new User();
        // $user->name = 'jun1';
        // $user->age = 30;
        // $result = $user->insert();

        // $user = User::findOne(['id' => 10]);
        // $user->name = 'test';
        // $result = $user->update();
        // $result = $user->delete();
        // var_dump($result);die;
        $user = User::findAll();
        // $user = User::deleteAll(['name' => 'jun1']);
        // // $user = User::updateAll(['age' => 21], ['age' => 20]);
        $data = [
            'first' => 'awesome-php-zh_CN',
            'second' => 'simple-framework',
            'user' => $user
        ];
        echo $this->toJson($data);
    }

    public function actionView()
    {
        $cache = Sf::createObject('cache');
        $cache->set('111', '2222');
        $result = $cache->get('111');
        $cache->flush();
        var_dump($result);die;
        $this->render('site/view', ['body' => 'Test body information']);
    }

    public function actionCache()
    {
        $cache = Sf::createObject('cache');
        $cache->set('test', '我就是测试一下缓存组件');
        $result = $cache->get('test');
        $cache->flush();
        echo $result;
    }
}
