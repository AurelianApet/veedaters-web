<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\helpers\CommonHelper;
use \backend\models\Order;
use \common\models\Notification;
use \common\models\TransactionRecords;
use yii\helpers\Url;
use \common\models\PhotosMap;
use \common\models\Photos;
use common\helpers\AssestsManager;
use \common\models\Subscription;
use \common\helpers\VeedaterEmail;
use paragraph1\phpFCM\Recipient\Device;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    
    public $debug_environ = 'production';
    public $errors = null;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // c($dataProvider); die;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
    
    
    // search user
    public function actionSearchUser()
    {
        if(Yii::$app->request->post()['username'])
        {
            $model = \common\models\User::find()
                    ->andWhere(['like','username',Yii::$app->request->post()['username'].'%',false])
                    ->all();
            if(!empty($model))
            {
                echo "<ul id=user-list>";
                foreach ($model as $key => $value) {                    
                    echo "<li onClick=selectUser('". str_replace(' ','-', $value->username)."',".$value->id.")>".$value->username."</li>";
                }
                echo "</ul>";
            }
            else
            {
                echo "<ul id=user-list>No user found";
                echo "</ul>";
            }
        }
        
    }
    
    
    // refund
    public function actionUserRefund()
    {
        if(Yii::$app->request->post()['user_id'])
        {
            // get order charge ID from local db
            $transaction = TransactionRecords::find()->andWhere(['user_id' => Yii::$app->request->post()['user_id']])->one();            
            if(!empty($transaction))
            {
                \Stripe\Stripe::setApiKey("sk_live_ZFlrGhTD7m4gpaMlbNMzn5L3");
                \Stripe\Refund::create(array(
                    "charge" => $transaction->charge_id,
                ));
            }
            else
            {
                return "no refund";
            }
        }
        
    }
    
    
    // search order
    public function actionUserOrders()
    {
        if(Yii::$app->request->post()['user_id'] && Yii::$app->request->post()['action'] == 'user_orders')
        {
            $model = Order::find()
                    ->andWhere(['user_id' => Yii::$app->request->post()['user_id']])
                    ->all();
            if(!empty($model))
            { 
                echo "<select id=user-list class=form-control>";
                foreach ($model as $key => $value) {                    
                    echo "<option>".$value->order_id."</option>";
                }
                echo "</select>";
            }
            else
            {
                echo "<select id=order-list class=form-control>";
                echo "<option>No order found</option>";
                echo "</select>";
            }
        }
        
    }
    
    public function actionPhotoDelete()
    {
        if(Yii::$app->request->post()['User']['user_id'] && Yii::$app->request->post()['User']['action'] == 'delete')
        {
            $photosMap = PhotosMap::find()
                    ->andWhere(['item_id' => Yii::$app->request->post()['User']['user_id']])
                    ->andWhere(['relationship' => REL_USER_PROFILE])
                    ->andWhere(['photos_id' => Yii::$app->request->post()['User']['photo_id']])
                    ->one();
            $photos = Photos::find()
                    ->andWhere(['photos_id' => Yii::$app->request->post()['User']['photo_id']])
                    ->one();
            // delete photo from folder
            if(file_exists(Yii::$app->basePath.AssestsManager::UPLOAD_PATH.$photos->photo_path))
            {
                unlink(Yii::$app->basePath.AssestsManager::UPLOAD_PATH.$photos->photo_path);
            }
            $photosMap->delete();
            $photos->delete();
        }
    }
    
    public function  actionSubscription()
    {
        
        if (Yii::$app->request->isPost) {
          $user = User::find()
                  ->where(['id'=>Yii::$app->request->post()['User']['user_id']])
                  ->asArray()
                  ->one();
          
          $months = '';
          $planStr = explode('-', Yii::$app->request->post()['User']['plan']);
          $plan = $planStr[0];
          $months = $planStr[1];
          
          // \Stripe\Stripe::setApiKey("sk_test_9dOkBu1CZEqHhTm6Rx7aZgva");
          \Stripe\Stripe::setApiKey(Yii::$app->params['stripeTestPrivateKey']);
          
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
            
          }catch(\Stripe\Error\Card $e) {
            c($e);
          } catch (\Stripe\Error\RateLimit $e) {
            c($e);
          } catch (\Stripe\Error\InvalidRequest $e) {
            c($e);
          } catch (\Stripe\Error\Authentication $e) {
            c($e);
          } catch (\Stripe\Error\ApiConnection $e) {
            c($e);
          } catch (\Stripe\Error\Base $e) {
            c($e);
          } catch (Exception $e) {
            c($e);
          }
          
          $model = Subscription::find()->andWhere(['user_id' => Yii::$app->request->post()['User']['user_id']])->one(); 
          if(empty($model))
          {
              $model = new Subscription(); 
              $model->user_id = Yii::$app->request->post()['User']['user_id'];
              $model->customer_id = $customer->id;
              $model->subscription_id = $subscription->id;
              $model->months   = $months;
          }
          else
          {
              $model->user_id = Yii::$app->request->post()['User']['user_id'];
              $model->customer_id = $customer->id;
              $model->subscription_id = $subscription->id;
              $model->months   = $months;
          }
          
          $model->save();
          
          if(isset($user['email']) && !empty($user['email']))
          {
            VeedaterEmail::send($user['email'], $plan.' Subscription', ["html" => "subscription-html", "plan" => $plan]);
          }
        }
    }
    

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        
        // get stripe plans
        // \Stripe\Stripe::setApiKey("sk_test_9dOkBu1CZEqHhTm6Rx7aZgva");
        \Stripe\Stripe::setApiKey("sk_test_ha1yafp3BUbBzmuc1u4jqTCJ");
        $plans = '';
        $plans = \Stripe\Plan::all(array("limit" => 3));
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'stripePlans' => $plans,
        ]);
    }
    
    public function actionRefund($id)
    {
        return $this->render('refund', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model != false)
        {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        else
        {
            return $this->render('update', [
                    'model' => $model,
                ]);
        }
    }
    
    public function actionUserUpdate($id)
    { 
        $model = $this->findModel($id);
        $model->validate();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            // send push notification to user
//            $this->debug_environ = 'developer';
//            $device_token = Notification::find()->andWhere(['user_id' => $id])->all();
//            if(!empty($device_token)) {
//                    foreach ($device_token as $key => $value) {
//                        
//                        $notification_message = $this->processMessage('account_blocked');
//                        $this->sendPush($value->device_token, $notification_message, $title = "block");
//                    }
//            }
            return json_encode(['status' => $model->is_active,'msg' => 'Status changed successfully!']);
        } else {
            return json_encode(['status' => false,'msg' => 'Status not changed']);
        }
        
    }
    
    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        
        
        $device_token = Notification::find()->andWhere(['user_id' => $id])->all();
        //c($device_token); die;     
             if(!empty($device_token)) {
                  foreach ($device_token as $key => $value) {
                    // get sender name 
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
        $this->findModel($id)->delete();      
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            return false;
        }
    }
}
