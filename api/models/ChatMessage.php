<?php

namespace api\models;

use Yii;
use api\models\Chat;
use api\models\Party;
use api\models\Message;
use yii\helpers\ArrayHelper;
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


    public static function getCurrentChat($request)
    {  
        if(!$request->post('chat_id')){
            throw new \yii\web\HttpException('500','chat_id cannot be blank.'); 
        }
        $chats = Party::find()->where(['chat_id'=>$request->post('chat_id'), 'user_id'=>\Yii::$app->user->id])->one();
        if(!$chats){
            throw new \yii\web\HttpException('500','You not have this chat_id'); 
        }
        $result = ChatMessage::find()->select('chat_id')->where(['chat_id'=>$request->post('chat_id')])->one();
        return $result;
    }

    public static function getChatMessage($id)
    {   
        $result = ChatMessage::find()->select('chat_id')->where(['user_id'=>$id])->distinct()->all();
        return $result;
    }

    public function fields()
    {
        return [
            'chat_id' => 'chat_id',
            'messages' => 'message_tbl',
            'partner_info' => 'partner_info',
        ];
    }

    public function getPartner_info()
    { 
        $party = Party::find()->where(['chat_id'=>$this->chat_id])->andWhere(['<>','user_id', \Yii::$app->user->id])->one();
        return UserMsg::find()->where(['id'=>$party->user_id])->one();
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