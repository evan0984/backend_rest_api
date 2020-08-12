<?php
namespace api\models;

use common\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\Token;
use WebSocket\Client as WEBCLIENT;
use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use yii\imagine\Image;

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
        return '{{%client}}';
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
            ['username', 'unique', 'targetClass' => '\common\models\Client', 'message' => 'This username has already been taken.', 'on'=>'create'],
            ['phone', 'unique', 'targetClass' => '\common\models\Client', 'message' => 'This phone number has already been taken.', 'on'=>'create'],
            ['phone', 'unique', 'targetClass' => '\common\models\Client', 'message' => 'This phone number has already been taken.', 'on'=>'update'],
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
        $images = Images::find()->where(['user_id'=>\Yii::$app->user->id])->orderBy(['sort' => SORT_DESC])->one();
        if($images){
            $n = $images->sort + 1;
        } else {
            $n = 0;
        }
        for ($i=0; $i < count($image['name']); $i++) { 
            $file_name = uniqid().'.jpg';   
            $temp_file_location = $image['tmp_name'][$i]; 
            User::s3Upload('user/', $file_name, $temp_file_location);
            $im = new Images();
            $im->user_id = \Yii::$app->user->id; 
            $im->avator = 0;
            $im->created_at = time();
            $im->path = env('AWS_S3_PLUZO').'user/'.$file_name;
            $im->sort = $n;
            $im->save();
            $n++;
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

    
    public function deleteAccount()
    {   
        $id = \Yii::$app->user->id;
        \Yii::$app
            ->db
            ->createCommand()
            ->delete('client', ['id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('token', ['user_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('images', ['user_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('chat', ['user_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('friend', ['user_source_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('friend', ['user_target_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('like', ['user_source_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('like', ['user_target_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('message', ['user_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('party', ['user_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('stream', ['user_id' => $id])
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->delete('stream_user', ['user_id' => $id])
            ->execute();
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

    public function checkNumber($number)
    {   
        $SnSclient = new SnsClient([
        //'profile' => 'default',
        'region' => 'us-east-1',
        'version' => 'latest',
        'credentials' => [
            'key'    => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
        ]
        ]);

        try {
            $result = $SnSclient->checkIfPhoneNumberIsOptedOut([
                'phoneNumber' => $number,
            ]);
            var_dump($result);
            die(1);
        } catch (AwsException $e) {
            print_r($e->getMessage());
            // output error message if fails
            error_log($e->getMessage());
            die(2);
        }
    }

    public function getRetailer()
    {   
        return $this->hasOne(Retailer::className(), ['user_id' => 'id']);        
    }

    public function getImages()
    {   
        return $this->hasMany(Images::className(), ['user_id' => 'id'])->
        orderBy(['sort' => SORT_ASC]);       
    }

    public static function setAvatar()
    { 
        $images = Images::find()->where(['user_id'=>\Yii::$app->user->id])->orderBy(['sort' => SORT_ASC])->one();
        $user = User::find()->where(['id'=>\Yii::$app->user->id])->one();
        if($images){
            $user->image = $images->path;
            $user->save();
        } else {
            $user->image = '';
            $user->save();
        }
    }

    public function searchUser($request)
    {
        if(!$request->post('search')){
            throw new \yii\web\HttpException('500','search cannot be blank.'); 
        }
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("Select ".User::userFields()." from `client` where `username` Like '%".$request->post('search')."%' OR `first_name` Like '%".$request->post('search')."%' OR `last_name` Like '%".$request->post('search')."%'");
        $result = $command->queryAll();
        return $result;
    }

    public function userFields(){
        return '`client`.`id`, `client`.`username`, `client`.`phone`, `client`.`image`, `client`.`gender`, `client`.`birthday`, `client`.`status`, `client`.`first_name`, `client`.`last_name`, `client`.`latitude`, `client`.`longitude`, `client`.`address`, `client`.`last_activity`, `client`.`premium`';
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
            'key'    => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
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

    public static function photoReduce($file_name){
        $dir = \Yii::getAlias('@webroot') . '/uploads/'.$file_name;
        if(filesize($dir) > 150000){
            Image::getImagine()->open($dir)->save($dir, ['jpeg_quality' => 100]);
        }
        
        //Image::getImagine()->open($dir)->save($dir, ['jpeg_quality' => 100]);

        /*Image::resize($dir, 300, 400, true)
        ->save($dir, ['quality' => 100]);*/

        /*$image = Image::getImagine()->open($dir);
        $metadata = $image->metadata();
        print_r($image);
        die();*/
        //Image::thumbnail($dir, 300, 300)
        //->save($dir, ['quality' => 50]);
    }

    public static function s3Upload($catalog, $file_name, $temp_file_location){
            $putdata = fopen($temp_file_location, "r");
            $filename = \Yii::getAlias('@webroot') . '/uploads/'. $file_name;
            $fp = fopen($filename, "w");
            while ($data = fread($putdata, 1024))
            fwrite($fp, $data);       
            fclose($fp);
            fclose($putdata);
            User::photoReduce($file_name);

            $s3Client = new S3Client([
                'region' => 'us-east-2',
                'version' => '2006-03-01',
                'credentials' => [
                        'key'    => env('AWS_KEY'),
                        'secret' => env('AWS_SECRET'),
                    ],
            ]);

            $temp_file_location = \Yii::getAlias('@webroot') . '/uploads/'.$file_name;
            $result = $s3Client->putObject(
                array(
                    'Bucket'=>'pluzo',
                    'Key'    => $catalog.$file_name,
                    'SourceFile' => $temp_file_location,
                    'ACL' => 'public-read',
                    'ContentType' => 'image',
                )
            );
            unlink($temp_file_location);
    }

    public static function socket($user, $data, $action){

        $client = new WEBCLIENT("ws://3.134.208.235:27800?user=0");

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

