<?php

namespace api\models;

use Yii;
use api\models\User;
/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property int|null $status
 * @property string|null $text
 * @property string|null $image
 * @property string|null $created_at
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_id', 'user_id'], 'required'],
            [['chat_id', 'user_id', 'status'], 'integer'],
            [['text', 'image', 'created_at'], 'safe'],
        ];
    }

    public function fields()
    {
        return [
            '_id' => 'id',
            'chat_id' => 'chat_id',   
            'user' => 'user_info', 
            'status' => 'status',
            'text' => 'text',  
            'image' => 'image',
            'createdAt' => 'created_at',
        ];
    }

    public function readMessage($request)
    {   
        $mess_id = (array)$request->post('message_id');
        if(count($mess_id) < 1){
            throw new \yii\web\HttpException('500','array lenght of message_id cannot < 1'); 
        }
        foreach ($mess_id as $key => $value) {
            $message = Message::find()->where(['id'=>$value])->one();
            if ($message) {
                $message->status = 1;
                $message->save();
            }
        }
        return ['read-messages'=>$mess_id];
        
    }
    

    public function getUser_info()
    {
        return UserMsg::find()->where(['id'=>$this->user_id])->one();
    }


}
