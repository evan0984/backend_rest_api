<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?= $form->field($model, 'access_token') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'oauth_client') ?>

    <?php // echo $form->field($model, 'oauth_client_user_id') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'logged_at') ?>

    <?php // echo $form->field($model, 'first_name') ?>

    <?php // echo $form->field($model, 'last_name') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'forgot_sms_code') ?>

    <?php // echo $form->field($model, 'forgot_sms_code_exp') ?>

    <?php // echo $form->field($model, 'login_sms_code') ?>

    <?php // echo $form->field($model, 'login_sms_code_exp') ?>

    <?php // echo $form->field($model, 'reset_pass_code') ?>

    <?php // echo $form->field($model, 'verify_sms_code') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
