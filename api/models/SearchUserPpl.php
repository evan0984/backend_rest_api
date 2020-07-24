<?php
namespace api\models;

use common\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\Token;


class SearchUserPpl extends ActiveRecord
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
            'username' => 'username',   
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
            'images'=>'images',
            'friend'=>'friend',
        ];
    }

    public function getFriend()
    {  
        $request = Friend::find()->where(['user_source_id'=>\Yii::$app->user->id, 'user_target_id'=>$this->id])->one();
        if ($request) {
            return 2;
        } else {
            return 1;
        }
    }

    public function getSearch($request)
    {
        return UserMsg::find()->where(['like', 'username', $request])
        ->orwhere(['like', 'first_name', $request])
        ->orwhere(['like', 'last_name', $request])
         ->all();
        
    }
    public function getImages()
    {   
        return $this->hasMany(Images::className(), ['user_id' => 'id']);        
    }

   
}   
