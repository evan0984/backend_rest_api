<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lead".
 *
 * @property int $id
 * @property string|null $first_name
 * @property string $last_name
 * @property string|null $company
 * @property string|null $phone
 * @property string|null $mobile
 * @property string $email
 * @property int $lead_status
 * @property string|null $lead_owner
 * @property string|null $lead_source
 * @property int|null $rental_address
 * @property string $created_at
 * @property string $updated_at
 * @property int $is_lead_opportunity
 */
class Lead extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['last_name', 'email', 'created_at', 'updated_at'], 'required'],
            [['lead_status', 'rental_address', 'is_lead_opportunity'], 'integer'],
            [['first_name', 'last_name', 'company', 'phone', 'mobile', 'email', 'lead_owner', 'lead_source', 'created_at', 'updated_at'], 'string', 'max' => 100],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created_at = time();
            $this->updated_at = time();
            $this->lead_status = 0;
            $this->is_lead_opportunity = 0;
            return true;
        } else {
            return false;
        }
    } 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company' => 'Company',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'lead_status' => 'Lead Status',
            'lead_owner' => 'Lead Owner',
            'lead_source' => 'Lead Source',
            'rental_address' => 'Rental Address',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_lead_opportunity' => 'Is Lead Opportunity',
        ];
    }
}
