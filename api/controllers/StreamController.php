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
use api\models\Stream;
use api\models\StreamUser;

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
        if(!isset($_POST['category'])){
            throw new \yii\web\HttpException('500','category cannot be blank.'); 
        }
        if(!isset($_POST['name'])){
            throw new \yii\web\HttpException('500','name cannot be blank.'); 
        }
        $check = Stream::find()->where(['channel'=>$_POST['channel_id']])->one();
        if ($check) {
            throw new \yii\web\HttpException('500','Stream channel already exist!'); 
        }
        $stream = new Stream();
        $stream->user_id = \Yii::$app->user->id;
        $stream->created_at = time();
        $stream->channel = $_POST['channel_id'];
        $stream->category = $_POST['category'];
        $stream->name = $_POST['name'];
        if ($stream->save()) {
            return $stream;
        } else {
            throw new \yii\web\HttpException('500','Error save stream'); 
        }
    }

    public function actionStreamStop() {
        if(!$_POST['channel_id']){
            throw new \yii\web\HttpException('500','channel_id cannot be blank.'); 
        }
        $stream = Stream::find()->where(['channel'=>$_POST['channel_id']])->one();
        if ($stream) {
            \Yii::$app
            ->db
            ->createCommand()
            ->delete('stream', ['id' => $stream->id])
            ->execute();
            return 'Stream deleted!';
        } else {
            throw new \yii\web\HttpException('500','Stream '.$_POST['channel_id'].' not exist');
        }
    }

    public function actionStreamListApi() {
        return Stream::getChannelList();
    }

    public function actionStreamUserListApi() {
        if(!$_POST['channel_id']){
            throw new \yii\web\HttpException('500','channel_id cannot be blank.'); 
        }
        $channel = $_POST['channel_id'];
        return Stream::getUserChannelList($channel);
    }

    public function actionStreamUsers() {
        return Stream::getUsers();
    }

    public function actionStreamJoin() {
        if(!$_POST['channel_id']){
            throw new \yii\web\HttpException('500','channel_id cannot be blank.'); 
        }
        $stream = new StreamUser();
        $stream->user_id = \Yii::$app->user->id;
        $stream->channel = $_POST['channel_id'];
        if ($stream->save()) {
            return $stream;
        } else {
            throw new \yii\web\HttpException('500','Error save stream'); 
        }
    }

    public function actionStreamDisconnect() {
        if(!$_POST['channel_id']){
            throw new \yii\web\HttpException('500','channel_id cannot be blank.'); 
        }
        $stream = new StreamUser();
        $stream->user_id = \Yii::$app->user->id;
        $stream->created_at = time();
        $stream->channel = $_POST['channel_id'];
        if ($stream->save()) {
            return $stream;
        } else {
            throw new \yii\web\HttpException('500','Error save stream'); 
        }
    }

    public function actionStreamInvite() {
        if(!$_POST['friend_id']){
            throw new \yii\web\HttpException('500','frined_id cannot be blank.'); 
        }
        if(!$_POST['channel_id']){
            throw new \yii\web\HttpException('500','channel_id cannot be blank.'); 
        }
        $result = ['user'=>UserMsg::find()->where(['id'=>\Yii::$app->user->id])->one(), 'stream'=>$_POST['channel_id']];
        User::socket($_POST['friend_id'], $result, 'Stream_invite');
    }

}