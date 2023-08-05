<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%notification}}".
 *
 * @property integer $notification_id
 * @property integer $user_id
 * @property string $device_type
 * @property string $device_token
 * @property integer $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class Notification extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'device_type', 'device_token'], 'required'],
            [['user_id', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['device_token'], 'string'],
            [['createddate', 'updateddate'], 'safe'],
            [['device_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'user_id' => 'User ID',
            'device_type' => 'Device Type',
            'device_token' => 'Device Token',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return NotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationQuery(get_called_class());
    }
    public function getNotification() { 
        return  $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getRequest() { 
        return  $this->hasOne(User::className(), ['id' => 'createdby']);
    }
}
