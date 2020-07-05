<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Property;

/**
 * PropertySeach represents the model behind the search form of `common\models\Property`.
 */
class PropertySeach extends Property
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'agent', 'type', 'stage', 'exist', 'building_type', 'pw_url_id'], 'integer'],
            [['property_name', 'property_owner', 'property_source', 'management_fee', 'created_at', 'updated_at', 'description', 'barhrooms', 'bedrooms', 'street', 'city', 'state', 'country', 'area', 'latitude', 'longitude', 'zip', 'image1', 'image2', 'image3', 'image4', 'image5', 'image6', 'image7', 'image8', 'image9', 'image10', 'image11', 'image12', 'image13', 'image14', 'image15', 'image_main'], 'safe'],
            [['rental_price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Property::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'agent' => $this->agent,
            'type' => $this->type,
            'stage' => $this->stage,
            'rental_price' => $this->rental_price,
            'exist' => $this->exist,
            'building_type' => $this->building_type,
            'pw_url_id' => $this->pw_url_id,
        ]);

        $query->andFilterWhere(['like', 'property_name', $this->property_name])
            ->andFilterWhere(['like', 'property_owner', $this->property_owner])
            ->andFilterWhere(['like', 'property_source', $this->property_source])
            ->andFilterWhere(['like', 'management_fee', $this->management_fee])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'barhrooms', $this->barhrooms])
            ->andFilterWhere(['like', 'bedrooms', $this->bedrooms])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude])
            ->andFilterWhere(['like', 'zip', $this->zip])
            ->andFilterWhere(['like', 'image1', $this->image1])
            ->andFilterWhere(['like', 'image2', $this->image2])
            ->andFilterWhere(['like', 'image3', $this->image3])
            ->andFilterWhere(['like', 'image4', $this->image4])
            ->andFilterWhere(['like', 'image5', $this->image5])
            ->andFilterWhere(['like', 'image6', $this->image6])
            ->andFilterWhere(['like', 'image7', $this->image7])
            ->andFilterWhere(['like', 'image8', $this->image8])
            ->andFilterWhere(['like', 'image9', $this->image9])
            ->andFilterWhere(['like', 'image10', $this->image10])
            ->andFilterWhere(['like', 'image11', $this->image11])
            ->andFilterWhere(['like', 'image12', $this->image12])
            ->andFilterWhere(['like', 'image13', $this->image13])
            ->andFilterWhere(['like', 'image14', $this->image14])
            ->andFilterWhere(['like', 'image15', $this->image15])
            ->andFilterWhere(['like', 'image_main', $this->image_main]);

        return $dataProvider;
    }
}
