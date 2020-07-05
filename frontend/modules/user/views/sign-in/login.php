<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var yii\bootstrap4\ActiveForm $form
 * @var frontend\modules\user\models\LoginForm $model
 */

$this->title = Yii::t('frontend', 'Login');
?>


<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
<div class="site-login mt-5">
    <div style="text-align: center;">
                <?php if (Yii::$app->session->hasFlash('alert')) : ?>
                    <?php echo Alert::widget([
                        'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ]) ?>
                <?php endif; ?> 
    </div>
    <div style="margin: 0" class="row justify-content-center">       
        <div class="col-sm-4">
            <div class="card mb-2">
                <div class="card-body">
                    <h1 class="text-muted text-center"><?php echo Html::encode($this->title) ?></h1>
                    <?php echo $form->errorSummary($model) ?>
                    <?php echo $form->field($model, 'identity') ?>
                    <?php echo $form->field($model, 'password')->passwordInput() ?>

                    <div class="d-flex justify-content-between">
                        <?php echo $form->field($model, 'rememberMe')->checkbox() ?>
                        <?php echo Html::a(Yii::t('frontend', 'Forgot your password?'), ['sign-in/request-password-reset'], ['class' => ['text-sm']]) ?>
                    </div>

                    <div class="form-group">
                        <?php echo Html::submitButton(Yii::t('frontend', 'Login'), ['class' => 'btn btn-primary btn-lg btn-block', 'name' => 'login-button']) ?>
                    </div>
                    <div class="form-group">
                        <?php if (Yii::$app->getModule('user')->shouldBeActivated) : ?>
                            <?php echo Html::a(Yii::t('frontend', 'Resend my activation email'), ['sign-in/resend-email']) ?>
                        <?php endif; ?>
                        <?php /*echo Html::a(Yii::t('frontend', 'Need an account? Sign up.'), ['signup'])*/ ?>
                    </div>
                </div>
            </div>

         
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>