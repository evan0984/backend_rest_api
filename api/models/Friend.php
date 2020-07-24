<?php

namespace api\models;

use Yii;
use api\models\UserMsg;
use api\models\User;
/**
 * This is the model class for table "friend".
 *
 * @property int $id
 * @property int $user_source_id
 * @property int $user_target_id
 * @property string|null $created_at
 */
class Friend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'friend';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_source_id', 'user_target_id'], 'required'],
            [['user_source_id', 'user_target_id', 'show'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function getSearch($request)
    {   
        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT ".User::userFields()." FROM `friend` l1 
            INNER JOIN `friend` l2 ON l1.user_source_id = l2.user_target_id AND l2.user_source_id = l1.user_target_id 
            LEFT JOIN `user` ON `user`.`id` = l2.user_source_id
            WHERE l1.user_source_id = ".\Yii::$app->user->id." AND `user`.`username` Like '%".$request."%'");
        $result = $command->queryAll();
        return $result;
    }

    
    public function friendRequestsReject($request)
    { 
        $user_target_id = (int)$request->post('user_target_id');
        if(!$user_target_id){
            throw new \yii\web\HttpException('500','user_target_id cannot be blank.'); 
        }
        $friend = Friend::find()->where(['user_source_id'=>\Yii::$app->user->id, 'user_target_id'=>$user_target_id])->one();
        if ($friend) {
            \Yii::$app
            ->db
            ->createCommand()
            ->delete('friend', ['id' => $friend->id])
            ->execute();
            return ['Request for user id = '.$user_target_id.' was deleted!'];
        } else {
            throw new \yii\web\HttpException('500', 'Request for '.$user_target_id.' not found!');
        }
    }

    public function addFriend($request)
    {   
        $user_target_id = (int)$request->post('user_target_id');
        if(!$user_target_id){
            throw new \yii\web\HttpException('500','user_target_id cannot be blank.'); 
        }
        if($user_target_id == \Yii::$app->user->id){
            throw new \yii\web\HttpException('500','user_target_id can not be your ID'); 
        }
        $friend = Friend::find()->where(['user_source_id'=>\Yii::$app->user->id, 'user_target_id'=>$user_target_id])->one();
        if($friend){
        } else {
            $friend = new Friend();
            $friend->user_source_id = \Yii::$app->user->id;
            $friend->user_target_id = $user_target_id;
            $friend->created_at = time();
            $friend->show = 1;
            $friend->save();
        }
        $send_to = Friend::getFriend($user_target_id);
        User::socket($user_target_id, $send_to, 'Friends');

        $my_friends = Friend::getFriend(\Yii::$app->user->id);
        return $my_friends;
    }

    public function friendRequestsMy()
    {   
        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT `user`.`id` FROM `friend` l1 
            INNER JOIN `friend` l2 ON l1.user_source_id = l2.user_target_id AND l2.user_source_id = l1.user_target_id 
            LEFT JOIN `user` ON `user`.`id` = l2.user_source_id
            WHERE l1.user_source_id = ".\Yii::$app->user->id);
        $result = $command->queryAll();
        $ar = [];
        foreach ($result as $key => $value) {
            array_push($ar, $value['id']);
        }
        return Friend::find()->with(['user'])->where(['user_source_id'=>\Yii::$app->user->id])
        ->andWhere(['not in', 'user_target_id', $ar])
        ->orderBy('id DESC')->all();
    }

    public function friendRequestsToMe()
    {   
        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT `user`.`id` FROM `friend` l1 
            INNER JOIN `friend` l2 ON l1.user_source_id = l2.user_target_id AND l2.user_source_id = l1.user_target_id 
            LEFT JOIN `user` ON `user`.`id` = l2.user_source_id
            WHERE l1.user_source_id = ".\Yii::$app->user->id);
        $result = $command->queryAll();
        $ar = [];
        foreach ($result as $key => $value) {
            array_push($ar, $value['id']);
        }
        $req =  Friend::find()->select(['user_source_id'])->where(['user_target_id'=>\Yii::$app->user->id])
        ->andWhere(['not in', 'user_source_id', $ar])
        ->orderBy('id DESC')->asArray()->all();
        $ar = [];
        foreach ($req as $key => $value) {
            array_push($ar, $value['user_source_id']);
        }
        return UserMsg::find()->where(['in', 'id', $ar])->all();

    }

    public function getFriend($id)
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT ".User::userFields()." FROM `friend` l1 
            INNER JOIN `friend` l2 ON l1.user_source_id = l2.user_target_id AND l2.user_source_id = l1.user_target_id 
            LEFT JOIN `user` ON `user`.`id` = l2.user_source_id
            WHERE l1.user_source_id = ".$id);
        $result = $command->queryAll();
        $friend = [];
        foreach ($result as $key => $value) {
            $images = $command = $connection->createCommand("SELECT `images`.`id`, `images`.`path`  FROM `images` WHERE `user_id`=".$value['id']);
            $result_images = $command->queryAll();
            $ar = [
                'id'=>$value['id'],
                'username'=>$value['username'],
                'phone'=>$value['phone'],
                'image'=>$value['image'],
                'gender'=>$value['gender'],
                'birthday'=>$value['birthday'],
                'status'=>$value['status'],
                'first_name'=>$value['first_name'],
                'last_name'=>$value['last_name'],
                'latitude'=>$value['latitude'],
                'longitude'=>$value['longitude'],
                'address'=>$value['address'],
                'last_activity'=>$value['last_activity'],
                'premium'=>$value['premium'],
                'images'=>$result_images,
            ];
            array_push($friend, $ar);
        }
        return $friend;
    }

    public function addFriendUsername($request)
    {
        $username = $request->post('username');
        if(!$username){
            throw new \yii\web\HttpException('500','username cannot be blank.'); 
        }
        $user = User::find()->where(['username'=>$username])->one();
        if (!$user) {
            throw new \yii\web\HttpException('500','User with username '.$username.' not exist'); 
        }
        if($user->id == \Yii::$app->user->id){
            throw new \yii\web\HttpException('500','user ID can not be your ID'); 
        }
        $friend = Friend::find()->where(['user_source_id'=>\Yii::$app->user->id, 'user_target_id'=>$user->id])->one();
        if($friend){
        } else {
            $friend = new Friend();
            $friend->user_source_id = \Yii::$app->user->id;
            $friend->user_target_id = $user->id;
            $friend->created_at = time();
            $friend->show = 1;
            $friend->save();
        }
        return $friend;
    }



    public function fields()
    {
        return [
            //'id' => 'id',
            'user_source_id' => 'user_source_id',
            'user_target_id' => 'user_target_id', 
            'user_info' => 'user',
        ];
    }

    public function getUser()
    {   
        return $this->hasOne(UserMsg::className(), ['id' => 'user_target_id']);        
    }



}
