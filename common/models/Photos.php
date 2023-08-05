<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use common\helpers\AssestsManager;
/**
 * This is the model class for table "{{%photos}}".
 *
 * @property integer $photos_id
 * @property string $photo_type
 * @property string $photo_title
 * @property string $photo_path
 * @property string $photo_details
 * @property boolean $isactive
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 *
 * @property User $createdby0
 * @property User $updatedby0
 * @property PhotosMap[] $photosMaps
 */
class Photos extends ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%photos}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['photo_title', 'photo_path', 'createdby'], 'required'],
            [['isactive'], 'boolean'],
            [['createdby', 'updatedby'], 'integer'],
            [['createddate', 'updateddate'], 'safe'],
            [['photo_title'], 'string', 'max' => 100],
            [['photo_path'], 'string', 'max' => 255],
            [['photo_details'], 'string', 'max' => 500],
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
            'photos_id' => 'Photos ID',
            'photo_title' => 'Photo Title',
            'photo_path' => 'Photo Path',
            'photo_details' => 'Photo Details',
            'isactive' => 'Isactive',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }
    
    public function fields(){
        return [
            'photos_id',
            'photo_title',
            'photo_path',
            'photo_details',
            'isactive',
            'image_url' => function ($model) {
               return [
                    'orig' => $model->getUrl(),
                    'thumb' =>  $model->getUrl('thumb'),
               ];
            },
        ];    
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotosMaps()
    {
        return $this->hasMany(PhotosMap::className(), ['photos_id' => 'photos_id']);
    }
    
    public function getPhotosMap()
    {
        return $this->hasOne(PhotosMap::className(), ['photos_id' => 'photos_id']);
    }
     
    /**
     * Create urls accordingliy
     */
    public function urlSession() {
        return $this->getUrl();
    }
    
    public static function urlActivity($activity, $image = 'enabled') {
        if(\Yii::$app->params['S3']['enabled']){
            return \Yii::$app->params['S3']['url'].AssestsManager::ACTIVITY_DIR.$activity->activity_id .'/' . $activity->getImages()[$image];
        }else{
            return Url::to('@web/../../uploads/activity/'. $activity->activity_id .'/' . $activity->getImages()[$image], true);
        }
    }
    
    public static function urlUser($user){
        if(!empty($user)){
            if(!$user->userphoto_path || !file_exists(Yii::getAlias('@uploads'). '/user/' . $user->userphoto_path)) {
                return Url::to(['site/create-thumb', 'data'=> '100-100-efefef-ff7e00', 'text'=> strtoupper($user->firstname[0])]);
            }
        
            return Url::to(['imagine/resize',
                'ImageBtv' => [
                    'type' => 'user',
                    'width' => 100,
                    'height' => 100,
                    'image_path' => $user->userphoto_path
                ]], true);
        }
    }
    public function urlUserImage(){
        if(!$this->photo_path) {
            return Url::to(['site/create-thumb', 'data' => '100-100-efefef-ff7e00', 'text'=> strtoupper($user->firstname[0])]);
        }
        
        //return orignal
        return $this->getUrl();
    }
    
    public function getPhotos(){
        return $this->hasOne(Photos::className(), ['photos_id' => 'photos_id'])->alias('photo');
    }
   
    public function urlPlace($place_id = null){
        //return Url::to('@web/../../uploads/host/place/'.$place_id."/". $this->photo_path, true);
        return $this->getUrl();
    }

    public function getUrl($key = ''){
        $photo = $this->photo_path;
        // return original url
        if(empty($key)){
            $image = $photo;
        }else{
            $path_parts = pathinfo($photo);
            $image = $path_parts['dirname'].DIRECTORY_SEPARATOR.$path_parts['filename']."_".$key.".".$path_parts['extension'];
        }

        if(\Yii::$app->params['S3']['enabled'] == 'true'){
            return \Yii::$app->params['S3']['url'].$image;
        }else{
            return Url::to('@web/../../uploads/'.$image, true);   
        }
    }

    public static function getPhotoUrl($photo_path = null, $key = ''){
        $photo = $photo_path;
        // return original url
        if(empty($key)){
            $image = $photo;
        }else{
            $path_parts = pathinfo($photo);
            $image = $path_parts['dirname'].DIRECTORY_SEPARATOR.$path_parts['filename']."_".$key.".".$path_parts['extension'];
        }

        if(\Yii::$app->params['S3']['enabled'] == 'true'){
            return \Yii::$app->params['S3']['url'].$image;
        }else{
            return Url::to('@web/../../uploads/'.$image, true);   
        }
    }
}
