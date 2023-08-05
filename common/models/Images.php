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
 * This is the model class for table "{{%images}}".
 *
 * @property integer $id
 * @property string $images_url
 * @property string $image_title
 * @property string $image_description
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property ImageoMap[] $imageMaps
 */
class Images extends ModelBase {

  public $tmp_image;
  public $user_id;

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return '{{%images}}';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      [['user_id','createddate', 'updateddate'], 'safe'],
      [['createdby', 'updatedby'], 'integer'],
      [['image_url', 'image_title', 'image_description'], 'string', 'max' => 255],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'id' => 'ID',
      'user_id'=>'User ID',
      'image_url' => 'Image Url',
      'image_title' => 'Image Title',
      'image_description' => 'Image Description',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getImageMaps() {
    return $this->hasMany(ImageMap::className(), ['image_id' => 'id']);
  }

  public function saveImage() {
    $transaction = Yii::$app->db->beginTransaction();
    if (count($this->tmp_images) > 0) {
      $this->image_title = $this->tmp_videos->name;
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

    $ext = pathinfo($this->tmp_images, PATHINFO_EXTENSION);
    $imgPath = "user/". \Yii::$app->user->getId()."/" . "user_image_{$this->id}" . ".{$ext}";
    $filePath = \yii\helpers\Url::to('@uploads/' . $imgPath);
    if ($this->tmp_images->saveAs($filePath, true)) {      
      return $imgPath;
      $this->processImage();
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

  private function deleteImage(){
    $path = \yii\helpers\Url::to('@uploads/') ;
    if(empty($this->image_url)){
      return true;
    }
    $FQVPath = $path . $this->image_url;
    if($this->delete()){
      if(file_exists($FQVPath)){
        unlink($FQVPath);
      }
      return true;
    }
  }

  private function processImage() {

    $fileExt = pathinfo($this->tmp_images->name, PATHINFO_EXTENSION);
    $fileName = basename($this->tmp_images->name, ".{$fileExt}");

    $ffmpeg = \FFMpeg\FFMpeg::create();
    $path = \yii\helpers\Url::to('@uploads/');
    $image = $ffmpeg->open($path . $this->tmp_images->name);

    //saving image in .png format to reduce quality
    
    $image->save($format, "{$path}{$fileName}.png");

    //slice a thumbnail of the image and save it under same name
    $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1));
    $frame->save(\yii\helpers\Url::to('@uploads/' . $fileName) . '.jpg');
  }

  public function afterSave($insert, $changedAttributes) {
    if ($insert) {
      $imageMap = new ImageMap();
      $imageMap->load(['ImageMap' => ['image_id' => $this->id, 'user_id' => Yii::$app->user->getId(), 'is_primary' => true]]);
      $imageMap->save();
    }
    parent::afterSave($insert, $changedAttributes);
  }

}
