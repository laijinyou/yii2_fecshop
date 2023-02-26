<?php

namespace fecshop\app\appinstall\modules\Database\controllers;

class ErrorController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\web\ErrorAction',
            ],
            //'captcha' => [
            //    'class' => 'yii\captcha\CaptchaAction',
            //    'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            //],
        ];
    }

}
