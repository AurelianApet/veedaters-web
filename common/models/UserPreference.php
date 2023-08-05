<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_preferences}}".
 */
class UserPreference extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_preferences}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['gender', 'religion', 'min_age', 'max_age', 'distance', 'sports', 'min_income', 'max_income', 'style', 'alchohol', 'smoke', 'tatoo'], 'safe'],
        ];
    }

    
    /**
     * @inheritdoc
     * @return NotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserPreferenceQuery(get_called_class());
    }
}
