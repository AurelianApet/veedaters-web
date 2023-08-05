<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%video_map}}".
 *
 * @property integer $id
 * @property integer $video_id
 * @property integer $user_id
 * @property integer $is_primary
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $user
 * @property Videos $video
 */
class VideoMap extends ModelBase {

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return '{{%video_map}}';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      [['video_id', 'user_id', 'is_primary', 'createdby', 'updatedby'], 'integer'],
      [['createddate', 'updateddate'], 'safe'],
      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
      [['video_id'], 'exist', 'skipOnError' => true, 'targetClass' => Videos::className(), 'targetAttribute' => ['video_id' => 'id']],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'id' => 'ID',
      'video_id' => 'Video ID',
      'user_id' => 'User ID',
      'is_primary' => 'Is Primary',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getUser() {
    return $this->hasOne(User::className(), ['id' => 'user_id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getVideo() {
    return $this->hasOne(Videos::className(), ['id' => 'video_id']);
  }

}
