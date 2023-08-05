<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use common\helpers\AssestsManager;

/**
 * This is the model class for table "subscription".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $subscription_type
 * @property string $expires_on
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscription}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'months'], 'integer'],
            [['amount', 'expires_on', 'createddate', 'updateddate'], 'safe'],
            [['charge_id', 'transaction_id', 'plan'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    // public function attributeLabels()
    // {
    //     return [
    //         'id' => 'ID',
    //         'user_id' => 'User ID',
    //         'subscription_type' => 'Subscription Type',
    //         'expires_on' => 'Expires On',
    //         'created_at' => 'Created At',
    //         'updated_at' => 'Updated At',
    //         'created_by' => 'Created By',
    //         'updated_by' => 'Updated By',
    //     ];
    // }

    /**
     * @inheritdoc
     * @return SubscriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubscriptionQuery(get_called_class());
    }
    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }
}
