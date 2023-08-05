<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%photos_map}}".
 *
 * @property integer $photo_map_id
 * @property integer $item_id
 * @property string $relationship
 * @property boolean $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 *
 * @property User $createdby0
 * @property User $updatedby0
 */
class PhotosMap extends ModelBase
{
    
    const R_SESSION_IMAGE = 'session_image';
    const R_USER_IMAGE = 'user_image';
    const R_USER_GALLERY_IMAGE = 'user_image_gallery';
    const R_HOST_PLACE_IMAGE = 'place_image';
    const R_COACH_PROFILE_IMAGE = 'coach_profile_image';
    const R_COACH_OTHER_IMAGE = 'coach_other_image';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%photos_map}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['photos_id', 'item_id', 'createdby', 'relationship', 'createddate', 'updatedby', 'updateddate'], 'required'],
            [[ 'item_id', 'createdby', 'updatedby'], 'integer'],
            [['is_active'], 'boolean'],
            [['createddate', 'updateddate'], 'safe'],
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
            'photo_map_id' => 'Photo Map ID',
            'photos_id' => 'Photos ID',
            'item_id' => 'Item ID',
            'relationship' => 'Relationship',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }
    

    /**
     * Delete associated photo record if photo map is deleted
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            
            if($this->photos) $this->photos->delete();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedby0()
    {
        return $this->hasOne(User::className(), ['user_id' => 'createdby']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedby0()
    {
        return $this->hasOne(User::className(), ['user_id' => 'updatedby']);
    }
    
    public function getPhotos(){
        return $this->hasOne(Photos::className(), ['photos_id' => 'photos_id'])->alias('photo');
    }
    
}
