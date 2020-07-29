<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\models\User;
use common\models\Token;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Url;
use api\models\Chat;
use api\models\Party;
use api\models\Message;
use api\models\ChatMessage;

class StreamController extends Controller
{   

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' =>  HttpBearerAuth::className(),
            'except' => '',
        ];

        return $behaviors;
    }

    public function actionStreamStart() {
        if(!$_POST['channel_id']){
            throw new \yii\web\HttpException('500','channel_id cannot be blank.'); 
        }
        return $_POST['channel_id'];
    }

    public function actionStreamList() {
        return [];
    }

}