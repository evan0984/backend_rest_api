<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

   

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'username',
            //'auth_key',
            //'access_token',
            //'password_hash',
            //'oauth_client',
            //'oauth_client_user_id',
            //'email:email',
            //'status',
            'created_at:date',
            //'updated_at',
            'birthday',
            'first_name',
            'last_name',
            'phone',
            'gender',

            [
    'attribute' => 'image',
    'format' => 'html',
    'value' => function($data) { return Html::img($data->image, ['width'=>'100']); },
],
            //'forgot_sms_code',
            //'forgot_sms_code_exp',
            //'login_sms_code',
            //'login_sms_code_exp',
            //'reset_pass_code',
            //'verify_sms_code',
            //'birthday',

           

                [
                    'class' => \common\widgets\ActionColumn::class,
                    'template' => '{update}{delete}',
                    

                ],
        ],
    ]); ?>


</div>
