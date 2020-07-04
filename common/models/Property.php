<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property".
 *
 * @property int $id
 * @property string $property_name
 * @property int|null $agent
 * @property int $type
 * @property int $stage
 * @property string|null $property_owner
 * @property string|null $property_source
 * @property float|null $rental_price
 * @property string|null $management_fee
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $description
 * @property string|null $barhrooms
 * @property string|null $bedrooms
 * @property int $exist
 * @property string|null $street
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $area
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $zip
 * @property int $building_type
 * @property int $pw_url_id
 * @property string|null $image1
 * @property string|null $image2
 * @property string|null $image3
 * @property string|null $image4
 * @property string|null $image5
 * @property string|null $image6
 * @property string|null $image7
 * @property string|null $image8
 * @property string|null $image9
 * @property string|null $image10
 * @property string|null $image11
 * @property string|null $image12
 * @property string|null $image13
 * @property string|null $image14
 * @property string|null $image15
 * @property string|null $image_main
 */
class Property extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['property_name', 'created_at', 'updated_at', 'building_type', 'pw_url_id'], 'required'],
            [['agent', 'type', 'stage', 'exist', 'building_type', 'pw_url_id'], 'integer'],
            [['rental_price'], 'number'],
            [['description'], 'string'],
            [['property_name', 'street', 'state', 'country', 'area', 'latitude', 'longitude', 'image1', 'image2', 'image3', 'image4', 'image5', 'image6', 'image7', 'image8', 'image9', 'image10', 'image11', 'image12', 'image13', 'image14', 'image15', 'image_main'], 'string', 'max' => 256],
            [['property_owner', 'property_source', 'management_fee', 'created_at', 'updated_at', 'barhrooms', 'bedrooms', 'city', 'zip'], 'string', 'max' => 100],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created_at = time();
            $this->updated_at = time();
            $this->type = 0;
            $this->stage = 0;
            $this->exist = 1;
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
            'property_name' => 'Property Name',
            'agent' => 'Agent',
            'type' => 'Type',
            'stage' => 'Stage',
            'property_owner' => 'Property Owner',
            'property_source' => 'Property Source',
            'rental_price' => 'Rental Price',
            'management_fee' => 'Management Fee',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'description' => 'Description',
            'barhrooms' => 'Barhrooms',
            'bedrooms' => 'Bedrooms',
            'exist' => 'Exist',
            'street' => 'Street',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'area' => 'Area',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'zip' => 'Zip',
            'building_type' => 'Building Type',
            'pw_url_id' => 'Pw Url ID',
            'image1' => 'Image1',
            'image2' => 'Image2',
            'image3' => 'Image3',
            'image4' => 'Image4',
            'image5' => 'Image5',
            'image6' => 'Image6',
            'image7' => 'Image7',
            'image8' => 'Image8',
            'image9' => 'Image9',
            'image10' => 'Image10',
            'image11' => 'Image11',
            'image12' => 'Image12',
            'image13' => 'Image13',
            'image14' => 'Image14',
            'image15' => 'Image15',
            'image_main' => 'Image Main',
        ];
    }
}
