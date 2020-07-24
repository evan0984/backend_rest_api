<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\models\User;
use common\models\Token;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Url;
use api\models\Friend;

class FriendController extends Controller
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

    public function actionAddFriend() {
        $request = Yii::$app->request;
        return Friend::addFriend($request);
    }

    public function actionFriendRequestsReject() {
        $request = Yii::$app->request;
        return Friend::friendRequestsReject($request);
    }

    public function actionGetFriends() {
        $id = \Yii::$app->user->id;
        return Friend::getFriend($id);
    }

    public function actionFriendRequestsMy() {
        return Friend::friendRequestsMy();
    }

    public function actionFriendRequestsToMe() {
        return Friend::friendRequestsToMe();
    }

    public function actionAddFriendUsername() {
        $request = Yii::$app->request;
        return Friend::addFriendUsername($request);
    }
}