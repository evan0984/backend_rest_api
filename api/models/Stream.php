<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "stream".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $channel
 * @property string|null $created_at
 */
class Stream extends \yii\db\ActiveRecord
{   
    const AGORA_KEY = 'YmVkZDBjNmU0NmM5NGY2NmIzZTJmNWRjMzI0ZjlhYzc6NDFmZWZlNDQyZWJmNDlhZjg3OGJmZGZkMWNlMjQyMmY=';
    //ZDYyODNiNjcyZTRmNDE0NWE5OTgwOWU0Yjg0ZDkxNWM6ZjViMmMzOWFlMzUxNDk4MGExY2E2NzMyNDYyNzVlMDI=
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stream';
    }

    public static function getChannelList(){

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '.Stream::AGORA_KEY
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://api.agora.io/dev/v1/channel/8fb9cd7b72694baa9a048ee3dc4633d7');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $data = curl_exec($ch);
        print_r($data);
        die();
        
        $data = json_decode($data);
        return $data->data;
    }

    public static function getUserChannelList($channel){

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '.Stream::AGORA_KEY
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://api.agora.io/dev/v1/channel/user/8fb9cd7b72694baa9a048ee3dc4633d7/'.$channel);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $data = curl_exec($ch);
        print_r($data);
        die();
        $data = json_decode($data);
        return $data->data;
    }

    public static function getUsers(){
        return Stream::find()->all();
    }

    public function fields()
    {
        return [
            'id' => 'id',
            'channel' => 'channel', 
            'category' => 'category', 
            'name' => 'name', 
            'user' => 'user', 
            'count' => 'count',  
        ];
    }

    public function getUser()
    {   
        return $this->hasOne(UserMsg::className(), ['id' => 'user_id']);        
    }

    public function getCount()
    {   
        return StreamUser::find()->where(['channel'=>$this->channel])->count();        
    }
}
