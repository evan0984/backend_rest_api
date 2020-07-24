<?php
namespace api\models;

use common\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\Token;
use api\models\SearchUserPpl;

class SearchUser extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    public $password;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'on'=>'create'],
            ['phone', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This phone number has already been taken.', 'on'=>'create'],
            [['username', 'email', 'phone'], 'required', 'on'=>'create'],
            ['status', 'integer'],
            [['password','gender', 'first_name', 'last_name', 'phone', 'username', 'email'], 'safe'],
            
        ];
    }
           
    
    public function fields()
    {
        return [
            'id' => 'id',
            'name' => 'username',   
            //'token' => 'token', 
            'first_name' => 'first_name',
            'last_name' => 'last_name',  
            'phone' => 'phone',
            'status' => 'status',
            'gender'=>'gender',
            'avatar'=>'image',
            'birthday'=>'birthday',
            'latitude'=>'latitude',
            'longitude'=>'longitude',
            'address'=>'address',
            'last_activity'=>'last_activity',
            'premium'=>'premium',
            'bio'=>'bio',
            'images'=>'images'
        ];
    }

    public function getSearch($request)
    {   
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
        return SearchUserPpl::find()->where(['like', 'username', $request])
        ->orwhere(['like', 'first_name', $request])
        ->orwhere(['like', 'last_name', $request])
        ->andWhere(['<>','id', \Yii::$app->user->id])
        ->andWhere(['not in', 'id', $ar])
         ->all();
        
    }
    public function getImages()
    {   
        return $this->hasMany(Images::className(), ['user_id' => 'id']);        
    }

   
}   