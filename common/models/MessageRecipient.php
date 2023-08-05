<?php

namespace common\models;
use \common\models\User;

use Yii;

/**
 * This is the model class for table "{{%message_recipient}}".
 *
 * @property integer $message_recipient_id
 * @property integer $message_id
 * @property integer $sender_id
 * @property integer $is_read
 * @property integer $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class MessageRecipient extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_recipient}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'is_read'], 'required'],
            [['message_id', 'recipient_id', 'is_read','first_message_key', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['createddate', 'updateddate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_recipient_id' => 'Message Recipient ID',
            'message_id' => 'Message ID',
            'sender_id' => 'Sender ID',
            'is_read' => 'Is Read',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return MessageRecipientQuery the active query used by this AR class.
     */
    // public static function find()
    // {
    //     return new MessageRecipientQuery(get_called_class());
    // }

    public function getReciever() { 
        return  $this->hasOne(User::className(), ['id' => 'recipient_id'])->addSelect(['id','username','name']);
    }
    public function getSender() { 
        return  $this->hasOne(User::className(), ['id' => 'createdby'])->alias('u2')->addSelect(['u2.id','u2.username','u2.name']);
    }
    
}
