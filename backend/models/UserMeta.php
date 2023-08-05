<?php

namespace backend\models;
use common\models\User;
use Yii;

/**
 * This is the model class for table "{{%user_meta}}".
 *
 * @property integer $user_meta_id
 * @property string $meta_key
 * @property integer $user_id
 * @property string $meta_value
 * @property integer $is_active
 * @property integer $createdby
 * @property integer $updatedby
 * @property string $updateddate
 */
class UserMeta extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meta_key', 'user_id', 'meta_value'], 'required'],
            [['user_id', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['meta_value'], 'string'],
            [['createddate', 'updateddate'], 'safe'],
            [['meta_key'], 'string', 'max' => 50],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['updatedby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updatedby' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_meta_id' => 'User Meta ID',
            'meta_key' => 'Meta Key',
            'user_id' => 'User ID',
            'meta_value' => 'Meta Value',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return UserMetaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserMetaQuery(get_called_class());
    }
}
