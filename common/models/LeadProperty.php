<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lead_property".
 *
 * @property int $id
 * @property int|null $lead
 * @property int|null $property
 * @property int $agent
 * @property int $status
 */
class LeadProperty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lead', 'property', 'agent', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lead' => 'Lead',
            'property' => 'Property',
            'agent' => 'Agent',
            'status' => 'Status',
        ];
    }
}
