<?php

namespace common\models;
use common\models\MessageRecipient;
use \common\models\User;
use \common\models\Photos;
use \common\models\PhotosMap;

use Yii;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property integer $message_id
 * @property string $message_subject
 * @property integer $message_creator_id
 * @property string $message_body
 * @property integer $message_parent_id
 * @property integer $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class Message extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    
    public static $IS_READ = 1;
    public static $IS_UNREAD = 0;
    
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_creator_id', 'message_parent_id'], 'required'],
            [['message_creator_id', 'message_parent_id','clear_for_recipient','clear_for_sender', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['message_body'], 'string'],
            [['createddate', 'updateddate'], 'safe'],
            [['message_subject'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'message_subject' => 'Message Subject',
            'message_creator_id' => 'Message Creator ID',
            'message_body' => 'Message Body',
            'message_parent_id' => 'Message Parent ID',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return MessageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessageQuery(get_called_class());
    }
    
    
    public function getConversation() { 
        return  $this->hasOne(MessageRecipient::className(), ['message_id' => 'message_id']);
    }

    // public function getId() { 
    //     return  $this->message_id;
    // }    
    
    public function getPhotos(){
        return $this->hasOne(Photos::className(),['createdby'=>'message_creator_id']);
    }
    public function getUser() { 
        return  $this->hasOne(User::className(), ['id' => 'message_creator_id']);
    }
    public function getReciever() { 
        return  $this->hasOne(User::className(), ['id' => 'message_recipient_id']);
    }
    public function getResmsg(){
        return  $this->hasOne(Message::className(), ['message_recipient_id' => 'message_creator_id']);
    }
    
    public function getRecipient() { 
        return  $this->hasOne(MessageRecipient::className(), ['message_id' => 'message_id']);
    }
    public function getresphotos(){
        return $this->hasOne(Photos::className(),['createdby'=>'message_recipient_id']);
    }
    
    public function getInfo() { 
        return  $this->hasOne(MessageRecipient::className(), ['message_id' => 'message_id'])->alias('mR');
    }
    
    public function getReply() { 
        return  $this->hasOne(MessageRecipient::className(), ['message_id' => 'message_id']);
    }

    public function getMessagePhoto(){
      return $this->hasOne(Photos::className(), [ 'photos_id' => 'photos_id'])->viaTable(PhotosMap::tableName(), ['item_id' => 'message_id'], function($query) {
          $query->alias('mpicp');
          $query->andOnCondition(['mpicp.relationship' => 'REL_MESSAGE_PICTURE']);
        })->alias('mpic');
    }
}
