<?php

namespace api\models;

use Yii;
use api\models\Like;
use api\components\DistanceHelper;
/**
 * This is the model class for table "like".
 *
 * @property int $id
 * @property int $user_source_id
 * @property int $user_target_id
 * @property int $like
 * @property string|null $created_at
 */
class Like extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'like';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_source_id', 'user_target_id', 'like'], 'required'],
            [['user_source_id', 'user_target_id', 'like'], 'integer'],
            
        ];
    }

    public function sendLikeAll($request)
    {   
        $result = [];
        $is_like = (int)$request->post('is_like');
        $user_target_id = (array)$request->post('user_target_id');
        if (count($user_target_id) < 1) {
            throw new \yii\web\HttpException('500','user_target_id cannot be blank.'); 
        }
        if(!isset($is_like)){
            throw new \yii\web\HttpException('500','is_like cannot be blank.'); 
        }
        foreach ($user_target_id as $key => $value) {
            $like = Like::sendLike((int)$value, $is_like);
            array_push($result, $like);
        }

        return
        [
            'result' => $result,
            'like_info' => Like::getLikeInfo(),
        ];
    }

    public function sendLike($user_target_id, $is_like)
    {           
        if(!$user_target_id){
            throw new \yii\web\HttpException('500','user_target_id cannot be blank.'); 
        }
        if(!isset($is_like)){
            throw new \yii\web\HttpException('500','is_like cannot be blank.'); 
        }
        if($user_target_id == \Yii::$app->user->id){
             throw new \yii\web\HttpException('500','user_target_id can not be your ID'); 
        }

        $like = Like::find()->where(['user_source_id'=>\Yii::$app->user->id, 'user_target_id'=>$user_target_id])->one();
        if($like){
            $like->like = $is_like;
            $like->created_at = time();
            $like->save();
        } else {
            $like = new Like();
            $like->user_source_id = \Yii::$app->user->id;
            $like->user_target_id = $user_target_id;
            $like->created_at = time();
            $like->like = $is_like;
            $like->save();
        }
        return $like;
    }

    public function getLikeInfo()
    {
        $dis = 0;
        $like = 0;
        $super = 0;
        $likes = Like::find()->where(['user_source_id'=>\Yii::$app->user->id])->all();
        foreach ($likes as $key => $value) {
            if ($value['like'] == 0) {
                $dis++;
            }
            if ($value['like'] == 1) {
                $like++;
            }
            if ($value['like'] == 2) {
                $super++;
            }
        }
        return [
            'dislike' => $dis,
            'like' => $like,
            'superlike' => $super
        ];
    }

    public function getMatch()
    {
        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT l2.user_source_id, ".User::userFields()." FROM `like` l1 
            INNER JOIN `like` l2 ON l1.user_source_id = l2.user_target_id AND l2.user_source_id = l1.user_target_id 
            LEFT JOIN `client` ON `client`.`id` = l2.user_source_id
            WHERE l1.user_source_id = ".\Yii::$app->user->id." AND (l1.like = 1 OR l2.like = 1 OR l2.like = 2 OR l2.like = 2)  AND (l1.like <> 0 AND l2.like <> 0)");
        $result = $command->queryAll();
        return 
        [            
            'result' => $result,
            'like_info' => Like::getLikeInfo(),
        ]; 

    }

    public function swipeSearch($request)
    {   
        if(!$request->post('latitude')){
            throw new \yii\web\HttpException('500','latitude cannot be blank.'); 
        }
        if(!$request->post('longitude')){
            throw new \yii\web\HttpException('500','longitude cannot be blank.'); 
        }
        if(!$request->post('gender')){
            throw new \yii\web\HttpException('500','gender cannot be blank.'); 
        }
        if(!$request->post('age_from')){
            throw new \yii\web\HttpException('500','age_from cannot be blank.'); 
        }
        if(!$request->post('age_to')){
            throw new \yii\web\HttpException('500','age_to cannot be blank.'); 
        }
        $lat = $request->post('latitude');
        $lon = $request->post('longitude');
        $gender = $request->post('gender');
        $age_from = $request->post('age_from');
        $age_to = $request->post('age_to');

        $id = \Yii::$app->user->id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT *, YEAR(CURRENT_TIMESTAMP)-FROM_UNIXTIME(`birthday`, '%Y') AS `age`, ".DistanceHelper::distance($lat, $lon)." AS `distance` FROM user WHERE `gender`=".$gender." HAVING `distance` < 100 AND `age`>=".$age_from." AND `age`<=".$age_to);      

        $result = $command->queryAll();

        $ar1 = [];
        foreach ($result as $key => $value) {
            array_push($ar1, $value['id']);
        }

        $distance = [];
        foreach ($result as $key => $value) {
            array_push($distance, [$value['id']=>$value['distance']]);
        }

        $like = Like::find()->select('user_target_id')->where(['user_source_id'=>\Yii::$app->user->id])->asArray()->all();
        $ar2 = [];
        foreach ($like as $key => $value) {
            array_push($ar2, $value['user_target_id']);
        }
        $swipe = User::find()
        ->where(['<>','id', \Yii::$app->user->id])
        ->andWhere(['in', 'id', $ar1])
        ->andWhere(['not in', 'id', $ar2])
        ->orderBy([
        'id' => SORT_DESC     
        ])
        ->all();  
        return 
        [    
            'swipe' => $swipe,
            'distance'=>$distance,
            'like_info' => Like::getLikeInfo(),
        ];



        //return $result;
    }

    public function swipe()
    {   
        $like = Like::find()->select('user_target_id')->where(['user_source_id'=>\Yii::$app->user->id])->asArray()->all();
        $ar = [];
        foreach ($like as $key => $value) {
            array_push($ar, $value['user_target_id']);
        }
        $swipe = User::find()
        ->where(['<>','id', \Yii::$app->user->id])
        ->andWhere(['not in', 'id', $ar])
        ->orderBy([
        'id' => SORT_DESC     
        ])
        ->all();  
        return 
        [    
            'swipe' => $swipe,
            'like_info' => Like::getLikeInfo(),
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_source_id' => 'User Source ID',
            'user_target_id' => 'User Target ID',
            'like' => 'Like',
            'created_at' => 'Created At',
        ];
    }
}
