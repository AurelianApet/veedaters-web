<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Message;

/**
 * MessageSearch represents the model behind the search form about `backend\models\Message`.
 */
class MessageSearch extends Message
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'message_creator_id', 'message_parent_id', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['message_subject', 'message_body', 'createddate', 'updateddate'], 'safe'],
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
        $query = Message::find();

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
            'message_id' => $this->message_id,
            'message_creator_id' => $this->message_creator_id,
            'message_parent_id' => $this->message_parent_id,
            'is_active' => $this->is_active,
            'createdby' => $this->createdby,
            'createddate' => $this->createddate,
            'updatedby' => $this->updatedby,
            'updateddate' => $this->updateddate,
        ]);

        $query->andFilterWhere(['like', 'message_subject', $this->message_subject])
            ->andFilterWhere(['like', 'message_body', $this->message_body]);

        return $dataProvider;
    }
}
