<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use common\models\VideoMap;
use \common\helpers\AssestsManager;
/**
 * This is the model class for table "{{%videos}}".
 *
 * @property integer $id
 * @property string $video_url
 * @property string $video_title
 * @property string $video_description
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property VideoMap[] $videoMaps
 */
class Videos extends ModelBase {

  public $tmp_videos;
  public $user_id;

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return '{{%videos}}';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      [['createddate', 'updateddate'], 'safe'],
      [['createdby', 'updatedby'], 'integer'],
      [['video_url', 'video_title', 'video_description'], 'string', 'max' => 255],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'id' => 'ID',
      //'user_id' =>'User ID',
      'video_url' => 'Video Url',
      'video_title' => 'Video Title',
      'video_description' => 'Video Description',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getVideoMaps() {
    return $this->hasMany(VideoMap::className(), ['video_id' => 'id']);
  }

  public function saveVideo() {
    $transaction = Yii::$app->db->beginTransaction();
    if (count($this->tmp_videos) > 0) {
      $this->video_title = $this->tmp_videos->name;
      if($this->save()){ 
        $this->video_url = $this->moveFiles();
        $this->save();
        $transaction->commit();
        return true;
      }
    }
    return false;
  }

  private function moveFiles() { 
      
    // create user folder if not exsist  
    if(!file_exists(\Yii::getAlias('@uploads') . '/user/')) {
        $old_umask = umask(0);                    
        mkdir(\Yii::getAlias('@uploads') . '/user/', 0777, true);
        chmod(\Yii::getAlias('@uploads') . '/user/', 0777);
        umask($old_umask);
    }
    
    // create user id folder if not exsist
    if(!file_exists(\Yii::getAlias('@uploads') . '/user/'.\Yii::$app->user->getId())) {
        mkdir(\Yii::getAlias('@uploads') . '/user/'.\Yii::$app->user->getId(), 0777, true);
        chmod(\Yii::getAlias('@uploads') . '/user/'.\Yii::$app->user->getId(), 0777);
    }

    $ext = pathinfo($this->tmp_videos, PATHINFO_EXTENSION);
    $vidPath = "user/". \Yii::$app->user->getId()."/" . "user_video_{$this->id}" . ".{$ext}";
    $filePath = \yii\helpers\Url::to('@uploads/' . $vidPath);
    if ($this->tmp_videos->saveAs($filePath, true)) {      
      return $vidPath;
      $this->processVideo();
    }
    
    
  }
  
  public function saveThumb() { 
      
      // delete photo thumbs
      $photo_ids = PhotosMap::find()->addSelect(['item_id','relationship','photos_id'])->andWhere(['item_id' => Yii::$app->user->getId() , 'relationship' => REL_VIDEO_THUMB])->all();      
      PhotosMap::deleteAll(['item_id' => Yii::$app->user->getId() , 'relationship' => REL_VIDEO_THUMB]);
      
        $imageFile = [];
        $imageFile = \yii\web\UploadedFile::getInstancesByName('Videos[video_thumb]');
        if ($imageFile) { 
          $formSingle = new \api\modules\v1\models\ImageUploadForm();
          $formSingle->temp_images = $imageFile;
          if ($formSingle->upload('_video_thumb')) {
            if (!file_exists(\Yii::getAlias('@uploads') . '/user/')) {
              $old_umask = umask(0);
              mkdir(\Yii::getAlias('@uploads') . '/user/', 0777, true);
              chmod(\Yii::getAlias('@uploads') . '/user/', 0777);
              umask($old_umask);
            }
            if (!file_exists(\Yii::getAlias('@uploads') . '/user/' . Yii::$app->user->getId())) {
              $old_umask = umask(0);
              mkdir(\Yii::getAlias('@uploads') . '/user/' . Yii::$app->user->getId(), 0777, true);
              umask($old_umask);
            }

            $photo_map = '';
            $photo_map = PhotosMap::find()->andWhere(['item_id' => Yii::$app->user->getId(), 'relationship' => REL_VIDEO_THUMB])->all();
            $fileDateArray = array_column($formSingle->response, 'savedName');
            $photoUploader = new \common\helpers\PhotoUploader(AssestsManager::PHOTO_DIR_USER);
            $photoUploader->entity = Yii::$app->user->getIdentity();
            $photoUploader->relationship = REL_VIDEO_THUMB;
            $photoUploader->upload($fileDateArray);
          }
        }
  }

  public function deleteOldVideo($userId = false){
    if(!$userId){
      return false;
    }
    $res = [];
    $oldVideos = VideoMap::find()
    ->andWhere(['user_id' => $userId])
    ->andWhere(['!=', 'video_id', $this->id])
    ->addSelect('video_id')->asArray()->all();
    if(!empty($oldVideos)){
      foreach($oldVideos as $key => $val){
        $video = Videos::findOne($val['video_id']);
        if($video && $video->deleteVideo()){
          $res["{$video->id}_deleted"] = true;
        }
      }
    }
    VideoMap::deleteAll(['user_id' => $userId]);
  }

  private function deleteVideo(){
    $path = \yii\helpers\Url::to('@uploads/') ;
    if(empty($this->video_url)){
      return true;
    }
    $FQVPath = $path . $this->video_url;
    if($this->delete()){
      if(file_exists($FQVPath)){
        unlink($FQVPath);
      }
      return true;
    }
  }

  private function processVideo() {

    $fileExt = pathinfo($this->tmp_videos->name, PATHINFO_EXTENSION);
    $fileName = basename($this->tmp_videos->name, ".{$fileExt}");

    $ffmpeg = \FFMpeg\FFMpeg::create();
    $path = \yii\helpers\Url::to('@uploads/');
    $video = $ffmpeg->open($path . $this->tmp_videos->name);

    //saving video in .avi format to reduce quality
    $format = new \FFMpeg\Format\Video\X264();
    $format->setKiloBitrate(1000)->setAudioChannels(2)->setAudioKiloBitrate(256);
    $format->setAudioCodec("libmp3lame");
    $video->save($format, "{$path}{$fileName}.avi");

    //slice a thumbnail of the video and save it under same name
    $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1));
    $frame->save(\yii\helpers\Url::to('@uploads/' . $fileName) . '.jpg');
  }

  public function afterSave($insert, $changedAttributes) {
    if ($insert) {
      $videoMap = new VideoMap();
      $videoMap->load(['VideoMap' => ['video_id' => $this->id, 'user_id' => Yii::$app->user->getId(), 'is_primary' => true]]);
      $videoMap->save();
    }
    parent::afterSave($insert, $changedAttributes);
  }

}
