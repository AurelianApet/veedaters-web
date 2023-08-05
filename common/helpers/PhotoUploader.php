<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\helpers;
use Yii;
use common\helpers\AssetManager;
use common\models\Photos;
use common\models\PhotosMap;
use common\helpers\CommonHelper;

/**
 * Provides a single method upload to move temp files to there respective locations
 * It moves file either to local file system or s3 based on whether we are in production 
 * environent or local. It also takes care of creating respective entry in photo & photo_map
 * tables if image is moved successfully
 * @author Ankit
 */
class PhotoUploader {

    private $rel_temp_folder_path;
    private $abs_temp_folder_path;

    /**
     * Root directory name for the entity for which we are moving the image from temp
     * directory will get created if not already present
     * @var [type]
     */
    public $entity_dir;

    /**
     * Root directory name specific the role for which we are uploading file
     * directory will get created if not already present
     * @var string
     */
    public $role_dir;
    
    public $fs = null;


    /**
     * respective entity object for which images are to be moved.
     * folder is created by the id of this object and images are stored with in it
     * @var ActiveRecord
     */
    public $entity = null;

    /**
     * relationship constant for entity
     * @var integer
     */
    public $relationship = null;

    public $unique_relationships = [];
    


    /**
     * config for image thumbnail sizes.
     * loaded from params
     * Sample:
     * [
     *      'thumb' => '64x64', 
     *      'medium'=>'120x120'
     *  ];
     * @var array
     */
    public $sizes = [];

    /**
     * [__construct description]
     * @param string $entity_dir       directory name for the entity
     * @param string $role_dir          directory name for the role
     * @param ActiveRecord $entity      entity object
     * @param int $relationship         relationship constant for photo map
     */
    function __construct($entity_dir, $role_dir = '', $entity = null, $relationship = null){
      $this->role_dir = $role_dir;
      $this->entity_dir = $entity_dir;
      $this->entity = $entity;
      $this->relationship = $relationship;

      $this->user = Yii::$app->user->identity;

      $this->rel_temp_folder_path = 'temp'.DIRECTORY_SEPARATOR;
      $this->abs_temp_folder_path = Yii::getAlias('@uploads').DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

    }

    /**
     * move files from temp locations to desired place
     * @param  mixed[string|array]  $temp_files   name of the temp files
     * @param  int                  $relationship override relationship value
     * @param  ActiveRecord         $entity    
     * @return array                $returns array of successfully moved files
     */
    function upload($temp_files, $relationship = null, $entity = null){
      
      $this->entity      = isset($entity) ? $entity : $this->entity; 
      $this->relationship = isset($relationship) ? $relationship : $this->relationship;

      //if entity is still not available
      if($this->entity == null){
        throw new \Exception("You must provide an entity before you can upload file", 1);
        
      }

      //if relationship is still not available
      if($this->relationship == null){
        throw new \Exception("You must provide relationship before you can upload file", 1);
        
      }

      //cast temp files to array if string is provied. that allows to pass both array or single file
      $temp_files = (array) $temp_files;

      $user = Yii::$app->user->identity;
      
      $successfull_uploads = [];
      foreach($temp_files as $filename) {

        if($this->moveTempFile($filename)){ 
          $photo = $this->savePhotoRecord($filename);
          if($photo){
            $successfull_uploads[] = $photo;
          }
        }
      }
      return $successfull_uploads;
    }
    
    
    function uploadtmp($temp_files, $relationship = null, $entity = null){
      
      $this->entity      = isset($entity) ? $entity : $this->entity; 
      $this->relationship = isset($relationship) ? $relationship : $this->relationship;

      //if entity is still not available
      if($this->entity == null){
        throw new \Exception("You must provide an entity before you can upload file", 1);
        
      }

      //if relationship is still not available
      if($this->relationship == null){
        throw new \Exception("You must provide relationship before you can upload file", 1);
        
      }

      //cast temp files to array if string is provied. that allows to pass both array or single file
      $temp_files = (array) $temp_files;

      $user = Yii::$app->user->identity;
      
      $successfull_uploads = [];
      foreach($temp_files as $filename) {

        if($this->moveTempFile($filename)){ 
          $photo = $this->savePhotoRecordTmp($filename);
          if($photo){
            $successfull_uploads[] = $photo;
          }
        }
      }
      return $successfull_uploads;
    }

    /**
     * move file to new location
     * @param  string $from name of temp file
     * @return boolean       
     */
    protected function moveTempFile($temp_name){      
      $from = $this->rel_temp_folder_path.$temp_name;
      $to = $this->getNewPath($temp_name);
      
      try {
        if(Yii::$app->fs->has($from)) { 
          $this->createThumbs($temp_name);
          Yii::$app->fs->put($to, Yii::$app->fs->read($from));
          Yii::$app->fs->delete($from);
          return true;
        }
      } catch (\Exception $e) {
        print_r($e);exit;
        return false;
      }

      return false;
    }

    /**
     * create thumbnails based on sizes. naming convention for thumbnails
     * is $filename_$sizekey.$ext. Thumbs are saved in the same folder as the 
     * original image
     * @param  string $temp_name name of the temp file
     * @return null
     */
    protected function createThumbs($temp_name){
      $abs_temp_path = $this->abs_temp_folder_path.$temp_name;
      $imagine = DigiBtvImage::factory();
      $path_parts = pathinfo($abs_temp_path);

      foreach($this->sizes as $key => $dimentions){
        list($width, $height) = explode('x', $dimentions);

        $thumb_name = $path_parts['filename']."_".$key.".".$path_parts['extension'];
        $thumb_path = $this->abs_temp_folder_path.$thumb_name;

        $imagine->getResized($abs_temp_path, $width, $height)->save($thumb_path);
        
        $move_thumb_to = $this->getNewPath($thumb_name);
        
        $rel_thumb_path = $this->rel_temp_folder_path.$thumb_name;
        
        //move thumbnail to final location
        $this->fs->put($move_thumb_to, Yii::$app->fs->read($rel_thumb_path));
        
        Yii::$app->fs->delete($rel_thumb_path); // delete thumb from temp
      }
    }

    /**
     * Save photo and photomap records in the db. It also makes sure that 
     * if the relationship is supposed on to be unique, i.e only one entry should exist
     * in photo map then it removed the existing entries and creates new one.
     * @param  string $filename name of the photo
     * @return mixed           Photo in case of success, false otherwise
     */
    public function savePhotoRecord($filename){
      if(in_array($this->relationship, $this->unique_relationships)){
        $this->deletePhotoRecord($this->entity->getId(), $this->relationship);
      }

      $photo = new Photos;
      $photo->photo_type = $this->relationship;
      $photo->photo_title = $filename;
      $photo->photo_path = $this->getNewPath($filename);
      $photo->photo_details = '';
      CommonHelper::updateModelDefault($photo);      
      if($photo->validate() && $photo->save()){          
        //map photo to place
        $photo_map = new PhotosMap;
        $photo_map->photos_id = $photo->photos_id;
        $photo_map->item_id = $this->entity->getId();
        $photo_map->relationship = $this->relationship;
        CommonHelper::updateModelDefault($photo_map);
        $photo_map->is_active = 1;
        $photo_map->save(); 

        return $photo;

      }else{
        return false;
      }
    }
    
    
    public function savePhotoRecordTmp($filename){
      $photo =  new \common\models\TmpPhoto();
      $photo->photo_type = $this->relationship;
      $photo->photo_title = $filename;
      $photo->photo_path = $this->getNewPath($filename);
      $photo->photo_details = '';
      CommonHelper::updateModelDefault($photo);
      if($photo->save()){
        return $photo;
      }else{
        return false;
      }
    }

    /**
     * delete existing photo map entry 
     * @param  int $item_id      id of entity
     * @param  string $relationship relationship constant value
     * @return boolean
     */
    protected function deletePhotoRecord($item_id, $relationship){

      $existingPhotos = PhotosMap::find()->andWhere(['item_id' => $item_id, 'relationship' => $relationship])->all();
      foreach($existingPhotos as $photo){
        $photo->delete();

        /**
         * @todo  remove actual images
         */

      }
      return true;
    }

    /**
     * returns the new path for file based o role & entity folders
     * @param  string $filename name of the file, usually the name of the file in temp folder
     * @return string new path
     */
    private function getNewPath($filename){
      
      $role_dir_path = (!empty($this->role_dir)) ? $this->role_dir.DIRECTORY_SEPARATOR : '';

      $entity_path = $role_dir_path.$this->entity_dir.DIRECTORY_SEPARATOR.$this->entity->getId();
      return $entity_path.DIRECTORY_SEPARATOR.$filename;
    }  
    
}
