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

class ChatController extends Controller
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

    public function actionChat() {
        return Chat::getChat();
    }

    public function actionDeleteChat() {
        $request = Yii::$app->request;
        return Chat::deleteChat($request);
    }

    public function actionGetChatUser() {
        $request = Yii::$app->request;
        return Chat::getChatUser($request);
    }
    public function actionGetCurrentChat() {
        $request = Yii::$app->request;
        return ChatMessage::getCurrentChat($request);
    }

    public function actionReadMessage() {
        $request = Yii::$app->request;
        return Message::readMessage($request);
    }

    public function actionChatMessage() {
        $id = \Yii::$app->user->id;
        return ChatMessage::getChatMessage($id);
    }

    public function actionSendMessage() {
        $request = Yii::$app->request;
        return Chat::addMessage($request);
    }

    public function actionUpdateMessage() {
        $request = Yii::$app->request;
        return Chat::updateMessage($request);
    }

    public function actionDeleteMessage() {
        $request = Yii::$app->request;
        return Chat::deleteMessage($request);
    }

}