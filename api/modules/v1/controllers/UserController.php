<?php

namespace api\modules\v1\controllers;

use Yii;
use common\models\User;
use common\models\LoginForm;
use \common\helpers\VeedaterEmail;
use \backend\models\UserMeta;
use common\models\Photos;
use common\models\PhotosMap;
use common\models\Subscription;
use common\helpers\CommonHelper;
use common\helpers\AssestsManager;
use \backend\models\Ratings;
use \common\models\Blocklist;
use \common\models\Notification;
use \common\models\UserPreference;
use common\models\VideoMap;
use common\models\Videos;
use yii\db\Expression;
use Stripe\Stripe;

/**
 * User Controller API
 *
 */
class UserController extends ApiController {

  public $modelClass = '\common\models\User';
  public $hasErrors = false;
  public $errors = null;
  public $debug_environ = 'distribution';
  
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

  public function actionSignup() {
    if (Yii::$app->request->isPost) {

      if (empty(Yii::$app->request->post()['User']['email'])) {
        return $this->error(['error' => "email is required"]);
      }
      if (empty(Yii::$app->request->post()['User']['username'])) {
        return $this->error(['error' => "username is required"]);
      }
      if (empty(Yii::$app->request->post()['User']['password'])) {
        return $this->error(['error' => "password is required"]);
      }

      $user = User::find()->andWhere(['username' => Yii::$app->request->post()['User']['username']])->orWhere(['email' => Yii::$app->request->post()['User']['email']])->one();
      
      if (!empty($user)) {
        return $this->error(['error' => "username/email already exist"]);
      }
      
      return $this->signup();
    }
  }

  // social signup
  public function actionSocialSignup() {
    if (Yii::$app->request->isPost) {

      if (empty(Yii::$app->request->post()['User']['social_id'])) {
        return $this->error(['error' => "social ID is required"]);
      }

      $user = User::find()->andWhere(['social_id' => Yii::$app->request->post()['User']['social_id']])->one();
      if (!empty($user)) {
        return $this->error(['error' => "user already exists"]);
      }

      $user = new \common\models\User();
      $user->load(\Yii::$app->request->post());
      $user->setPassword('123465');
      $user->username = Yii::$app->request->post()['User']['social_id'];
      $user->email = Yii::$app->request->post()['User']['email'];
      $user->validate();
      if (!$user->save()) {
        return $this->error(['error' => $user->getErrors()]);
      } else {
        return $this->success(['is_success' => true, 'user_id' => $user->id]);
      }
      return $this->returnResponse();
    }
  }

  // social login
  public function actionSocialLogin() {
    if (Yii::$app->request->isPost) {

      if (empty(Yii::$app->request->post()['User']['social_id'])) {
        return $this->error(['error' => "social_id is required"]);
      }

      if (empty(Yii::$app->request->post()['User']['social_media_type'])) {
        return $this->error(['error' => "social_media_type is required"]);
      }

      $user = '';
      $user = User::find()->addSelect(['social_id', 'id', 'social_media_type'])->where(['social_id' => Yii::$app->request->post()['User']['social_id'], 'social_media_type' => Yii::$app->request->post()['User']['social_media_type']])->one();
      if (empty($user)) {
        return $this->error(['error' => "user does not exist"]);
      } else {
        return $this->success(['is_success' => true, 'user' => $user]);
      }
    }
  }

  public function actionLogin() {
    if (Yii::$app->request->isPost) {
      $user = '';
      if (isset(Yii::$app->request->post()['User']['social_media_type'])) {
        $user = $this->getUser(Yii::$app->request->post()['User']['social_media_type']);

      } else {
        if (empty(Yii::$app->request->post()['User']['username'])) {
          return $this->error(['error' => "username is required"]);
        }
        if (empty(Yii::$app->request->post()['User']['password'])) {
          return $this->error(['error' => "password is required"]);
        }
        $user = $this->getUser();
      }
      
      // update device token
      $device = Notification::find()->andWhere(['user_id' => $user['id'],'device_token' => Yii::$app->request->post()['User']['device_token']])->one();
      if(empty($device))
      {
          $device = new Notification();
          $device->user_id = $user['id'];
          $device->device_type = !empty(Yii::$app->request->post()['User']['device_type']) ? Yii::$app->request->post()['User']['device_type']:'1';
          $device->device_token = !empty(Yii::$app->request->post()['User']['device_token']) ? Yii::$app->request->post()['User']['device_token']:'1';
          $device->save();
      }
      else
      {
          $device = Notification::find()->andWhere(['user_id' => $user['id'],'device_token' => Yii::$app->request->post()['User']['device_token']])->one();
          $device->user_id = $user['id'];
          $device->device_type = !empty(Yii::$app->request->post()['User']['device_type']) ? Yii::$app->request->post()['User']['device_type']:'1';
          $device->device_token = !empty(Yii::$app->request->post()['User']['device_token']) ? Yii::$app->request->post()['User']['device_token']:'1';
          $device->save();
      }

      $subscription['subscription'] = Subscription::find()
                      ->andWhere(['user_id'=>$user['id']])
                      ->asArray()
                      ->one();
      // c($subscription); die;

     if (empty($user) || !$user) {
        return $this->error(['error' => ((!is_null($this->errors)) ? $this->errors : "User does not exsist")]);
      } else { 
        if (isset(Yii::$app->request->post()['User']['social_media_type'])) {
          // unset($user['password_hash']);
          return $this->success(['is_success' => true, 'user' => array_merge($user, User::loadUserData($user['id']))]);
        } else {
          if (Yii::$app->getSecurity()->validatePassword(Yii::$app->request->post()['User']['password'], $user['password_hash'])) {
            return $this->success(['is_success' => true, 'user' => array_merge($user, User::loadUserData($user['id']), $subscription)]);
          } else {
            return $this->error(['error' => "Username or password is not correct"]);
          }
        }
      }
    }
  }

  // forgot password
  public function actionUserDetail($id) {
    $this->initHeader();  
    $user = $this->fetchUser($id);
    if ($user) {
      unset($user['password_hash']);
      unset($user['password_reset_token']);
      return $this->success(['is_success' => true, 'user' => $user]);
    } else {
      return $this->error(['error' => "User Id invalid"]);
    }
  }

  public function actionForgotpassword() {
    if (Yii::$app->request->isPost) {

      if (empty(Yii::$app->request->post()['User']['email'])) {
        return $this->error(['error' => "email is required"]);
      }

      // 1. check username and send random password
      if (!empty(Yii::$app->request->post()['User']['email'])) {
        $check_user = '';
        $check_user = User::find()->where(['email' => Yii::$app->request->post()['User']['email'], 'is_active' => User::$IS_ACTIVE])->one();
        if (!empty($check_user)) {
          $code = $check_user->getVerificationCode();
          VeedaterEmail::send($check_user->email, 'Password recovery code from Veedater App', ["html" => "resend-code-html", "code" => $code]);
          $check_user->password_hash = Yii::$app->security->generatePasswordHash($code);
          $check_user->save();
          return $this->success(['is_success' => true, 'message' => 'password changed!']);
        } else {
          return $this->error(['error' => "user does not exsist/not active"]);
        }
      }
    }
  }
  
  
  public function actionSupport() {
    $this->initHeader();  
    if (Yii::$app->request->isPost) {

      if (empty(Yii::$app->request->post()['User']['message'])) {
        return $this->error(['error' => "message is required"]);
      }

      if (!empty(Yii::$app->request->post()['User']['message'])) {
        $check_user = '';
        $check_user = User::find()->where(['id' => Yii::$app->user->getId() , 'is_active' => User::$IS_ACTIVE])->one();
        if (!empty($check_user)) {
          VeedaterEmail::send('gurcharan.singh@digimantra.com', 'Support for Veedater App', ["html" => "support-html"]);
          return $this->success(['is_success' => true, 'message' => 'request message sent!']);
        } else {
          return $this->error(['error' => "user does not exist/is not active"]);
        }
      }
    }
  }
  
  
  private function getAdminEmail()
  {
      $adminEmail = User::find()
                    ->joinWith(['adminEmail',true,'RIGHT JOIN'])
                    ->one();
      c($adminEmail); die;
  }

  
  public function actionChangePassword() {
    $this->initHeader();    
    if (Yii::$app->request->isPost) {

      if (empty(Yii::$app->request->post()['User']['old_password'])) {
        return $this->error(['error' => "old password  is required"]);
      }
      if (empty(Yii::$app->request->post()['User']['new_password'])) {
        return $this->error(['error' => "new password  is required"]);
      }

      // 1. check old password 
      if (!empty(Yii::$app->request->post()['User']['old_password'])) {
        $check_user = '';
        $check_user = User::find()->where(['id' => \Yii::$app->user->getId(), 'is_active' => User::$IS_ACTIVE])->one();
        if (!empty($check_user)) {
            
            // check old password
            if(Yii::$app->getSecurity()->validatePassword(Yii::$app->request->post()['User']['old_password'], $check_user->password_hash))
            {
                 
                $check_user->password_hash = Yii::$app->security->generatePasswordHash(Yii::$app->request->post()['User']['new_password']);
                $check_user->save();
                return $this->success(['is_success' => true, 'message' => 'password changed!']);
            }
            else
            {
                return $this->error(['is_success' => false, 'message' => 'Invalid password']);
            }
          
        } else {
          return $this->error(['error' => "user does not exsist/not active"]);
        }
      }
    }
  }
  
  // user get profile
  public function actionGetProfile() {
    $this->initHeader();
    if (Yii::$app->request->isGet) {
      $useData = $this->fetchUser(Yii::$app->user->getId());
      if (empty($useData)) {
        return $this->error(['error' => "user does not exist"]);
      }
      if (empty($useData['userPhoto'])) {
        $useData['userPhoto'] = null;
      } 
       $subscription['subscription'] = Subscription::find()
                      ->andWhere(['user_id'=>$useData['id']])
                      ->asArray() 
                      ->one();
      return $this->success(['is_success' => true, 'user' =>array_merge($useData, $subscription)]);
    }
  }

  // user list
  public function actionList() {
    $this->initHeader();
    if (Yii::$app->request->isGet) {
      $user = [];
      $lat = 30.854;
      $lng = 75.8626;
      $pageLimit = 20;
      $offset = 0;
      
      $nearby = Yii::$app->request->get()['nearby'];
      if(!empty($nearby) && $nearby == 0)
      {
          $userLat = User::find()->andWhere(['id' => \Yii::$app->user->getId()])->one();  
          $lat = !empty($userLat->latitude) ? $userLat->latitude: $lat;
          $lng = !empty($userLat->longitude) ? $userLat->longitude: $lng;
      }
      elseif(!empty($nearby) && $nearby == 1)
      {
          $lat = isset(Yii::$app->request->get()['lat']) ? Yii::$app->request->get()['lat']:'';
          $lng = isset(Yii::$app->request->get()['lng']) ? Yii::$app->request->get()['lng']:'';
      }
      
      
      $distance = "";
      $distanceInit = 0;
      $minAge = 0;
      $maxAge = 50;
      $gender = '';
      
      
      // if preference are saved in db
      $userPreference = UserPreference::find()->andWhere(['user_id' => \Yii::$app->user->getId()])->one();
      $userLat = User::find()->andWhere(['id' => \Yii::$app->user->getId()])->one();
      if(!empty($userPreference))
      {
          $lat = !empty($userLat->latitude) ? $userLat->latitude: $lat;
          $lng = !empty($userLat->longitude) ? $userLat->longitude: $lng;
          $distance = !empty($userPreference->distance) ? !empty($userPreference->distance): $distance;
          $distanceInit = 0;
          $minAge = !empty($userPreference->min_age) ? $userPreference->min_age : $minAge;
          $maxAge = !empty($userPreference->max_age) ? $userPreference->max_age : $maxAge;
          $gender = !empty($userPreference->gender) ? $userPreference->gender : $gender;
      }
      
      
            
      // if get params are set
      if (isset(Yii::$app->request->get()['distance']) && !empty(Yii::$app->request->get()['distance'])) {
        $distance = Yii::$app->request->get()['distance'];
      }
      if (isset(Yii::$app->request->get()['gender'])) {
          if(Yii::$app->request->get()['gender'] == 'All' || Yii::$app->request->get()['gender'] == 'all')
          {
            $gender = '';
          }
          else
          {
              $gender = Yii::$app->request->get()['gender'];
          }
          
      }
      
      if (isset(Yii::$app->request->get()['age']) && !empty(Yii::$app->request->get()['age'])) {
        $age = explode('-', trim(Yii::$app->request->get()['age']));
        $minAge = $age[0];
        $maxAge = $age[1];
      }
      
      $exclude = [Yii::$app->user->getId()];
      $blockedUsers = BlockList::find()->addSelect('blocked_id')->andWhere(['blocked_by_id' => Yii::$app->user->getId(), 'is_blocked' => REL_USER_BLOCK])->asArray()->all();
      if (!empty($blockedUsers) || !$blockedUsers) {
        $exclude = array_merge($exclude, array_column($blockedUsers, 'blocked_id'));
      }
      
      $excludeAdmin = [];
      // get admin id
      $excludeAdmin = User::find()
                      ->joinWith('admin')  
                      ->asArray()
                      ->one();
      if(!empty($excludeAdmin))
      {
        $excludeAdmin = $excludeAdmin['id'];
      }
     
      
      $query = User::find()->alias('uS')->addSelect(['uS.id', 'username', 'name', 'address', 'email', 'latitude', 'longitude'])
        ->addSelect(['uM.meta_value', 'uM.meta_key', 'uM.user_id', 'TIMESTAMPDIFF(YEAR, uM.meta_value, CURDATE()) AS age'])
        ->joinWith(['userPhoto' => function($q) {$q->addSelect(['photo_path', 'photos_id']);}], true, 'LEFT JOIN')
        ->joinWith(['userVideo uV' => function($q) {$q->addSelect(['uV.id', 'uV.video_url']);}], true, 'LEFT JOIN')          
        ->joinWith(['userVideoThumb uVTs' => function($q) {$q->addSelect(['uVTs.photos_id', 'uVTs.photo_path']);}], true, 'LEFT JOIN')          
        ->joinWith(['userAgeValue' => function($q) {}], false, 'LEFT JOIN')
        ->joinWith(['userGender' => function($q) use($gender)  {
            if (!empty($gender))
                { 
                    $q->andWhere(['uMG.meta_value' => $gender]);
                }
            }], true, 'LEFT JOIN')
        ->joinWith(['userLike uL' => function($q) {$q->andOnCondition(['uL.createdby' => Yii::$app->user->getId()]);}], true, 'LEFT JOIN');
        $query->addSelect([new Expression('ROUND(LAT_LNG_DISTANCE(:userLat, :userLng, latitude, longitude), 0) as miles', [':userLat' => $lat, ':userLng' => $lng])]);
        if($distance)
        {
            $query->andHaving('miles BETWEEN ' .$distanceInit.' AND '.$distance);          
        }
        
        
        if(!empty($maxAge)) {            
          $query->andHaving('age BETWEEN ' .$minAge.' AND '.$maxAge);
        }
        $query->andWhere(['NOT IN', 'uS.id', $exclude]);
        if(!empty($excludeAdmin))
        {
            $query->andWhere(['NOT IN', 'uS.id', $excludeAdmin]);
        }
        $query->andWhere(['!=','uS.is_active', User::$IN_ACTIVE]);
        $query->orderBy('id ASC');
        $query->asArray();
        //echo $query->createCommand()->rawSql;exit;
        
        $this->dataprovider = new \yii\data\ActiveDataProvider([
              'query' => $query,
              'pagination' => [
                'pageSize' => 50
              ]
        ]);
        
        if(isset(Yii::$app->request->get()['page']) && Yii::$app->request->get()['page'] == 0)
        { 
            $user = $query->asArray()->all();
        }
        elseif(isset(Yii::$app->request->get()['page']) && !empty(Yii::$app->request->get()['page']) && Yii::$app->request->get()['page'] != 0)
        {
            $query->asArray();
            $user = $this->dataprovider->getModels();
        }
        elseif(!isset(Yii::$app->request->get()['page']))
        {
            $user = $query->asArray()->all();
        }
        
        
       // $user = $query->asArray()->all();
       // $user = $this->dataprovider->getModels();
        $dataArr = [];
        if (!empty($user)) {
          foreach ($user as $key => $value) {
            $dataArr[] = $value;
            $dataArr[$key]['user_meta']['age'] = !empty($value['age']) ? $value['age'] : null;
            $dataArr[$key]['user_meta']['gender'] = !empty($value['userGender']['meta_value']) ? $value['userGender']['meta_value'] : null;
            $dataArr[$key]['user_meta']['like'] = !empty($value['userLike']['like']) ? (int) $value['userLike']['like'] : 0;
            unset($dataArr[$key]['userGender']);
            unset($dataArr[$key]['userAge']);
            unset($dataArr[$key]['userLike']);
          }
        }

        if (empty($user)) {
          return $this->success(['is_success' => true, 'message' => 'No User found']);
        } else {
          return $this->success(['is_success' => true, 'user' => $dataArr,"pagination" => $this->getPagination(50)]);
        }
    }
  }
  
  
//  public function actionList2() {
//    $this->initHeader();
//    if (Yii::$app->request->isGet) {
//      $user = [];
//      $lat = 30.854;
//      $lng = 75.8626;
//      $pageLimit = 20;
//      $offset = 0;
//      
//      $nearby = Yii::$app->request->get()['nearby'];
//      if(!empty($nearby) && $nearby == 0)
//      {
//          $userLat = User::find()->andWhere(['id' => \Yii::$app->user->getId()])->one();  
//          $lat = !empty($userLat->latitude) ? $userLat->latitude: $lat;
//          $lng = !empty($userLat->longitude) ? $userLat->longitude: $lng;
//      }
//      elseif(!empty($nearby) && $nearby == 1)
//      {
//          $lat = isset(Yii::$app->request->get()['lat']) ? Yii::$app->request->get()['lat']:'';
//          $lng = isset(Yii::$app->request->get()['lng']) ? Yii::$app->request->get()['lng']:'';
//      }
//      
//      
//      $distance = "";
//      $distanceInit = 0;
//      $minAge = 0;
//      $maxAge = 50;
//      $gender = '';
//      
//      
//      // if preference are saved in db
//      $userPreference = UserPreference::find()->andWhere(['user_id' => \Yii::$app->user->getId()])->one();
//      $userLat = User::find()->andWhere(['id' => \Yii::$app->user->getId()])->one();
//      if(!empty($userPreference))
//      {
//          $lat = !empty($userLat->latitude) ? $userLat->latitude: $lat;
//          $lng = !empty($userLat->longitude) ? $userLat->longitude: $lng;
//          $distance = !empty($userPreference->distance) ? !empty($userPreference->distance): $distance;
//          $distanceInit = 0;
//          $minAge = !empty($userPreference->min_age) ? $userPreference->min_age : $minAge;
//          $maxAge = !empty($userPreference->max_age) ? $userPreference->max_age : $maxAge;
//          $gender = !empty($userPreference->gender) ? $userPreference->gender : $gender;
//      }
//      
//            
//      // if get params are set
//      if (isset(Yii::$app->request->get()['distance']) && !empty(Yii::$app->request->get()['distance'])) {
//        $distance = Yii::$app->request->get()['distance'];
//      }
//      if (isset(Yii::$app->request->get()['gender'])) {
//          if(Yii::$app->request->get()['gender'] == 'All' || Yii::$app->request->get()['gender'] == 'all')
//          {
//            $gender = '';
//          }
//          else
//          {
//              $gender = Yii::$app->request->get()['gender'];
//          }
//          
//      }
//      
//      if (isset(Yii::$app->request->get()['age']) && !empty(Yii::$app->request->get()['age'])) {
//        $age = explode('-', trim(Yii::$app->request->get()['age']));
//        $minAge = $age[0];
//        $maxAge = $age[1];
//      }
//      
//      $exclude = [Yii::$app->user->getId()];
//      $blockedUsers = BlockList::find()->addSelect('blocked_id')->andWhere(['blocked_by_id' => Yii::$app->user->getId(), 'is_blocked' => REL_USER_BLOCK])->asArray()->all();
//      if (!empty($blockedUsers) || !$blockedUsers) {
//        $exclude = array_merge($exclude, array_column($blockedUsers, 'blocked_id'));
//      }
//
//      $query = User::find()->alias('uS')->addSelect(['uS.id', 'username', 'name', 'address', 'email', 'latitude', 'longitude'])
//        ->addSelect(['uM.meta_value', 'uM.meta_key', 'uM.user_id', 'TIMESTAMPDIFF(YEAR, uM.meta_value, CURDATE()) AS age'])
//        ->joinWith(['userPhoto' => function($q) {$q->addSelect(['photo_path', 'photos_id']);}], true, 'LEFT JOIN')
//        ->joinWith(['userVideo uV' => function($q) {$q->addSelect(['uV.id', 'uV.video_url']);}], true, 'LEFT JOIN')          
//        ->joinWith(['userVideoThumb uVTs' => function($q) {$q->addSelect(['uVTs.photos_id', 'uVTs.photo_path']);}], true, 'LEFT JOIN')          
//        ->joinWith(['userAgeValue' => function($q) {}], false, 'LEFT JOIN')
//        ->joinWith(['userGender' => function($q) use($gender)  {
//            if (!empty($gender))
//                { 
//                    $q->andWhere(['uMG.meta_value' => $gender]);
//                }
//            }], true, 'LEFT JOIN')
//        ->joinWith(['userLike uL' => function($q) {$q->andOnCondition(['uL.createdby' => Yii::$app->user->getId()]);}], true, 'LEFT JOIN');
//        $query->addSelect([new Expression('ROUND(LAT_LNG_DISTANCE(:userLat, :userLng, latitude, longitude), 0) as miles', [':userLat' => $lat, ':userLng' => $lng])]);
//        if($distance)
//        {
//            $query->andHaving('miles BETWEEN ' .$distanceInit.' AND '.$distance);          
//        }
//        
//        
//        if (!empty($minAge) && !empty($maxAge)) {            
//          $query->andHaving('age BETWEEN ' .$minAge.' AND '.$maxAge);
//        }
//        
//        // pagination logic
//        $perpage = 20;
//        if(isset(Yii::$app->request->get()['page']) & !empty(Yii::$app->request->get()['page'])){
//	      $curpage = Yii::$app->request->get()['page'];
//        }else{
//        $curpage = 1;
//        }
//        
//        $start = ($curpage * $perpage) - $perpage;
//
//        $query->andWhere(['NOT IN', 'uS.id', $exclude]);
//        $query->andWhere(['!=','uS.is_active', User::$IN_ACTIVE]);
//        $query->orderBy('id ASC');
//        $query->limit($perpage);
//        $query->offset($start);
//        $user = $query->asArray()->all();
//                
////        $this->dataprovider = new \yii\data\ActiveDataProvider([
////              'query' => $query,
////              'pagination' => [
////                'pageSize' => 50
////              ]
////        ]);
//        
////        if(isset(Yii::$app->request->get()['page']) && Yii::$app->request->get()['page'] == 0)
////        {
////            $user = $query->asArray()->all();
////        }
////        elseif(isset(Yii::$app->request->get()['page']) && Yii::$app->request->get()['page'] == 1)
////        {
////            $query->asArray();
////            $user = $this->dataprovider->getModels();
////        }
////        elseif(!isset(Yii::$app->request->get()['page']))
////        {
////            $user = $query->asArray()->all();
////        }
//       // $user = $query->asArray()->all();
//        
//        $dataArr = [];
//        if (!empty($user)) {
//          foreach ($user as $key => $value) {
//            $dataArr[] = $value;
//            $dataArr[$key]['user_meta']['age'] = !empty($value['age']) ? $value['age'] : null;
//            $dataArr[$key]['user_meta']['gender'] = !empty($value['userGender']['meta_value']) ? $value['userGender']['meta_value'] : null;
//            $dataArr[$key]['user_meta']['like'] = !empty($value['userLike']['like']) ? (int) $value['userLike']['like'] : 0;
//            unset($dataArr[$key]['userGender']);
//            unset($dataArr[$key]['userAge']);
//            unset($dataArr[$key]['userLike']);
//          }
//        }
//
//        if (empty($user)) {
//          return $this->success(['is_success' => true, 'message' => 'No User found']);
//        } else {
//          return $this->success(['is_success' => true, 'user' => $dataArr]);
//        }
//    }
//  }

        // get blocklist 
  public function actionBlocklist() {
    $this->initHeader();
    if (Yii::$app->request->isGet) {
      $user = [];
      $blockedUsers = BlockList::find()->alias('bl')
          ->joinWith(['blockedUser u' => function($q) {
              $q->joinWith('userPhoto uPH');
              $q->joinWith('userGender uMG');
              $q->joinWith('userAge uM');
            }], false)
          ->addSelect(['uPH.photo_path', 'uMG.meta_value as gender', 'uM.meta_value as dob', 
          'u.id', 'u.name', 'u.username', 'TIMESTAMPDIFF(YEAR, uM.meta_value, CURDATE()) AS age'])
          ->andWhere(['blocked_by_id' => Yii::$app->user->getId(), 'is_blocked' => REL_USER_BLOCK])
          ->andWhere(['!=','u.is_active', User::$IN_ACTIVE])
          ->asArray()->all();
      if (empty($blockedUsers) || !$blockedUsers) {
        return $this->success(['is_success' => true, 'message' => "Blocklist is empty"]);
      }
      return $this->success(['is_success' => true, 'blocked_users' => $blockedUsers]);
    }
  }

  public function actionFavlist() {
    $this->initHeader();
    if (Yii::$app->request->isGet) {
      $user = [];
       $user = User::find()->alias('u')->addSelect(['id', 'username', 'name', 'address', 'email'])
          ->joinWith(['userPhoto' => function($q) {
              $q->addSelect(['photo_path', 'photos_id']);
            }], true, 'LEFT JOIN')
            ->joinWith(['userAge' => function($q) {

              }], true, 'LEFT JOIN')
            ->joinWith(['userGender' => function($q) {

              }], true, 'LEFT JOIN')
            ->joinWith(['userLike' => function($q) {
                $q->andWhere(['uL.review' => REL_USER_REVIEW_LIKE, 'uL.createdby' => Yii::$app->user->getId()]);
              }], true, 'LEFT JOIN')
            ->andWhere(['!=','u.is_active', User::$IN_ACTIVE])
        ->asArray()->all();
              
     
      /***** Blocked List *************/
        $exclude = [];  
          $blockedUsers = BlockList::find()->alias('bl')
          ->addSelect('blocked_id')
          ->andWhere(['blocked_by_id' => Yii::$app->user->getId(), 'is_blocked' => REL_USER_BLOCK])
          ->asArray()->all();
          if (!empty($blockedUsers) || !$blockedUsers) {
            $exclude = array_merge($exclude, array_column($blockedUsers, 'blocked_id'));
          }
      /****** Blocked List ************/

          $dataArr = [];
          if (!empty($user)) {
            foreach ($user as $key => $value) {
              if(!in_array($value['id'], $exclude))
              {
                $dataArr[] = $value;
                $dataArr[$key]['user_meta']['age'] = !empty($value['userAge']['age']) ? $value['userAge']['age'] : null;
                $dataArr[$key]['user_meta']['gender'] = !empty($value['userGender']['meta_value']) ? $value['userGender']['meta_value'] : null;
                $dataArr[$key]['user_meta']['like'] = !empty($value['userLike']['like']) ? (int) $value['userLike']['like'] : 0;
                unset($dataArr[$key]['userGender']);
                unset($dataArr[$key]['userAge']);
                unset($dataArr[$key]['userLike']);    
              }
            }
          }
      if (empty($user)) {
        return $this->success(['is_success' => true, 'message' => "No user found"]);
      } else {
        return $this->success(['is_success' => true, 'user' => $dataArr]);
      }
    }
  }

            // get user video
  public function actionVideo() {
    $this->initHeader();
    if (Yii::$app->request->isGet) {
      //$user = [];
      $user = User::find()->addSelect(['u.id'])
          ->joinWith(['userVideo uV' => function($q) {
              $q->addSelect(['uV.id', 'uV.video_url']);
            }], true, 'LEFT JOIN')
            ->andWhere(['u.id' => \Yii::$app->user->getId()])
            ->alias('u')
            ->asArray()->one();

        if (empty($user)) {
          return $this->error(['error' => "not video found"]);
        } else {
          return $this->success(['is_success' => true, 'video' => $user]);
        }
      }
  }

              // user profile update api
  public function actionProfileUpdate() {
    $this->initHeader();
    if (Yii::$app->request->isPost) {
      if (isset(Yii::$app->request->post()['UserMeta']) && !empty(Yii::$app->request->post()['UserMeta'])) {
        foreach (User::userMetaKeys() as $key) {
          $model = UserMeta::find()->andWhere(['user_id' => Yii::$app->user->getId(), 'meta_key' => $key])->one();
          if ($model) {
            if (!isset(Yii::$app->request->post()['UserMeta'][$key]) || empty(Yii::$app->request->post()['UserMeta'][$key])) {
              $model->delete();
              continue;
            } else {
              
              $model->meta_value = Yii::$app->request->post()['UserMeta'][$key];
              if($key == REL_USER_GENDER && Yii::$app->request->post()['UserMeta'][$key] == 'Woman')
              {
                  $model->meta_value = 'Women';
              }
              
              if($key == REL_USER_GENDER && Yii::$app->request->post()['UserMeta'][$key] == 'Man')
              {
                  $model->meta_value = 'Men';
              }
              $model->save();
              continue;
            }
          } else {
            if (isset(Yii::$app->request->post()['UserMeta'][$key]) || !empty(Yii::$app->request->post()['UserMeta'][$key])) {
              $model = new UserMeta();
              $model->meta_key = $key;
              $model->user_id = Yii::$app->user->getId();
              $model->meta_value = Yii::$app->request->post()['UserMeta'][$key];
              $model->meta_value = Yii::$app->request->post()['UserMeta'][$key];
              
              if($key == REL_USER_GENDER && Yii::$app->request->post()['UserMeta'][$key] == 'Woman')
              {
                  $model->meta_value = 'Women';
              }
              
              if($key == REL_USER_GENDER && Yii::$app->request->post()['UserMeta'][$key] == 'Man')
              {
                  $model->meta_value = 'Men';
              }
              $model->save();
            }
          }
        }
      } 

      if (isset(Yii::$app->request->post()['User']) && !empty(Yii::$app->request->post()['User'])) {
        $user = User::find()->andWhere(['id' => Yii::$app->user->getId()])->one();
        $user->name = Yii::$app->request->post()['User']['name'];
        if(isset(Yii::$app->request->post()['User']['address']))
        {
          $user->address = Yii::$app->request->post()['User']['address'];
        }
        if(isset(Yii::$app->request->post()['User']['latitude']))
        {
          $user->latitude = Yii::$app->request->post()['User']['latitude'];
        }
        if(isset(Yii::$app->request->post()['User']['longitude']))
        {
          $user->longitude = Yii::$app->request->post()['User']['longitude'];
        }
        $user->validate();
        $user->save();
      }

      // delete photos from photosmap
      if (isset(Yii::$app->request->post()['User']['photo_id'])) {
        PhotosMap::deleteAll(['photos_id' => Yii::$app->request->post()['User']['photo_id']]);
        Photos::deleteAll(['photos_id' => Yii::$app->request->post()['User']['photo_id']]);
      }

      $imageFile = \yii\web\UploadedFile::getInstancesByName('User[user_photo]');
      if (!empty($imageFile)) { 

        $imageFilesData = [];
        $imageFile = \yii\web\UploadedFile::getInstancesByName('User[user_photo]');
        if ($imageFile) {
          $formSingle = new \api\modules\v1\models\ImageUploadForm();
          $formSingle->temp_images = $imageFile;
          if ($formSingle->upload('_user')) {
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
            $photo_map = PhotosMap::find()->andWhere(['item_id' => Yii::$app->user->getId(), 'relationship' => REL_USER_PROFILE])->all();
            $fileDateArray = $this->array_column($formSingle->response, 'savedName');
            $photoUploader = new \common\helpers\PhotoUploader(AssestsManager::PHOTO_DIR_USER);
            $photoUploader->entity = Yii::$app->user->getIdentity();
            $photoUploader->relationship = REL_USER_PROFILE;
            $photoUploader->upload($fileDateArray);

            $photo_map = PhotosMap::find()->andWhere(['item_id' => Yii::$app->user->getId()])->one();
            $photo = Photos::find()->andWhere(['photos_id' => $photo_map->photos_id])->one();
          }
        }
      }
      return $this->success(['is_success' => true, 'message' => 'profile updated!']);
    }
  }

  
  public function actionSettings() {
    $this->initHeader();
    if (Yii::$app->request->isPost) {
      if(isset(Yii::$app->request->post()['User']['action']) && !empty(Yii::$app->request->post()['User']['action'])) {
          switch (Yii::$app->request->post()['User']['action']) {
              case "delete":
              $device_token = Notification::find()->andWhere(['user_id' => Yii::$app->user->getId()])->all();
                if(!empty($device_token)) {
                     foreach ($device_token as $key => $value) {
                       $option  = [];
                       $user = User::find()->andWhere(['id' => Yii::$app->user->getId()])->one();
                       $username = !empty($user->username) ? $user->email : '';
                       $name = !empty($user->name) ? $user->name : '';                    
                       if($value->device_type=="iOS")
                       {
                         $notification_message = $this->processMessage('message_recieved',['USERNAME' => $username,'NAME' => $name ]);
                         $this->sendPush($value->device_token, $notification_message, $title = "chat", $option);  
                       } 
                       else
                       { 
                           $deviceToken = $value->device_token;                        
                           $notification_message = $this->processMessage('message_recieved',['USERNAME' => $username,'NAME' => $name  ]);
                           CommonHelper::sendPushAndroid($value->device_token,$notification_message,$title = "chat",$option);
                       }

                     }
                 }    
              $this->deleteUserData(\Yii::$app->user->getId());
              return $this->success(['is_success' => true, 'message' => 'profile deleted!']);    
              break;
              case "deactivate":
              $model = User::find()->andWhere(['id' => Yii::$app->user->getId()])->one();    
              $model->is_active = User::$IN_ACTIVE;
              $model->save();
              return $this->success(['is_success' => true, 'message' => 'profile deactivated!']);        
              break;
              case "activate":
              $model = User::find()->andWhere(['id' => Yii::$app->user->getId()])->one();    
              if(empty($model))
              {
                 return $this->error(['is_success' => false, 'message' => 'user not found']);
              }
              else
              {
                  $model->is_active = User::$IS_ACTIVE;
                  $model->save();
                  return $this->success(['is_success' => true, 'message' => 'profile activated!']);          
              }
              break;
          }
      }
    }
  }
  
  private function deleteUserData($user_id)
  {
        $user_photos = '';
        $user_photos = PhotosMap::find()->andWhere(['item_id' => \Yii::$app->user->getId()])->all();    
        if(!empty($user_photos))
        {
            $userPhotos = '';
            // delete all photos and folder of user
            $files = glob(\Yii::getAlias('@uploads') . '/user/' . Yii::$app->user->getId().'/*'); // get all file names
            if(!empty($files))
            {
                foreach($files as $file){ 
                    if(is_file($file))
                      unlink($file);
                }
            }
            rmdir(\Yii::getAlias('@uploads') . '/user/' . Yii::$app->user->getId());
            foreach ($user_photos as $key => $value) {
                // delete user records
                PhotosMap::deleteAll(['photos_id' => $value->photos_id]);
                Photos::deleteAll(['photos_id' => $value->photos_id]);
            }
        }
        Notification::deleteAll(['user_id' => Yii::$app->user->getId()]);
        Ratings::deleteAll(['user_id' => Yii::$app->user->getId()]);
        Subscription::deleteAll(['user_id' => Yii::$app->user->getId()]);
        UserMeta::deleteAll(['user_id' => Yii::$app->user->getId()]);
        UserPreference::deleteAll(['user_id' => Yii::$app->user->getId()]);
        Videos::deleteAll(['createdby' => Yii::$app->user->getId()]);
        VideoMap::deleteAll(['user_id' => Yii::$app->user->getId()]);
        User::deleteAll(['id' => Yii::$app->user->getId()]);
  }
  
  // user like/dislike
  public function actionReview() {
    $this->initHeader();
    if (Yii::$app->request->isPost) {
      if (isset(Yii::$app->request->post()['User']['review'])) {
        $model = Ratings::find()->andWhere(['user_id' => Yii::$app->request->post()['User']['user_id'], 'createdby' => Yii::$app->user->getId()])->one();
        if (empty($model)) {
          $model = new Ratings();
          $model->user_id = Yii::$app->request->post()['User']['user_id'];
        }
        $model->review = (Yii::$app->request->post()['User']['review'] == REL_USER_REVIEW_LIKE ? REL_USER_REVIEW_LIKE : REL_USER_REVIEW_DISLIKE);
        $model->is_active = Ratings::$IS_ACTIVE;
        if (!$model->save()) {
          return $this->error(['error' => $model->getErrors()]);
        }
        return $this->success(['is_success' => true, 'message' => 'Review added successfully']);
      }
      return $this->error(['error' => 'Review rating is invalid.']);
    }
    return $this->error(['error' => 'Request payload is empty']);
  }
  
   private function processMessage($msgKey = '', $array = []){
        $string = Yii::$app->params['message_templates'][$msgKey]['text'];
        foreach($array as $key => $value){
            $string = str_replace('{'.strtoupper($key).'}', $value, $string);
        }
        return $string;
   }

   private function sendPush($deviceToken, $message = "", $title = ""){ 
          return CommonHelper::sendPush($deviceToken, $message, $title, $this->debug_environ);
   }

  public function actionUnblock() {
    $this->initHeader();
    $response = [];
    if (Yii::$app->request->isPost) {
      if (isset(Yii::$app->request->post()['User']['user_id'])) {
        if (is_array(Yii::$app->request->post('User')['user_id'])) {
          foreach (Yii::$app->request->post('User')['user_id'] as $key => $val) {
            if ($val == Yii::$app->user->getId()) {
              $response[$val] = "Blocked Id and User Id cannot be same";
            }
            $response[$val] = $this->unblockUser($val);
          }
          return $this->success(['is_success' => true, 'response' => $response]);
        } else {
          $response = $this->unblockUser(Yii::$app->request->post('User')['user_id']);
          if (isset($response['is_success'])) {
            return $this->success($response);
          } else {
            return $this->error($response);
          }
        }
      }
      return $this->error(['error' => "User Id is required."]);
    }
  }

  // user block/unblock
  public function actionBlock() {
    $this->initHeader();
    if (Yii::$app->request->isPost) {
      if (isset(Yii::$app->request->post()['User']['user_id'])) {
        if (Yii::$app->request->post()['User']['user_id'] == Yii::$app->user->getId()) {
          return $this->error(['error' => "Blocked Id and User Id cannot be same"]);
        }
        $blockedUser = $this->getUser(false, Yii::$app->request->post()['User']['user_id']);
        $model = $this->getBlockModel();
        $model->block();
        if (!$model->save()) {
          return $this->error(['error' => "Something went wrong. Please try again later."]);
        }
        return $this->success(['is_success' => true, 'message' => (is_null($blockedUser['name']) ? "User" : $blockedUser['name']) . " is blocked."]);
      }
      return $this->error(['error' => "User Id is required."]);
    }
  }

  private function getBlockModel() {
    $model = Blocklist::find()->andWhere(['blocked_id' => Yii::$app->request->post()['User']['user_id'], 'blocked_by_id' => Yii::$app->user->getId()])->one();
    if (empty($model) || !$model) {
      $model = new Blocklist();
      $model->blocked_id = Yii::$app->request->post()['User']['user_id'];
      $model->blocked_by_id = Yii::$app->user->getId();
    }
    return $model;
  }
  
  public function fetchUser($id = false) {
    if (!$id) {
      return false;
    } 
    $user = User::find()->alias('uS')->addSelect(['uS.id', 'username', 'name', 'address','uS.is_active', 'email'])
        ->andWhere(['uS.id' => $id])
        ->joinWith(['userPhoto' => function($q) {
          $q->addSelect(['photos_id', 'photo_title', 'photo_path']);
          }], true, 'LEFT JOIN')
        ->joinWith(['userVideo uV' => function($q) {
            $q->addSelect(['uV.id', 'video_url', 'video_title']);
          }], true, 'LEFT JOIN')
        ->joinWith(['userVideoThumb uVTs' => function($q) {$q->addSelect(['uVTs.photos_id', 'uVTs.photo_path']);}], true, 'LEFT JOIN')                    
        ->asArray()->one();
    return $useData = array_merge($user, User::loadUserData($id));
  }

  public function unblockUser($val = false) {
    $model = Blocklist::find()->andWhere(['blocked_id' => $val, 'blocked_by_id' => Yii::$app->user->getId()])->one();
    if (!$model) {
      return ['error' => "Something went wrong. Please try again later."];
    }
    $blockedUser = $this->getUser(false, $val);
    $model->unblock();
    if (!$model->save()) {
      return ['error' => "Something went wrong. Please try again later."];
    }
    return ['is_success' => true, 'message' => (is_null($blockedUser['name']) ? "User" : $blockedUser['name']) . " is unblocked."];
  }  

  public function getUser($social_media_type = false, $id = false) {
    switch ($social_media_type) {
      case 'facebook':
        $user = User::find()->where(['social_media_type' => $social_media_type, 'social_id' => Yii::$app->request->post()['User']['social_id']])->asArray()->one();        
          if (!$user) {
          if ($user = $this->signup(true)) {
            $user = User::find()->where(['id' => $user->id])->asArray()->one();
          } elseif ($this->hasErrors && !is_null($this->errors)) {
            return false;
          } else {

            throw new \Exception('Something went wrong. Please try again later');
          }
        }
        break;
      case 'google':
        $user = User::find()->where(['social_media_type' => $social_media_type, 'social_id' => Yii::$app->request->post()['User']['social_id']])->asArray()->one();
        if (!$user) {
          if ($user = $this->signup(true)) {
            $user = User::find()->where(['id' => $user->id])->asArray()->one();
          } elseif ($this->hasErrors && !is_null($this->errors)) {
            return false;
          } else {
            throw new \Exception('Something went wrong. Please try again later');
          }
        }
        break;
      default:
        if ($id) {
          $user = User::find()->where(['id' => $id])->asArray()->one();
        } else {
          $user = User::find()->where(['email' => Yii::$app->request->post()['User']['username']])->orWhere(['username' => Yii::$app->request->post()['User']['username']])->asArray()->one();
        }
        break;
    }
    return $user;
  }

  public function array_column($array, $key) {
    return array_column($array, $key);
  }
    
  public function signup($returnRaw = false) {
    $user = new \common\models\User();
    $user->load(\Yii::$app->request->post());
    if (!isset(\Yii::$app->request->post('User')['name']) && empty(\Yii::$app->request->post('User')['name'])) {
       $user->name = Yii::$app->request->post('User')['username'];
    }

    if(isset(\Yii::$app->request->post('User')['social_media_type']) && \Yii::$app->request->post('User')['social_media_type']!="")
    {
      if(!isset(\Yii::$app->request->post('User')['password']) && empty(\Yii::$app->request->post('User')['password']))
      {
        $password="123";
        $user->setPassword($password);
        $user->email=\Yii::$app->request->post('User')['email'];  
      }
    }
    else
    {
      $user->setPassword(\Yii::$app->request->post('User')['password']);
    }
    
    $user->validate();
    if (!$user->validate()) {
      $this->hasErrors = true;
      $this->errors = $user->getErrors();
      
      return false;
    }
    $user->is_active = User::$IS_ACTIVE;
    if (!$user->save()) {
      if ($returnRaw) {
        return false;
      }
      return $this->error(['error' => $user->getErrors()]);
    } else {
      if ($returnRaw) {
        return $user;
      }
      // update device token
      $device = Notification::find()->andWhere(['user_id' => $user['id'],'device_token' => Yii::$app->request->post()['User']['device_token']])->one();
      if(empty($device))
      {
          $device = new Notification();
          $device->user_id = $user['id'];
          $device->device_type = !empty(Yii::$app->request->post()['User']['device_type']) ? Yii::$app->request->post()['User']['device_type']:'1';
          $device->device_token = !empty(Yii::$app->request->post()['User']['device_token']) ? Yii::$app->request->post()['User']['device_token']:'1';
          $device->save();
      }
      else
      {
          $device = Notification::find()->andWhere(['user_id' => $user['id'],'device_token' => Yii::$app->request->post()['User']['device_token']])->one();
          $device->user_id = $user['id'];
          $device->device_type = !empty(Yii::$app->request->post()['User']['device_type']) ? Yii::$app->request->post()['User']['device_type']:'1';
          $device->device_token = !empty(Yii::$app->request->post()['User']['device_token']) ? Yii::$app->request->post()['User']['device_token']:'1';
          $device->save();
      }
      if($user->id!="")
      {
        $user->id=strval($user->id);
      }
      
      $subs = new Subscription();
      $subs->id = $user['id'];
      $subs->user_id = $user['id'];
      $subs->save();
      
      return $this->success(['is_success' => true, 'user' => $user]);
    }
  } 

  public function  actionSubscription()
  {
    $this->initHeader();
    
    if (Yii::$app->request->isPost) {
      $user = User::find()
              ->where(['id'=>Yii::$app->user->getId()])
              ->asArray()
              ->one();
      // c(Yii::$app->request->post()); die;
      // $price=Yii::$app->request->post()['User']['amount']*100;
      if(Yii::$app->request->post()['User']['months'] == 1)
      {
        $plan="1Month";
      } 
      else
      {
        $plan=Yii::$app->request->post()['User']['months']."Months";
      }  
      
      
        // \Stripe\Stripe::setApiKey("sk_test_9dOkBu1CZEqHhTm6Rx7aZgva");
        \Stripe\Stripe::setApiKey(Yii::$app->params['stripeLivePrivateKey']);
      
      try {
        
        $customer = \Stripe\Customer::create(array(
          "description" => "Customer email ".$user['email'],
          "email" => $user['email'],
          "source" => Yii::$app->request->post()['User']['token'] // obtained with Stripe.js
        )); 

        $subscription = \Stripe\Subscription::create(array(
          "customer" => $customer->id,
          "items" => array(
            array(
              "plan" => $plan,
            ),
          )
        ));
        // c($customer); die;
        // $charge = \Stripe\Charge::create(array(
        //   "amount" => $price,
        //   "currency" => "usd",
        //   "source" => Yii::$app->request->post()['User']['token'], // obtained with Stripe.js
        //   "description" => "Create Subsription"
        // ));
      }catch(\Stripe\Error\Card $e) {
        return $this->error(['error' => $e]);
      } catch (\Stripe\Error\RateLimit $e) {
        return $this->error(['error' => $e]); 
      } catch (\Stripe\Error\InvalidRequest $e) {

        return $this->error(['error' => $e]);
      } catch (\Stripe\Error\Authentication $e) {
        return $this->error(['error' => $e]);
        
      } catch (\Stripe\Error\ApiConnection $e) {
        return $this->error(['error' => $e]);
      } catch (\Stripe\Error\Base $e) {
        return $this->error(['error' => $e]);
      } catch (Exception $e) {
        return $this->error(['error' => $e]);
      }
        //die("hello");
      $model = new Subscription(); 
      $model->user_id = Yii::$app->user->getId();
      // $model->charge_id = $charge->id;
      // $model->charge_id = "test";
      // $model->transaction_id = $charge->balance_transaction;
      $model->customer_id = $customer->id;
      $model->subscription_id = $subscription->id;
      // $model->transaction_id = "transid";
      // $model->amount   = Yii::$app->request->post()['User']['amount'];
      $model->months   = Yii::$app->request->post()['User']['months'];

      if(isset($user['email']) && !empty($user['email']))
      {
        VeedaterEmail::send($user['email'], $plan.' Subscription', ["html" => "subscription-html", "plan" => $plan]);
      }
      
      $dataArr  = '';
      
      
      if (!$model->save()) {
        return $this->error(['error' => $model->getErrors()]);
      } else {
        $dataArr['user_id'] = (string)$model->user_id;  
        $dataArr['id'] = (string)$model->id;  
        $dataArr['customer_id'] = (string)$model->customer_id;  
        $dataArr['subscription_id'] = (string)$model->subscription_id;  
        $dataArr['months'] = (string)$model->months; 
        return $this->success(['is_success' => true, 'id' => (string)$model->id, 'subscription'=>$dataArr]);
      }
      // c($model); die;
    }
  }


  public function actionPreferences()
  {
    $this->initHeader();
    if (Yii::$app->request->isGet) {
        $dataArr = [];
        $preference = UserPreference::find()->andWhere(['user_id' => \Yii::$app->user->getId()])->one();
        if($preference)
        {
            foreach ($preference as $key => $value) {
                $dataArr[$key] = "".$value."";
            }
        }
        
        return $this->success(['is_success' => true ,  'user' => !empty($dataArr) ? $dataArr:null]);
        
    }
    if (Yii::$app->request->isPost) {
        $user = Yii::$app->request->post()['User'];
        $preference = UserPreference::find()->andWhere(['user_id' => \Yii::$app->user->getId()])->one();
        if(empty($preference))
        {
          $preference = new UserPreference();
          $preference->user_id = Yii::$app->user->getId();
          $preference->gender = $user['gender'];
          $preference->min_age = $user['min_age'];
          $preference->max_age = $user['max_age'];
          $preference->distance = $user['distance'];
          $preference->religion = $user['religion'];
          $preference->sports = $user['sports'];
          $preference->min_income = $user['min_income'];
          $preference->max_income = $user['max_income'];
          $preference->style = $user['style'];
          $preference->alchohol = $user['alchohol'];
          $preference->smoke = $user['smoke'];
          $preference->tatoo = $user['tatoo'];
          $preference->save();
        }
        else
        {
          $preference->user_id = Yii::$app->user->getId();
          $preference->gender = $user['gender'];
          $preference->min_age = $user['min_age'];
          $preference->max_age = $user['max_age'];
          $preference->distance = $user['distance'];
          $preference->religion = $user['religion'];
          $preference->sports = $user['sports'];
          $preference->min_income = $user['min_income'];
          $preference->max_income = $user['max_income'];
          $preference->style = $user['style'];
          $preference->alchohol = $user['alchohol'];
          $preference->smoke = $user['smoke'];
          $preference->tatoo = $user['tatoo'];
          $preference->save();   
        }

        // array_merge($user, User::loadUserData($user['id'])
        $userIds=[];
        $users = [];
//        foreach($preference as $key => $value)
//        {
//          // c($preference->min_age); die;
//          if($key=="min_age" || $key=="max_age")
//          {
//              $checkusers = UserMeta::find()
//                       ->andWhere(['meta_key'=>'age'])
//                       ->andwhere(['>=', 'meta_value', $preference->min_age])
//                       ->andWhere(['<=', 'meta_value', $preference->max_age])->all();
//                foreach($checkusers as $checkUser)
//                {
//                  if(!in_array($checkUser['user_id'], $userIds))
//                  {
//                    $userIds[]=$checkUser['user_id'];
//                    $userInfo = User::find()->andWhere(['id'=>$checkUser['user_id']])->asArray()->one();
//                    $users[]=array_merge($userInfo, User::loadUserData($userInfo['id']));
//                  }
//                }
//          }
//          else if($key=="min_income" || $key=="max_income")
//          {
//              $checkusers = UserMeta::find()
//                       ->andWhere(['meta_key'=> REL_USER_INCOME])->all();
//                foreach($checkusers as $checkUser)
//                {
//                  if(!in_array($checkUser['user_id'], $userIds))
//                  {
//                    if($checkUser->meta_value<=$preference->max_income && $checkUser->meta_value>=$preference->min_income)
//                    {
//                      $userIds[]=$checkUser['user_id'];
//                      $userInfo = User::find()->andWhere(['id'=>$checkUser['user_id']])->asArray()->one();
//                      $users[]=array_merge($userInfo, User::loadUserData($userInfo['id']));  
//                    }
//                    
//                  }
//                }
//          }
//           else if($key=="distance")
//           { 
//               // get lat long of current user and calculate distance between other users
//               $latitude = '';
//               $longitude = '';
//               
//               $startDistance = 0;
//               $endDistance = 5;
//               if(!empty($preference->distance))
//               {
//                   $endDistance = $preference->distance;
//               }
//               
//               $currentUser = User::find()
//                       ->addSelect(['latitude','longitude'])
//                       ->andWhere(['id' => \Yii::$app->user->getId()])
//                       ->one();
//               $latitude = $currentUser->latitude;
//               $longitude = $currentUser->longitude;
//               if(!empty($latitude) &&  !empty($longitude))
//               {
//                   $checkusers = User::find()
//                        ->addSelect(['ROUND(LAT_LNG_DISTANCE(latitude, longitude, '.$latitude.', '.$longitude.'),0) as distance'])
//                        ->addSelect(['latitude', 'longitude','id','username'])
//                        ->having('distance between ' . $startDistance . ' and ' . $endDistance)   
//                        ->asArray()   
//                        ->all();
//                      foreach($checkusers as $checkUser)
//                      {
//                        if(!in_array($checkUser['id'], $userIds))
//                        {
//                            $userIds[]=$checkUser['id'];
//                            $userInfo = User::find()->andWhere(['id'=>$checkUser['id']])->asArray()->one();
//                            $users[]=array_merge($userInfo, User::loadUserData($userInfo['id']));  
//                        }
//                      }
//               }
//               
//           }
//          else
//          {
//               $checkusers = UserMeta::find()
//                       ->andWhere(['meta_key'=>$key, 'meta_value'=>$value])->all();
//                foreach($checkusers as $checkUser)
//                {
//                  if(!in_array($checkUser['user_id'], $userIds))
//                  {
//                    $userIds[]=$checkUser['user_id'];
//                    $userInfo = User::find()->andWhere(['id'=>$checkUser['user_id']])->asArray()->one();
//                    $users[]=array_merge($userInfo, User::loadUserData($userInfo['id']));
//                  }
//                }
//          }
//         
//        }

        //return $this->success(['is_success' => true, 'users' => $users]);
        return $this->success(['is_success' => true ,  'message' => 'success']);

        // c(json_encode($users)); 
        
    }
  }

  public function actionLogout() {
        $this->initHeader();
        if (Yii::$app->request->isPost) {
              if(!empty(Yii::$app->request->post()['User']))
              {
                $model = Notification::find()->andWhere(['user_id' => \Yii::$app->user->getId(),'device_token' => Yii::$app->request->post()['User']['device_token'],'device_type' => Yii::$app->request->post()['User']['device_type']])->one();              
                if(!empty($model))
                {
                  $model->delete();
                }
                return $this->success(['is_success' => true, 'message' => 'user logout']);      
              }
              else
              {
                  return $this->success(['is_success' => true, 'message' => 'user logout']);      
              }
        }
        
    }
}
            
