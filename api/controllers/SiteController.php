<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\models\LoginForm;
use api\models\User;
use common\models\Token;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Url;

class SiteController extends Controller
{   

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' =>  HttpBearerAuth::className(),
            'except' => ['login', 'signup', 'forgot-sms-send', 'forgot-sms-code', 'new-pass-code', 'verify-sms-send', 'verify-sms-code', 'login-sms-send', 'login-sms-code']
        ];

        return $behaviors;
    }

    public function actionForgotSmsSend()
    {      
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone']])->one();
        if ($user) {
            //send sms
            $digits = 4;
            $user->forgot_sms_code = 5555;
            //$user->forgot_sms_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $user->forgot_sms_code_exp = time()+3600;
            if ($user->save()) {
                //return $user->forgot_sms_code;
                return 'Code sent to '.$_POST['phone'].'!';
            }
        }
        throw new \yii\web\HttpException('500','User with this number not found!'); 
    }

    public function actionForgotSmsCode()
    {   
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        if(!$_POST['code']){
            throw new \yii\web\HttpException('500','code cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone'], 'forgot_sms_code'=>$_POST['code']])->one();
        if ($user) {
            $digits = 10;
            $user->reset_pass_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            if ($user->save()) {
                return ['pass_code'=> $user->reset_pass_code];
            }
        }
        throw new \yii\web\HttpException('500','Code for number '.$_POST['phone'].' is incorrect !'); 
    }

    public function actionNewPassCode()
    {   
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        if(!$_POST['pass_code']){
            throw new \yii\web\HttpException('500','pass_code cannot be blank.'); 
        }
        if(!$_POST['password']){
            throw new \yii\web\HttpException('500','password cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone'], 'reset_pass_code'=>$_POST['pass_code']])->one();
        if ($user) {
            $user->password_hash = Yii::$app->security->generatePasswordHash($_POST['password']);
            $user->forgot_sms_code = '';
            $user->forgot_sms_code_exp = '';
            $user->reset_pass_code = '';
            $token = new Token();
            $token->user_id = $user->id;
            $token->generateToken(time() + 3600 * 24 * 365);
            $token->save();
            if ($user->save()) {
                return $user;
            }
        }
        throw new \yii\web\HttpException('500','User with this pass_code not found!');
    }


    public function actionVerifySmsSend()
    {      
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone']])->one();
        if ($user) {
            //send sms
            $digits = 4;
            //$user->verify_sms_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $user->verify_sms_code = 5555;
            if ($user->save()) {
                //return $user->forgot_sms_code;
                return 'Verify code sent to '.$_POST['phone'].'!';
            }
        }
        throw new \yii\web\HttpException('500','User with this number not found!'); 
    }

    public function actionVerifySmsCode()
    {   
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        if(!$_POST['code']){
            throw new \yii\web\HttpException('500','code cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone'], 'verify_sms_code'=>$_POST['code']])->one();
        if ($user) {
            $user->status = User::STATUS_ACTIVE;
            $user->verify_sms_code = '';
            $token = new Token();
            $token->user_id = $user->id;
            $token->generateToken(time() + 3600 * 24 * 365);
            $token->save();
            if ($user->save()) {
                return $user;
            }
        }
        throw new \yii\web\HttpException('500','Code for number '.$_POST['phone'].' is incorrect !'); 
    }

    public function actionLoginSmsSend()
    {      
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone']])->one();
        if ($user) {
            //send sms
            $digits = 4;
            //$user->login_sms_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $user->login_sms_code = 5555;
            if ($user->save()) {
                //return $user->forgot_sms_code;
                return 'Verify code sent to '.$_POST['phone'].'!';
            }
        }
        throw new \yii\web\HttpException('500','User with this number not found!'); 
    }

    public function actionLoginSmsCode()
    {   
        if(!$_POST['phone']){
            throw new \yii\web\HttpException('500','phone cannot be blank.'); 
        }
        if(!$_POST['code']){
            throw new \yii\web\HttpException('500','code cannot be blank.'); 
        }
        $user = User::find()->where(['phone'=>$_POST['phone'], 'login_sms_code'=>$_POST['code']])->one();
        if ($user) {
            $user->login_sms_code = '';
            $token = new Token();
            $token->user_id = $user->id;
            $token->generateToken(time() + 3600 * 24 * 365);
            $token->save();
            if ($user->save()) {
                return $user;
            }
        }
        throw new \yii\web\HttpException('500','Code for number '.$_POST['phone'].' is incorrect !'); 
    }
    

    public function actionIndex()
    {
        return 'api';
    }

    public function actionLogin()
    {   
        $model = new LoginForm();
        $model->username = $_POST['username'];
        $model->password = $_POST['password'];
        if ($token = $model->auth()) {  
            return User::findOne($token->user_id); 
        } else {
            return $model;
        }
    }

    public function actionProfile()
    {  
        return User::find()->where(['id'=>\Yii::$app->user->id])->one();
    }

    

    public function actionUpdate()
    {
        $user = User::find()->where(['id'=>\Yii::$app->user->id])->one();
        if (isset($_POST['gender'])) { $user->gender = $_POST['gender']; }
        if (isset($_POST['birthday'])) { $user->birthday = $_POST['birthday']; }
        if (isset($_POST['first_name'])) { $user->first_name = $_POST['first_name']; }
        if (isset($_POST['last_name'])) { $user->last_name = $_POST['last_name']; }
        if (isset($_POST['password'])) { $user->password_hash = Yii::$app->security->generatePasswordHash($_POST['password']); }
        if( count($_FILES)>0 AND $_FILES['image']['tmp_name'] ) {
            $putdata = fopen($_FILES['image']['tmp_name'], "r");
            $photoname = uniqid().'.jpg';
            $filename = \Yii::getAlias('@webroot') . '/uploads/'. $photoname;
            $fp = fopen($filename, "w");
                                while ($data = fread($putdata, 1024))
                                fwrite($fp, $data);       
                                fclose($fp);
                                fclose($putdata);
            $model->image = \yii\helpers\Url::to(['/uploads'], true).'/'.$photoname;
        }

        $user->save();
        return $user;
    }   


    public function actionSignup()
    {   
        $model = new User();
        $model->scenario = 'create';
        $model->auth_key = 'pluzo';
        $model->access_token = 'access_token'.time();
        $model->password_hash = Yii::$app->security->generatePasswordHash($_POST['password']);
        $model->username = $_POST['username'];
        $model->email = $_POST['username'];
        $model->birthday = $_POST['birthday'];
        $model->gender = $_POST['gender'];
        $model->status = User::STATUS_NOT_ACTIVE;
        $model->first_name = $_POST['first_name'];
        $model->last_name = $_POST['last_name'];
        $model->phone = $_POST['phone'];

        if( count($_FILES)>0 AND $_FILES['image']['tmp_name'] ) {
            $putdata = fopen($_FILES['image']['tmp_name'], "r");
            $photoname = uniqid().'.jpg';
            $filename = \Yii::getAlias('@webroot') . '/uploads/'. $photoname;
            $fp = fopen($filename, "w");
                                while ($data = fread($putdata, 1024))
                                fwrite($fp, $data);       
                                fclose($fp);
                                fclose($putdata);
            $model->image = \yii\helpers\Url::to(['/uploads'], true).'/'.$photoname;
        }
        
        if ($model->save()) {
            
            $token = new Token();
            $token->user_id = $model->id;
            $token->generateToken(time() + 3600 * 24 * 365);
            $token->save();
            return User::findOne($model->id);            

        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        };
        return $model;
    }

}
