<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

  

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'chat_id',
            'user_id',
            'text:ntext',
            [
    'attribute' => 'image',
    'format' => 'html',
    'value' => function($data) { return Html::img($data->image, ['width'=>'100']); },
],
            //'created_at',

            [
                    'class' => \common\widgets\ActionColumn::class,
                    'template' => '{update}{delete}',
                    

                ],
        ],
    ]); ?>


</div>
