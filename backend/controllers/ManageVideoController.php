<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ShopSearch;
use common\models\Shop;
use common\models\UserMeta;
use common\models\Notification;
use common\helpers\CommonHelper;
use common\models\ShopMeta;
use common\models\Order;
use common\models\PhotosMap;
use common\models\Photos;
use common\models\Product;
use common\models\ProductAvailability;
use common\models\ProductDelivery;
use common\models\ShopDelivery;
use common\models\ShopAvailability;



/**
 * ManageVideoController implements the CRUD actions for Shop model.
 */
class ManageVideoController extends BaseController
{
    public $debug_environ = 'developer';
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
     * Lists all Shop models.
     * @return mixed
     */
    
    private function processMessage($msgKey = '', $array = []){
        $string = Yii::$app->params['message_templates'][$msgKey]['text'];
        foreach($array as $key => $value){
            $string = str_replace('{'.strtoupper($key).'}', $value, $string);
        }
        return $string;
    }

    private function sendPush($deviceToken,$deviceType,$message = "", $title = ""){ 
        // badge count for push notification
        $badgeCount = Notification::find()
          ->andWhere(['device_token' => $deviceToken])->one();

        $badgeCount->badge_count = $badgeCount->badge_count + 1;
        $badgeCount->save();  
        return CommonHelper::sendPush($deviceToken,$deviceType,$message, $title, $this->debug_environ);
    }
    
    public function actionIndex()
    {
        return $this->render('/manage-video/index');
    }
    
    /**
     * Displays a single Shop model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Shop();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->shop_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    // search shop 
    public function actionSearchShop()
    {
        if(Yii::$app->request->post()['shop_title'])
        {
            $shop = Shop::find()
                    ->andWhere(['like','shop_title',Yii::$app->request->post()['shop_title']])
                    ->all();
            if(!empty($shop))
            {
                echo "<ul id=shop-list>";
                foreach ($shop as $key => $value) {
                    
                    echo "<li onClick=selectShop('". str_replace(' ','-', $value->shop_title)."',".$value->shop_id.")>".$value->shop_title."</li>";
                }
                echo "</ul>";
            }
            else
            {
                echo "<ul id=shop-list>No shop found";
                echo "</ul>";
            }
            
        }        
    }
    
    
    // featured shop
    public function actionFeaturedShop()
    {
        if(Yii::$app->request->post()['shop_id'] && Yii::$app->request->post()['status'] == 'add')
        {
            $shop_status = ShopMeta::find()->andWhere(['shop_id' => Yii::$app->request->post()['shop_id']])->one();
            if(empty($shop_status))
            { 
                $model = new ShopMeta();
                $model->shop_id = Yii::$app->request->post()['shop_id'];
                $model->meta_key = REL_SHOP_META_FEATURED;
                $model->meta_value = 'true';
                $model->save();
            }
            else
            {
                $model = ShopMeta::find()->andWhere(['shop_id' => Yii::$app->request->post()['shop_id']])->one();
                $model->shop_id = Yii::$app->request->post()['shop_id'];
                $model->meta_key = REL_SHOP_META_FEATURED;
                $model->meta_value = 'true';
                $model->save();
            }
            return json_encode(['status' => true,'msg' => 'Status changed']);
        }
        if(Yii::$app->request->post()['shop_id'] && Yii::$app->request->post()['status'] == 'remove')
        {
            $shop_status = ShopMeta::find()->andWhere(['shop_id' => Yii::$app->request->post()['shop_id']])->one();
            if(empty($shop_status))
            {
                $model = new ShopMeta();
                $model->shop_id = Yii::$app->request->post()['shop_id'];
                $model->meta_key = REL_SHOP_META_FEATURED;
                $model->meta_value = 'false';
                $model->save();
            }
            else
            {
                $model = ShopMeta::find()->andWhere(['shop_id' => Yii::$app->request->post()['shop_id']])->one();
                $model->shop_id = Yii::$app->request->post()['shop_id'];
                $model->meta_key = REL_SHOP_META_FEATURED;
                $model->meta_value = 'false';
                $model->save();
            }
            return json_encode(['status' => true,'msg' => 'Status changed']);
        }
        
    }

    /**
     * Updates an existing Shop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    { 
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->shop_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    
    public function actionShopUpdate($id)
    { 
        $model = $this->findModel($id);
        $model->validate();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                       
            if(Yii::$app->request->post()['Shop']['isactive'] == Shop::$IN_ACTIVE)
            {
                $device_token = Notification::find()->andWhere(['user_id' => $model->createdby])->all();
                if(!empty($device_token)) {
                        foreach ($device_token as $key => $value) {
                          
                            $languages = UserMeta::find()->andWhere(['meta_key'=>REL_USER_LANGUAGE, 'user_id'=> $model->createdby])
                                        ->addSelect(['meta_value','meta_key','user_id'])->asArray()->all();
                            $lang = array();

                            foreach($languages as $language){
                                    $lang[] = $language['meta_value'];
                            } 

                            if (in_array("english", $lang)){
                                                                   

                             $notification_message = $this->processMessage('shop_blocked');
                            $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "Shop is blocked from Chibha");
                        
                                   
                            }else {    
                                
                              $notification_message = $this->processMessage('shop_lang_blocked');
                            $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "Shop is blocked from Chibha");
                        
                                 
                            } 
                            
                            
                        }
                }
            }
            if(Yii::$app->request->post()['Shop']['isactive'] == Shop::$IS_ACTIVE)
            {
                $device_token = Notification::find()->andWhere(['user_id' => $model->createdby])->all();
                if(!empty($device_token)) {
                        foreach ($device_token as $key => $value) {
                            
                             $languages = UserMeta::find()->andWhere(['meta_key'=>REL_USER_LANGUAGE, 'user_id'=> $model->createdby])
                                        ->addSelect(['meta_value','meta_key','user_id'])->asArray()->all();
                            $lang = array();

                            foreach($languages as $language){
                                    $lang[] = $language['meta_value'];
                            } 

                            if (in_array("english", $lang)){
                                                                   

                              $notification_message = $this->processMessage('shop_unblocked');
                            $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "Shop is unblocked from Chibha");
                        
                                   
                            }else {                                  
                             $notification_message = $this->processMessage('shop_lang_unblocked');
                            $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "Shop is unblocked from Chibha");
                            } 
                        }
                }
            }
            
            return json_encode(['status' => $model->isactive,'msg' => 'Status changed successfully!']);
        } else {
            return json_encode(['status' => false,'msg' => 'Status not changed']);
        }
        
    }
    
    // shop delete
    public function actionShopDelete($id) {        
       
        $this->init();
        if (Yii::$app->request->isPost) { 
            if(!isset(Yii::$app->request->post()['Shop']['shop_id']))
              {
                return $this->error(['error' => "Shop id is required"]);
              }
            
            // send push notification to user
            $this->debug_environ = 'developer';
            $model = $this->findModel($id);
            $device_token = Notification::find()->andWhere(['user_id' => $model->createdby])->all();
            if(!empty($device_token)) {
                    foreach ($device_token as $key => $value) {
                        $notification_message = $this->processMessage('shop_deleted');
                        $this->sendPush($value->device_token, $notification_message, $title = "Shop is deleted from Chibha");
//                   
                        
//                        $languages = UserMeta::find()->andWhere(['meta_key'=>REL_USER_LANGUAGE, 'user_id'=> $model->createdby])
//                                        ->addSelect(['meta_value','meta_key','user_id'])->asArray()->all();
//                            $lang = array();
//
//                            foreach($languages as $language){
//                                    $lang[] = $language['meta_value'];
//                            } 
//
//                            if (in_array("english", $lang)){
//                                                                   
//
//                              $notification_message = $this->processMessage('shop_deleted');
//                              $this->sendPush($value->device_token, $notification_message, $title = "Shop is deleted from Chibha");
//                   
//                                   
//                            }else {                                  
//                             $notification_message = $this->processMessage('shop_lang_deleted');
//                             $this->sendPush($value->device_token, $notification_message, $title = "Shop is deleted from Chibha");
//                   
//                                 
//                            }
                            
                            
                       
                        }
            }
          
            // delete shop photos
            $photos = PhotosMap::find()->andWhere(['item_id' => Yii::$app->request->post()['Shop']['shop_id'],'relationship' => REL_SHOP_PROFILE])->all();              
            $model = Shop::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id']])->one();  
              
        }
        
    }

    /**
     * Deletes an existing Shop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//           die("sdgsdgh");
        // send push notification to user
        $this->debug_environ = 'developer';
        $model = $this->findModel($id);
        $device_token = Notification::find()->andWhere(['user_id' => $model->createdby])->all();        
        if(!empty($device_token)) {
                foreach ($device_token as $key => $value) {
                   
                       $languages = UserMeta::find()->andWhere(['meta_key'=>REL_USER_LANGUAGE, 'user_id'=> $model->createdby])
                                        ->addSelect(['meta_value','meta_key','user_id'])->asArray()->all();
                            $lang = array();

                            foreach($languages as $language){
                                    $lang[] = $language['meta_value'];
                            } 

                            if (in_array("english", $lang)){
                                                                   

                              $notification_message = $this->processMessage('shop_deleted');
                              $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "Shop is deleted from Chibha");
                
                                   
                            }else {                                  
                             $notification_message = $this->processMessage('shop_lang_deleted');
                             $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "Shop is deleted from Chibha");
                
                                 
                            }
                            
                            
                  
                    
                }
        }
        $photosMap = '';
        $photosMap = PhotosMap::find()->andWhere(['item_id' => $id,'relationship' => REL_SHOP_PROFILE])->all();
        if(!empty($photosMap))
        {
            foreach ($photosMap as $key => $value) {
                Photos::deleteAll(['photos_id' => $value->photos_id]);
            }
        }
        
        $products = '';
        $products = Product::find()->andWhere(['shop_id' => $id])->all();
        if(!empty($products))
        {
            foreach ($products as $key => $value) {
                Order::deleteAll(['product_id' => $value->product_id ]);
            }
        }
        
        PhotosMap::deleteAll(['item_id' => $id,'relationship' => REL_SHOP_PROFILE]);
        Product::deleteAll(['shop_id' => $id]);
        ProductAvailability::deleteAll(['shop_id' => $id]);
        ProductDelivery::deleteAll(['shop_id' => $id]);
        ShopMeta::deleteAll(['shop_id' => $id]);
        ShopAvailability::deleteAll(['shop_id' => $id]);
        ShopDelivery::deleteAll(['shop_id' => $id]);
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shop::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
