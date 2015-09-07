<?php
namespace app\controllers;

use sf\web\Controller;

class SiteController extends Controller
{
    public function actionTest()
    {
        $data = ['first' => 'awesome-php-zh_CN', 'second' => 'simple-framework'];
        echo $this->toJson($data);
    }

    public function actionView()
    {
        $this->render('site/view', ['body' => 'Test body information']);
    }
}