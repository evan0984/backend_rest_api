<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "images".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $avator
 * @property string $created_at
 * @property string $path
 * @property int|null $sort
 */
class Images extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'images';
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'avator' => 'Avator',
            'created_at' => 'Created At',
            'path' => 'Path',
            'sort' => 'Sort',
        ];
    }

    public function fields()
    {
        return [
            'id' => 'id',
            'path' => 'path',  
            'sort' => 'sort',
        ];
    }
}
