<?php

namespace api\models;

use Yii;
use api\models\Chat;
use api\models\Party;
use api\models\Message;
use api\models\UserMsg;
use api\models\ChatMessage;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $user_id
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'integer'],
        ];
    }

    public static function getChatUser($request)
    {   
        $user_target_id = (int)$request->post('user_target_id');
        if(!$user_target_id){
            throw new \yii\web\HttpException('500','user_target_id cannot be blank.'); 
        }
        if($user_target_id == \Yii::$app->user->id){
            throw new \yii\web\HttpException('500','user_target_id = your id.'); 
        }
        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("
            SELECT * FROM `party` WHERE `party`.`chat_id` IN (SELECT `chat_id` FROM `party` WHERE `party`.`user_id` = ".\Yii::$app->user->id.") 
            AND `party`.`user_id` = ".$user_target_id);
        $result = $command->queryAll();
        return [
            'chat_id' => $result[0]['chat_id']
        ];
    }

    //return all chats for auth user
    public static function getChat()
    {   
        $result = Party::find()->select('chat_id')->where(['user_id'=>\Yii::$app->user->id])->distinct()->all();
        return $result;
    }

    public function getSearch($request)
    {   
        $my_chats = Party::find()->select('chat_id')->where(['user_id'=>\Yii::$app->user->id])->distinct()->all();
        $array = [];
        foreach ($my_chats as $key => $value) {
            array_push($array, $value['chat_id']);
        }
        $array = implode("','",$array);
        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT ".User::userFields().", `message`.`text`, `message`.`chat_id`, `message`.`created_at` FROM `message`
            LEFT JOIN `client` ON `client`.`id` = `message`.`user_id`
            WHERE `message`.`user_id` <> ".\Yii::$app->user->id." AND `message`.`text` Like '%".$request."%' AND `message`.`chat_id` IN ('".$array."')");
        $result = $command->queryAll();
        return $result;
    }

    //create new message
    public static function addMessage($request)
    {   
        if(!$request->post('send_to')){
            throw new \yii\web\HttpException('500','send_to cannot be blank.'); 
        }
        $chat_id = (int)$request->post('chat_id');
        if (!$chat_id) {
            $chat = new Chat();
            $chat->user_id = \Yii::$app->user->id;
            if ($chat->save()) {
                $chat_id = $chat->id;
                $party1 = new Party();
                $party1->user_id = \Yii::$app->user->id;
                $party1->chat_id = $chat_id;
                $party1->save();
                $party2 = new Party();
                $party2->user_id = $request->post('send_to');
                $party2->chat_id = $chat_id;
                $party2->save();
            }
        } else {
            $checkParty1 = Party::find()->where(['chat_id'=>$chat_id, 'user_id'=>\Yii::$app->user->id])->one();
            if (!$checkParty1) {
                $party1 = new Party();
                $party1->user_id = \Yii::$app->user->id;
                $party1->chat_id = $chat_id;
                $party1->save();
            }
            $checkParty2 = Party::find()->where(['chat_id'=>$chat_id, 'user_id'=>$request->post('send_to')])->one();
            if (!$checkParty2) {
                $party2 = new Party();
                $party2->user_id = $request->post('send_to');
                $party2->chat_id = $chat_id;
                $party2->save();
            }
        }
        $message = new Message();
        $message->chat_id = $chat_id;
        $message->user_id = \Yii::$app->user->id;
        $message->status = 0;
        $message->text = $request->post('text');
        $message->created_at = time();

        //image
        if( count($_FILES)>0 AND $_FILES['image']['tmp_name'] ) {
            $file_name = uniqid().'.jpg';   
            $temp_file_location = $_FILES['image']['tmp_name']; 
            User::s3Upload('chat/', $file_name, $temp_file_location);
            $message->image = env('AWS_S3_PLUZO').'chat/'.$file_name;
        }
        $message->save();
        $result = Chat::getMessagers($message->chat_id, $message->id);
        User::socket($request->post('send_to'), (array)$result, 'Chat');
        return $result;
    }

    public static function getMessagers($chat_id, $message_id)
    {   
        $result = Message::find()->where(['id'=>$message_id])->orderby('created_at DESC')->all();
        $result = ArrayHelper::toArray($result, [
            'api\models\Message' => [
                'id',
                'text',
                'created_at',
                'image',
                'chat_id',
                'status',
                'user' => 'user_info',
            ],
        ]);
        return $result;
    }

    public static function updateMessage($request)
    {   
        if(!$request->post('message_id')){
            throw new \yii\web\HttpException('500','message_id cannot be blank.'); 
        }
        $msg = Message::find()->where(['id'=>$request->post('message_id')])->one();
        if ($msg) {
            if ($request->post('text')) { $msg->text = $request->post('text'); }
            if( count($_FILES)>0 AND $_FILES['image']['tmp_name'] ) {
                $file_name = uniqid().'.jpg';   
                $temp_file_location = $_FILES['image']['tmp_name']; 
                User::s3Upload('chat/', $file_name, $temp_file_location);
                $msg->image = env('AWS_S3_PLUZO').'chat/'.$file_name;
            }
            if ($msg->save()) {
                return Message::find()->where(['chat_id'=>$msg->chat_id])->orderby('created_at DESC')->all();
            } else {
                throw new \yii\web\HttpException('500', 'Error save message!');
            }
            
        } else {
            throw new \yii\web\HttpException('500', 'Message with id = '.$request->post('message_id').' not exist');
        }
    }

    public static function deleteMessage($request)
    {   
        if(!$request->post('message_id')){
            throw new \yii\web\HttpException('500','message_id cannot be blank.'); 
        }
        $msg = Message::find()->where(['id'=>$request->post('message_id')])->one();
        if ($msg) {
            \Yii::$app
            ->db
            ->createCommand()
            ->delete('message', ['id' => $msg->id])
            ->execute();
            return Message::find()->where(['chat_id'=>$msg->chat_id])->orderby('created_at DESC')->all();
        } else {
            throw new \yii\web\HttpException('500', 'Message with id = '.$request->post('message_id').' not exist');
        }
    }

    public static function deleteChat($request)
    {   
        if(!$request->post('chat_id')){
            throw new \yii\web\HttpException('500','chat_id cannot be blank.'); 
        }
        $msg = Party::find()->where(['chat_id'=>$request->post('chat_id'), 'user_id'=>\Yii::$app->user->id])->one();
        if ($msg) {
            \Yii::$app
            ->db
            ->createCommand()
            ->delete('party', ['user_id' => \Yii::$app->user->id, 'chat_id' =>$request->post('chat_id')])
            ->execute();
            return ["result"=>'Deleted'];
        } else {
            throw new \yii\web\HttpException('500', 'Message with id = '.$request->post('message_id').' not exist');
        }
    }

    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'user_id' => 'User ID',
        ];
    }

    public function fields()
    {
        return [
            'id' => 'id',
        ];
    }
}
