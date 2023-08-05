<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;
use yii\web\Controller;
use Yii;
use yii\helpers\Url;
/**
 * Description of BaseController
 *
 * @author smart
 */
class BaseController extends Controller {
    
    public function beforeAction($action) {
        if($action->actionMethod != "actionLogin" && !Yii::$app->user->getId()) {
        //  return $this->unAuthorizedRedirect();
        } elseif (Yii::$app->user->getId() && !Yii::$app->user->getIdentity()->isAdmin()) {
          //Yii::$app->user->logout();
          //return $this->unAuthorizedRedirect();
        }
        return parent::beforeAction($action);
    }
    
    public function unAuthorizedRedirect(){
        return $this->redirect(Url::to(Yii::$app->homeUrl.'site/login'));
    }
}
