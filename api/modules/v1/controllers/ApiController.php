<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\modules\v1\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\User;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use api\modules\v1\controllers\UserController;
use yii\web\Response;

/**
 * Description of ApiController
 *
  "message": "SQLSTATE[42S02]: Base table or view not found: 1146   
 * @author Gurcharan Singh <gurcharan.singh@digimantra.com>
 */  
class ApiController extends \yii\rest\Controller{
    
    public $dataprovider;
    
    public $login_user;
     
    public $apiResponse = [];

    // error if no header token is set
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

    //put your code here
    /*
     * 
     * 
     */
    public function beforeAction($action) {
        if($action->controller->id == 'api' && $action->id == 'index' ){
            Yii::$app->request->headers['Accept']= 'application/json; charset=UTF-8';
//            c(Yii::$app->request->headers);
            return parent::beforeAction($action);    
        }
        $headers = Yii::$app->request->headers;
        if(isset($headers['chibha-header-token']) && !empty($headers['chibha-header-token'])){                        
            if(is_numeric($headers['chibha-header-token'])){
                Yii::$app->user->setIdentity(\common\models\User::findOne(["id" => $headers['chibha-header-token']]));           
            }
        }        
        $this->login_user = Yii::$app->user->getIdentity();        
       
        return parent::beforeAction($action);
    }   
    
    /**
     * @deprecated since version number
     * @param type $current_page
     * @param type $per_page
     * @return type
     * @throws Exception
     */
    public function getPagination($per_page = 10){
       return $this->pagination();
    }
    
    public function pagination(){
        if(!$this->dataprovider) throw new Exception('Please set the dataprovider before getPagination');        
        $total = $this->dataprovider->getTotalCount(); 
        $per_page = $this->dataprovider->pagination->getPageSize();        
        $load_more = false;       
        $current_page = $this->dataprovider->pagination->page+1;
        if($total && $per_page && ($total/($per_page))>$current_page){$load_more = true;}        
        return ['total' => $total, 'load_more' => $load_more, 'page_no' => $current_page];
    }
        
    public function success($data = [], $debug = false){        
        if($debug) $data = ArrayHelper::merge($data, ['debug' => $this->debug()]);        

        $cdn_url = "http://".$_SERVER['HTTP_HOST'].'/imagine/resize/';
        return ArrayHelper::merge(['is_success' => true],$data);       
    }
    
    public function error($error = []){ 
        if(!count($error)) return false;        
        return \yii\helpers\ArrayHelper::merge(['is_success' => false], $error);
    } 
    
    public function exception(Exception $e){        
        return \yii\helpers\ArrayHelper::merge(['is_success' => false], [
            'exception' => [
                'message' => $e->getMessage(),
                'code' => $e->getCode()                
            ]
        ]);
    } 
        
    public function debug(){        
        return Yii::getLogger()->getProfiling();
    }
        
    public function unAthorizeAccess(){
        throw new \yii\web\UnauthorizedHttpException('You are not allowed to perform this action.');
    }

    public function actionIndex(){
        $this->userApis();
        return $this->success([
            'info' => 'Veedator API',
            'api_health' => 'Good',
            'methods' => $this->apiResponse
        ]);
    }
    
    private function apiPostHeaders($headerToken = true){
        return [
            'method' => 'POST',
            'authentication' => ($headerToken ? 'required' : 'not required')
        ];
    }


    private function apiGetHeaders($headerToken = true){
        return [
            'method' => 'GET',
            'header_token_req' => ($headerToken ? 'yes' : 'no')
        ];
    }
    
    private function userApis(){
        $userPrefix = 'user';
        $userMethods = [
            'user' => [
                'base_path' => '/v1/user/',
                'allowed_methods' => [
                    "signup" => array_merge($this->apiPostHeaders(false), $this->getMethodParams($userPrefix, 'signup')),
                    "social-signup" => array_merge($this->apiPostHeaders(false), $this->getMethodParams($userPrefix, 'social-signup')),
                    "social-login" => array_merge($this->apiPostHeaders(false), $this->getMethodParams($userPrefix, 'social-login')),
                    "login" => array_merge($this->apiPostHeaders(false), $this->getMethodParams($userPrefix, 'login')),
                    "user-detail" => array_merge($this->apiGetHeaders(), $this->getMethodParams($userPrefix, 'user-detail')),
                    "forgotpassword" => array_merge($this->apiPostHeaders(),$this->getMethodParams($userPrefix, 'forgotpassword')),
                    "get-profile" => array_merge($this->apiGetHeaders(), $this->getMethodParams($userPrefix, 'get-profile')),
                    "list" => array_merge($this->apiGetHeaders(), $this->getMethodParams($userPrefix, 'list')),
                    "block-list" => array_merge($this->apiGetHeaders(), $this->getMethodParams($userPrefix, 'block-list')),
                    "favlist" => array_merge($this->apiGetHeaders(), $this->getMethodParams($userPrefix, 'favlist')),
                    "video" => array_merge($this->apiPostHeaders(), $this->getMethodParams($userPrefix, 'video')),
                    "profile-update" => array_merge($this->apiPostHeaders(), $this->getMethodParams($userPrefix, 'profile-update')),
                    "review" => array_merge($this->apiPostHeaders(), $this->getMethodParams($userPrefix, 'review')),
                    "block" => array_merge($this->apiPostHeaders(), $this->getMethodParams($userPrefix, 'block')),
                    "unblock" => array_merge($this->apiPostHeaders(), $this->getMethodParams($userPrefix, 'unblock')),
                ]
            ]            
        ];
        $this->apiResponse = array_merge($this->apiResponse, $userMethods);
        return $this;
    }
   
    private function getMethodParams($path, $method = 'index'){
        return Yii::$app->params['api_methods'][$path][$method];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /*public function behaviors() {        
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::className()],
                    ['class' => QueryParamAuth::className(), 'tokenParam' => 'accessToken'],
                ]
            ],
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::className()
            ],
            'class' => \yii\filters\ContentNegotiator::className(), 
        ]);
    }*/

}
