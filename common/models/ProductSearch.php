<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ShopSearch represents the model behind the search form about `backend\models\Shop`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'shop_id', 'product_quantity', 'isactive', 'createdby', 'updatedby', 'product_price'], 'integer'],
            [['	product_title', 'product_description'], 'safe'],
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
        $query = Product::find();

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
            'product_id' => $this->product_id,
            'product_title' => $this->product_title,
            'isactive' => $this->isactive,
            'createdby' => $this->createdby,
            'createddate' => $this->createddate,
            'updatedby' => $this->updatedby,
            'updateddate' => $this->updateddate,
        ]);

        $query->andFilterWhere(['like', 'product_title', $this->product_title]);

        return $dataProvider;
    }
}
