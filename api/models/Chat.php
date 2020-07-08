<?php

namespace api\models;

use Yii;
use api\models\Chat;
use api\models\Party;
use api\models\Message;

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

    //return all chats for auth user
    public static function getChat()
    {   
        $result = Party::find()->select('chat_id')->where(['user_id'=>\Yii::$app->user->id])->distinct()->all();
        return $result;
    }

    //create new message
    public static function addMessage($request)
    {   
<<<<<<< HEAD
        if(!$request->post('send_to')){
            throw new \yii\web\HttpException('500','send_to cannot be blank.'); 
        }
=======
>>>>>>> 0da959ceb0463e4e3a481c40691b5f0a2a071ac1
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
            $putdata = fopen($_FILES['image']['tmp_name'], "r");
            $photoname = uniqid().'.jpg';
            $filename = \Yii::getAlias('@webroot') . '/chat/'. $photoname;
            $fp = fopen($filename, "w");
                                while ($data = fread($putdata, 1024))
                                fwrite($fp, $data);       
                                fclose($fp);
                                fclose($putdata);
            $message->image = \yii\helpers\Url::to(['/chat'], true).'/'.$photoname;
        }

        $message->save();


        return Chat::getMessagers($message->chat_id);
    }

    public static function getMessagers($chat_id)
    { 
        return Message::find()->where(['chat_id'=>$chat_id])->orderby('created_at DESC')->all();
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
            $putdata = fopen($_FILES['image']['tmp_name'], "r");
            $photoname = uniqid().'.jpg';
            $filename = \Yii::getAlias('@webroot') . '/chat/'. $photoname;
            $fp = fopen($filename, "w");
                                while ($data = fread($putdata, 1024))
                                fwrite($fp, $data);       
                                fclose($fp);
                                fclose($putdata);
            $msg->image = \yii\helpers\Url::to(['/chat'], true).'/'.$photoname;
            }
            if ($msg->save()) {
                return Message::find()->where(['chat_id'=>$msg->chat_id])->orderby('created_at DESC')->all();
            } else {
                throw new ServerErrorHttpException('Error save message!');
            }
            
        } else {
            throw new ServerErrorHttpException('Message with id = '.$request->post('message_id').' not exist');
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
            throw new ServerErrorHttpException('Message with id = '.$request->post('message_id').' not exist');
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
            throw new ServerErrorHttpException('Message with id = '.$request->post('message_id').' not exist');
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
