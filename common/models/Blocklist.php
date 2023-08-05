<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * This is the model class for table "{{%blocklist}}".
 *
 * @property integer $blocklist_id
 * @property integer $blocked_id
 * @property integer $blocked_by_id
 * @property string $relationship
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 *
 * @property User $createdby0
 * @property User $updatedby0
 */
class Blocklist extends ModelBase
{
    /**
     * @inheritdoc
     */
    
    const STATUS_ACTIVE = 1;
    public static $IS_ACTIVE = 1;
    public static $IN_ACTIVE = 0;
    
    const BLOCK_VALUE_YES = 1;
    
    const BLOCK_VALUE_NO = 0;
    public $apiSelected= false;
    
    public static function tableName()
    {
        return '{{%blocklist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['blocked_id', 'blocked_by_id'], 'required'],
            [['blocked_id', 'blocked_by_id', 'createdby','updatedby'], 'integer'],
            [['createddate', 'updateddate'], 'safe'],            
            [['is_blocked'], 'boolean'],
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
            'blocklist_id' => 'Blocklist ID',
            'blocked_id' => 'Blocked ID',
            'blocked_by_id' => 'Blocked By ID',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

       
    public function search($params, $asArray = false){
        return new ActiveDataProvider([
            'query' => $this->getQuery($params, $asArray),
            'pagination' => [
                'pageSize' => 50
            ]
        ]);
    }
    
    public function getBlockedUser(){
        return $this->hasOne(User::className(), ['id' => 'blocked_id']);
    }

    public function getBlockedByUser(){
        return $this->hasOne(User::className(), ['id' => 'blocked_by_id']);
    }

    public function block(){
        $this->is_blocked = REL_USER_BLOCK;
    }

    public function unblock(){
        $this->is_blocked = REL_USER_UNBLOCK;
    }    
}
