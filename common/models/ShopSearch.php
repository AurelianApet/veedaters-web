<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Shop;

class ShopSearch extends Shop
{
    /**
     * @inheritdoc
     */
    public $shop_account_number=1;
    public $shop_zip=1;
    public $shop_routing_number=1;
    public $cancellation_policy=1;
    public function rules()
    {
        return [
            [['shop_id', 'shop_account_number', 'shop_zip', 'isactive', 'createdby', 'updatedby', 'shop_routing_number'], 'integer'],
            [['shop_title', 'shop_description', 'shop_delivery', 'cancellation_policy', 'shop_address', 'shop_email', 'shop_phone', 'shop_deposit_information', 'shop_last_active', 'createddate', 'updateddate', 'shop_city', 'shop_delivery_location', 'shop_delivery_hours', 'shop_delivery_days', 'shop_lat', 'shop_lng'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Shop::find();

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
            'shop_id' => $this->shop_id,
            'shop_account_number' => $this->shop_account_number,
            'shop_zip' => $this->shop_zip,
            'shop_last_active' => $this->shop_last_active,
            'isactive' => $this->isactive,
            'createdby' => $this->createdby,
            'createddate' => $this->createddate,
            'updatedby' => $this->updatedby,
            'updateddate' => $this->updateddate,
            'shop_routing_number' => $this->shop_routing_number,
        ]);

        $query->andFilterWhere(['like', 'shop_title', $this->shop_title])
            ->andFilterWhere(['like', 'shop_description', $this->shop_description])
            ->andFilterWhere(['like', 'shop_delivery', $this->shop_delivery])
            ->andFilterWhere(['like', 'cancellation_policy', $this->cancellation_policy])
            ->andFilterWhere(['like', 'shop_address', $this->shop_address])
            ->andFilterWhere(['like', 'shop_email', $this->shop_email])
            ->andFilterWhere(['like', 'shop_phone', $this->shop_phone])
            ->andFilterWhere(['like', 'shop_deposit_information', $this->shop_deposit_information])
            ->andFilterWhere(['like', 'shop_city', $this->shop_city])
            ->andFilterWhere(['like', 'shop_delivery_location', $this->shop_delivery_location])
            ->andFilterWhere(['like', 'shop_delivery_hours', $this->shop_delivery_hours])
            ->andFilterWhere(['like', 'shop_delivery_days', $this->shop_delivery_days])
            ->andFilterWhere(['like', 'shop_lat', $this->shop_lat])
            ->andFilterWhere(['like', 'shop_lng', $this->shop_lng]);

        return $dataProvider;
    }
}
