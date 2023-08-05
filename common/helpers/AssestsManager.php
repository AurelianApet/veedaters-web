<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\helpers;
use Yii;
/**
 * Description of AssestsManager
 *
 * @author bhavan
 */
class AssestsManager {
    //put your code here
    const UPLOAD_PATH = "/uploads/";    
    
    const PHOTO_DIR_USER = "user";
    const PHOTO_DIR_MESSAGE = "message";
    
    
    static function dirShop(){
        return Yii::$app->homeUrl. AssestsManager::UPLOAD_PATH.AssestsManager::PHOTO_DIR_SHOP;
    }
    
}
