<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\models\User;
use common\models\Token;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Url;
use api\models\Like;


class LikeController extends Controller
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

    public function actionSendLike() {
        $request = Yii::$app->request;
        $user_target_id = (int)$request->post('user_target_id');
        $is_like = (int)$request->post('is_like');
        return
        [
            'last_like_data' => Like::sendLike($user_target_id, $is_like),
            'like_info' => Like::getLikeInfo(),
        ];
    }

    public function actionSendLikeAll() {
        $request = Yii::$app->request;
        return Like::sendLikeAll($request);
    }

    public function actionGetMatch() {
        return Like::getMatch();
    }

    public function actionSwipe() {
        return Like::swipe();
    }

    

}