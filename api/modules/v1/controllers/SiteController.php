<?php 

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\modules\v1\controllers;
use Yii;
/**
 * Description of SiteController
 *
 * @author Gurcharan  Singh <Gurcharan.singh@digimantra.com>
 */
class SiteController extends ApiController{
    //put your code here
    
    public function actionError() { 
        $exception = Yii::$app->errorHandler->exception;        
        return ['status' => $exception->statusCode, 'message' => $exception->getMessage()];
    }
        
    public function actionTestHeaders($pretty){
      if($pretty == 1){
        c(Yii::$app->request->headers);exit;
      }
      return $this->success(['headers' => Yii::$app->request->headers]);
    }
}
