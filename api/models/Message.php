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
            'id' => 'id',
            'chat_id' => 'chat_id',   
            'user' => 'user_info', 
            'status' => 'status',
            'text' => 'text',  
            'image' => 'image',
            'created_at' => 'created_at',
        ];
    }

    public function getUser_info()
    {
        return UserMsg::find()->where(['id'=>$this->user_id])->one();
    }


}
