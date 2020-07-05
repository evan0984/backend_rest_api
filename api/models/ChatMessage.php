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
class ChatMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'party';
    }


    public static function getChatMessage()
    {   
        $result = ChatMessage::find()->select('chat_id')->where(['user_id'=>\Yii::$app->user->id])->distinct()->all();
        return $result;
    }

    public function fields()
    {
        return [
            'chat_id' => 'chat_id',
            'messages' => 'message_tbl',
        ];
    }


    public function getMessage_tbl()
    {   
        $limit = 10000;
        if (Yii::$app->request->post('limit')) {
            $limit = Yii::$app->request->post('limit');
        }
        return $this->hasMany(Message::className(), ['chat_id' => 'chat_id'])->orderBy(['created_at'=>SORT_DESC])->limit($limit);       
    }

}