<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\controllers\ApiController;
use common\models\User;
use common\models\LoginForm;
use common\models\Videos;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VideoController extends ApiController {
  
  public $modelClass = '\common\models\Videos';
  
  public function initHeader()
    { 
       $headers = Yii::$app->request->headers;

        if(!isset($headers['veedater-header-token']) && empty($headers['veedater-header-token'])){ 
            throw new \yii\web\HttpException(400,'header token is missing', 405);
        }

        if(isset($headers['veedater-header-token']) && !empty($headers['veedater-header-token'])){
            $token = $headers['veedater-header-token'];
            $user = User::find()->andWhere(['id' => $token])->one();
            Yii::$app->user->setIdentity($user);           
            if(empty($user))
            {
               throw new \yii\web\HttpException(400,'header token is invalid', 405);
            }
        } 
        true;
        
    }
  
  
  public function actionUpload(){
    $this->initHeader();  
    $model = new Videos();
    $model->load(Yii::$app->request->post());
    $model->tmp_videos = \yii\web\UploadedFile::getInstanceByName('Videos[video_data]');
    $model->validate(); 
    $model->deleteOldVideo(Yii::$app->user->getId());
    $model->saveVideo();
    $model->saveThumb();
    
    return $this->success(['is_success' => true, 'video' => $model]);      
  }
}