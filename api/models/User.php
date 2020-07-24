<?php
namespace api\models;

use common\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\Token;
use WebSocket\Client;
use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;

class User extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const NOT_PREMIUM = 0;
    const PREMIUM = 1;
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
            ['phone', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This phone number has already been taken.', 'on'=>'update'],
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
            'token' => function(){ if (Yii::$app->controller->action->id == 'get-user-info') { return ''; } else { return $this->token; } },
            'first_name' => 'first_name',
            'last_name' => 'last_name',  
            'phone' => 'phone',
            'status' => 'status',
            'gender'=>'gender',
            'image'=>'image',
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
    
    public function savePhoto($image)
    {   
        for ($i=0; $i < count($image['name']); $i++) { 
            $putdata = fopen($image['tmp_name'][$i], "r");
            $photoname = uniqid().'.jpg';
            $filename = \Yii::getAlias('@webroot') . '/uploads/'. $photoname;
            $fp = fopen($filename, "w");
            while ($data = fread($putdata, 1024))
            fwrite($fp, $data);       
            fclose($fp);
            fclose($putdata);
            $path = \yii\helpers\Url::to(['/uploads'], true).'/'.$photoname;
            $im = new Images();
            $im->user_id = \Yii::$app->user->id; 
            $im->avator = 0;
            $im->created_at = time();
            $im->path = $path;
            $im->sort = 0;
            $im->save();
        }
        
        
        
    }

    public function getExpiredat()
    { 
        $token = Token::find()
            ->andwhere(['user_id' => $this->id])
            ->andwhere(['>','expired_at',time()])
            ->orderBy('id DESC')
            ->one();
            return $token->expired_at;
    }



    public function getToken()
    {   
        
        $token = Token::find()
            ->andwhere(['user_id' => $this->id])
            ->andwhere(['>','expired_at',time()])
            ->orderBy('id DESC')
            ->one();
            return $token->token;
    }

    public function getPassword()
    {
        return $_POST['password'];
    }

    public function getRetailer()
    {   
        return $this->hasOne(Retailer::className(), ['user_id' => 'id']);        
    }

    public function getImages()
    {   
        return $this->hasMany(Images::className(), ['user_id' => 'id']);        
    }

    public function searchUser($request)
    {
        if(!$request->post('search')){
            throw new \yii\web\HttpException('500','search cannot be blank.'); 
        }
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("Select ".User::userFields()." from `user` where `username` Like '%".$request->post('search')."%' OR `first_name` Like '%".$request->post('search')."%' OR `last_name` Like '%".$request->post('search')."%'");
        $result = $command->queryAll();
        return $result;
    }

    public function userFields(){
        return '`user`.`id`, `user`.`username`, `user`.`phone`, `user`.`image`, `user`.`gender`, `user`.`birthday`, `user`.`status`, `user`.`first_name`, `user`.`last_name`, `user`.`latitude`, `user`.`longitude`, `user`.`address`, `user`.`last_activity`, `user`.`premium`';
    }

    public function getAddress($lat, $long)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&sensor=false&key=AIzaSyDiern53s3oclBm52lQK0F-YWzLWCA_5BU&language=en";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        $curlData = curl_exec($curl);
        curl_close($curl);
        $address = json_decode($curlData, true);
        return $address['results'][0]['formatted_address'];
    }

    public function Sms($phone, $message)
    {    
        $SnSclient = new SnsClient([
        //'profile' => 'default',
        'region' => 'us-east-1',
        'version' => 'latest',
        'credentials' => [
            'key'    => 'AKIARQV6Y3IO6M6PO4FQ',
            'secret' => 'Iq4xC+NHWFINEWjj2F4o59FsPEMTW11eJnLCymv+',
        ]
        ]);

        $phone = str_replace(' ', '', $phone);
        $message = $message;
        
        //$phone = '+6282144424304';
        //$message = 'test sms';
        
        $result = $SnSclient->publish([
                'Message' => $message,
                'PhoneNumber' => $phone,
            ]);
       
    }

    public static function socket($user, $data, $action){

        $client = new Client("ws://3.134.208.235:27800?user=0");

        $messageData = [
            'user'=>(int)$user,
            'action'=>$action,
            'data'=>json_encode($data, true)
        ];

        $message = json_encode($messageData); 


        $client->send($message);

        //echo $client->receive();

        $client->close();


        /*$localsocket = 'tcp://3.134.208.235:27900';
        $messageData = [
            'action'=>$action,
            'user'=>$user,
            'data'=>$data
        ];
        $message = json_encode($messageData); 
        $instance = stream_socket_client($localsocket);
        fwrite($instance, json_encode(['user' => $user, 'message' => $message])  . "\n");*/
    }
}   

