<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends Client
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at', 'gender'], 'integer'],
            [['username', 'address', 'auth_key', 'access_token', 'password_hash', 'oauth_client', 'oauth_client_user_id', 'email', 'first_name', 'last_name', 'phone', 'image', 'forgot_sms_code', 'forgot_sms_code_exp', 'login_sms_code', 'login_sms_code_exp', 'reset_pass_code', 'verify_sms_code', 'birthday'], 'safe'],
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
        $query = Client::find();


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]],
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'logged_at' => $this->logged_at,
            'gender' => $this->gender,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'oauth_client', $this->oauth_client])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'oauth_client_user_id', $this->oauth_client_user_id])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'forgot_sms_code', $this->forgot_sms_code])
            ->andFilterWhere(['like', 'forgot_sms_code_exp', $this->forgot_sms_code_exp])
            ->andFilterWhere(['like', 'login_sms_code', $this->login_sms_code])
            ->andFilterWhere(['like', 'login_sms_code_exp', $this->login_sms_code_exp])
            ->andFilterWhere(['like', 'reset_pass_code', $this->reset_pass_code])
            ->andFilterWhere(['like', 'verify_sms_code', $this->verify_sms_code])
            ->andFilterWhere(['like', 'birthday', $this->birthday]);

        return $dataProvider;
    }
}
