<?php

namespace common\models;
use \common\helpers\AssestsManager;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Photos;
use common\models\PhotosMap;
use Yii;

/**
 * This is the model class for table "shop".
 *
 * @property integer $shop_id
 * @property string $shop_title
 * @property string $shop_description
 * @property string $shop_delivery
 * @property string $shop_address
 * @property string $shop_email
 * @property string $shop_phone
 * @property string $shop_deposit_information
 * @property string $shop_last_active
 * @property integer $isactive
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class Shop extends \common\models\ModelBase 
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_title'], 'required'],
            [['shop_last_active', 'createddate', 'updateddate'], 'safe'],
            [['isactive', 'shop_delivery', 'shop_zip', 'shop_account_number','shop_routing_number','createdby', 'updatedby'], 'integer'],
            [['shop_title','shop_phone', 'shop_deposit_information'], 'string', 'max' => 100],
            [['shop_description','cancellation_policy'], 'string', 'max' => 500],
            [['shop_address','shop_lat','shop_lng'], 'string', 'max' => 200],
            [['shop_email'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shop_id' => 'Shop ID',
            'shop_title' => 'Shop Title',
            'shop_description' => 'Shop Description',
            'shop_delivery' => 'Shop Delivery',
            'shop_address' => 'Shop Address',
            'shop_email' => 'Shop Email',
            'shop_phone' => 'Shop Phone',
            'shop_deposit_information' => 'Shop Deposit Information',
            'shop_last_active' => 'Shop Last Active',
            'isactive' => 'Isactive',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return ShopQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShopQuery(get_called_class());
    }
    
    
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }


    public function updateShop($imageFiles = []) { 
        try {
            
            $photo_ids = []; 
            if(!empty(Yii::$app->request->post()['Shop']['photo_id']))
            {
               $photo_ids = Photos::find()
                   ->addSelect(['photos_id','photo_path'])
                   ->andWhere(['IN','photos_id',Yii::$app->request->post()['Shop']['photo_id']])
                   ->all();           
               // delete photos of the product
               if(!empty($photo_ids))
               {
                   foreach ($photo_ids as $key => $value) {
                       if(file_exists("../../uploads/".$value->photo_path))
                       {
                           unlink("../../uploads/".$value->photo_path);
                       }
                       Photos::deleteAll(['photos_id' => $value->photos_id]);
                       // delete photos from photomap table
                       PhotosMap::deleteAll(['photos_id' => $value->photos_id,'relationship' =>  REL_SHOP_PROFILE]);
                   }

               }  

            }
            
            
            
          if ($imageFiles) {  
            if (isset($imageFiles['shop_photo']) && !empty($imageFiles['shop_photo'])) {
              $formSingle = new \api\modules\v1\models\ImageUploadForm();
              $formSingle->temp_images = $imageFiles['shop_photo'];
              if ($formSingle->upload('_shop')) {
                
                if(!file_exists(\Yii::getAlias('@uploads') . '/shop/')) {
                    $old_umask = umask(0);                    
                    mkdir(\Yii::getAlias('@uploads') . '/shop/', 0777, true);
                    chmod(\Yii::getAlias('@uploads') . '/shop/', 0777);
                    umask($old_umask);
                }
                if(!file_exists(\Yii::getAlias('@uploads') . '/shop/'.$this->shop_id)) {
                    $old_umask = umask(0);
                    mkdir(\Yii::getAlias('@uploads') . '/shop/'.$this->shop_id, 0777, true);
                    umask($old_umask);
                }
                $fileDateArray = $this->array_column($formSingle->response, 'savedName');
                $photoUploader = new \common\helpers\PhotoUploader(AssestsManager::PHOTO_DIR_SHOP);                
                $photoUploader->entity = $this;
                $photoUploader->relationship = REL_SHOP_PROFILE;
                $photoUploader->upload($fileDateArray);
              }
            }
          }
          
        // update Shop delivery
        $this->updateShopDelivery($this->shop_id);
          
        $this->updateShopDeliveryTown($this->shop_id);
        // update product availability
        $this->updateShopAvailability($this->shop_id);
          
          
        } catch (\Exception $e) {
          throw $e;
        }
        return true;
    }
    
    
    function updateShopDelivery($product_id) { 
       if(isset(Yii::$app->request->post()['Shop']['delivery_working_day_id']))
        {
           
            // delete exsisting enteries
            ShopDelivery::deleteAll(['shop_id' => Yii::$app->request->post()['Shop']['shop_id']]);             
            
            $product_delivery = array_values(Yii::$app->request->post()['Shop']['delivery_working_day_id']);
            foreach ($product_delivery as $key => $value) {                                 
                  $shop_delivery = ShopDelivery::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'delivery_working_day_id' => $value])->one();                  
                  if(!empty($shop_delivery))
                  {
                      $shop_delivery = ShopDelivery::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'delivery_working_day_id' => $value])->one();
                  }
                  else
                  {
                      $shop_delivery = new ShopDelivery();
                  }
                  $shop_delivery->shop_id = Yii::$app->request->post()['Shop']['shop_id'];
                  $shop_delivery->delivery_working_day_id = $value;
                  $shop_delivery->delivery_start_time = Yii::$app->request->post()['Shop'][$value]['delivery_start_time'];
                  $shop_delivery->delivery_end_time = Yii::$app->request->post()['Shop'][$value]['delivery_end_time'];
                  $shop_delivery->validate();
                  $shop_delivery->save();
              }
        }
    }
    
    function updateShopDeliveryTown($product_id) { 
       if(!empty(Yii::$app->request->post()['Shop']['delivery_town']))
        {
           
            // delete exsisting enteries
            Town::deleteAll(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'product_id' => $product_id]);
            
            foreach (Yii::$app->request->post()['Shop']['delivery_town'] as $key => $value) { 
                  $product_delivery = Town::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'delivery_town' => $value])->one();
                  if(!empty($product_delivery))
                  {
                      $product_delivery = Town::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'delivery_town' => $value])->one();
                  }
                  else
                  {
                      $product_delivery = new Town();
                  }
                  $product_delivery->shop_id = Yii::$app->request->post()['Shop']['shop_id'];
                  $product_delivery->product_id = $product_id;
                  $product_delivery->delivery_city = Yii::$app->request->post()['Shop']['delivery_city'];
                  $product_delivery->delivery_town = Yii::$app->request->post()['Shop']['delivery_town'][$key];
                  $product_delivery->delivery_charges = Yii::$app->request->post()['Shop']['delivery_charges'][$key];
                  $product_delivery->validate();
                  //c($product_delivery->getErrors()); die;
                  $product_delivery->save();
              }
        }
    }
    
    
    // update shop availability
    function updateShopAvailability($product_id) { 
       if(isset(Yii::$app->request->post()['Shop']['availability_working_day_id']))
            {
                
                // delete exsisting enteries
                ShopAvailability::deleteAll(['shop_id' => Yii::$app->request->post()['Shop']['shop_id']]); 
           
                foreach (Yii::$app->request->post()['Shop']['availability_working_day_id'] as $key => $value) {
                      $shop_available = ShopAvailability::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'availability_working_day_id' => $value])->one();
                      if(!empty($shop_available))
                      {
                        $shop_available = ShopAvailability::find()->andWhere(['shop_id' => Yii::$app->request->post()['Shop']['shop_id'],'availability_working_day_id' => $value])->one();
                      }
                      else
                      {
                        $shop_available = new ShopAvailability();
                      }
                      $shop_available->shop_id = Yii::$app->request->post()['Shop']['shop_id'];
                      $shop_available->availability_working_day_id = $value;
                      $shop_available->availability_start_time = Yii::$app->request->post()['Shop'][$value]['availability_start_time'];
                      $shop_available->availability_end_time = Yii::$app->request->post()['Shop'][$value]['availability_end_time'];
                      $shop_available->save();
                  }
            }
    }
    
    
    public function getShopOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'createdby']);
    }
        
    public function getShopImage() { 
        return $this->hasMany(Photos::className(), ['photos_id' => 'photos_id'])
              ->viaTable('{{%photos_map}}', ['item_id' => 'shop_id'], function($query){
                  $query->alias('pMS');
                  return $query->andOnCondition(['pMS.relationship' => REL_SHOP_PROFILE]);
              })->alias('pS');
    }
    
    public function getPhotos(){
       return $this->hasMany(Photos::className(), ['photos_id' => 'photos_id'])->alias('p')->addSelect(['p.photos_id','p.photo_path'])
                 ->viaTable(PhotosMap::tableName().' phM', ['item_id' => 'shop_id'], function($q){
                    $q->onCondition(['phM.relationship' => REL_SHOP_PROFILE]);
                });
    }
    
    public function getDelivery(){
       return $this->hasMany(ShopDelivery::className(), ['shop_id' => 'shop_id'])->alias('pD');
    }
    
    public function getTown(){
       return $this->hasMany(Town::className(), ['shop_id' => 'shop_id'])->alias('sT');
    }
    
    public function getMeta(){
       return $this->hasMany(ShopMeta::className(), ['shop_id' => 'shop_id'])->alias('sM');
    }
    
    public function getAvailability(){
       return $this->hasMany(ShopAvailability::className(), ['shop_id' => 'shop_id'])->alias('sA');
    }

    public function getOwner(){
       return $this->hasOne(User::className(), ['id' => 'createdby']);
    }
     
    public function getProducts(){
       return $this->hasMany(Product::className(), ['shop_id' => 'shop_id']);
    }
    
}
