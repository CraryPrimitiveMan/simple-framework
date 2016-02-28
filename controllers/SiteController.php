<?php
namespace app\controllers;

use sf\web\Controller;
use app\models\User;

class SiteController extends Controller
{
    public function actionTest()
    {
        $user = User::findOne(['age' => 20, 'name' => 'harry']);
        $data = [
            'first' => 'awesome-php-zh_CN',
            'second' => 'simple-framework',
            'user' => $user
        ];
        echo $this->toJson($data);
    }

    public function actionView()
    {
        $this->render('site/view', ['body' => 'Test body information']);
    }
}
