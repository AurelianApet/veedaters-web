<?php

namespace common\models;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "{{%user_role_map}}".
 *
 * @property integer $user_role_map_id
 * @property integer $user_role_id
 * @property integer $user_id
 * @property boolean $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class UserRoleMap extends ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_role_map}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_role_id', 'user_id', 'createdby'], 'required'],
            [['user_role_id', 'user_id', 'createdby', 'updatedby'], 'integer'],
            [['is_active'], 'boolean'],
            [['createddate', 'updateddate'], 'safe'],
            [['user_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRole::className(), 'targetAttribute' => ['user_role_id' => 'user_role_id']],
            [['createdby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['createdby' => 'id']],
            [['updatedby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updatedby' => 'id']],
            //[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_role_map_id' => 'Userrolemap ID',
            'user_role_id' => 'Roleid',
            'user_id' => 'Userid',
            'is_active' => 'Isactive',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }
    
    public function getRoleObj(){
        return $this->hasOne(UserRole::className(), ['user_role_id' => 'user_role_id']);
    }
    
    public function getUser(){
      return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
        
    static function updateUserRole($user_id = 0, $user_role_id = REL_ROLE_USER){
        if(!$user_role_id) return false;
        $userrole = self::findOne(["user_id" => $user_id, "user_role_id" => $user_role_id ]);
        if(!$userrole){
            $userrole = new UserRoleMap();
            $userrole->defaultUpdate();
            $userrole->user_role_id = $user_role_id;
            $userrole->user_id = $user_id;
            $userrole->is_active = 1;
            return $userrole->save();           
        }
        return false;
    }
    
    
}
