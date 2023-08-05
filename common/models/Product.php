<?php

namespace common\models;
use \common\helpers\AssestsManager;
use common\models\Photos;
use common\models\User;
use common\models\PhotosMap;
use Yii;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property integer $product_id
 * @property string $product_title
 * @property string $product_description
 * @property string $product_ingredients
 * @property string $product_quantity
 * @property integer $product_price
 * @property string $product_cancellation_policy
 * @property string $product_location_address
 * @property string $product_location_lat
 * @property string $product_location_lng
 * @property string $product_shipping_status
 * @property integer $isactive
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class Product extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_title'], 'required'],
            [['isactive','shop_id','product_id', 'createdby', 'updatedby','product_delivery'], 'integer'],
            [['createddate', 'updateddate'], 'safe'],
            [['product_price'], 'number'],
            [['product_title'], 'string', 'max' => 100],
            [['product_description','product_shipping_charges', 'product_ingredients','product_location_address', 'product_cancellation_policy'], 'string', 'max' => 500],
            [['product_quantity'], 'string', 'max' => 50],
            [['product_location_lat', 'product_location_lng'], 'string', 'max' => 25],
            [['product_shipping_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'product_title' => 'Product Title',
            'product_description' => 'Product Description',
            'product_ingredients' => 'Product Ingredients',
            'product_quantity' => 'Product Quantity',
            'product_price' => 'Product Price',
            'product_cancellation_policy' => 'Product Cancellation Policy',
            'product_location_address' => 'Product Location Address',
            'product_location_lat' => 'Product Location Lat',
            'product_location_lng' => 'Product Location Lng',
            'product_shipping_status' => 'Product Shipping Status',
            'isactive' => 'Isactive',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
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
    
    public function updateProduct($imageFiles = []) { 
        try {
            
          // delete old images from photos table
            if(!empty($this->product_id))
            {
                 $photo_ids = []; 
                 if(!empty(Yii::$app->request->post()['Product']['photo_id']))
                 {
                    $photo_ids = Photos::find()
                        ->addSelect(['photos_id','photo_path'])
                        ->andWhere(['IN','photos_id',Yii::$app->request->post()['Product']['photo_id']])
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
                            PhotosMap::deleteAll(['photos_id' => $value->photos_id,'relationship' =>  REL_PRODUCT_PICTURE]);
                        }
                        
                    }  
                    
                 }
            }  
            
          if ($imageFiles) {
              
            if (isset($imageFiles['product_photos']) && !empty($imageFiles['product_photos'])) {
              $formSingle = new \api\modules\v1\models\ImageUploadForm();
              $formSingle->temp_images = $imageFiles['product_photos'];
              if ($formSingle->upload('_product')) { 
                $this->save();
                if(!file_exists(\Yii::getAlias('@uploads') . '/product/')) {
                    $old_umask = umask(0);                    
                    mkdir(\Yii::getAlias('@uploads') . '/product/', 0777, true);
                    chmod(\Yii::getAlias('@uploads') . '/product/', 0777);
                    umask($old_umask);
                }
                if(!file_exists(\Yii::getAlias('@uploads') . '/product/'.$this->product_id)) {
                    //$old_umask = umask(0);
                    mkdir(\Yii::getAlias('@uploads') . '/product/'.$this->product_id, 0777, true);
                    chmod(\Yii::getAlias('@uploads') . '/product/'.$this->product_id, 0777);
                    //umask($old_umask);
                }
                $fileDateArray = $this->array_column($formSingle->response, 'savedName');
                $photoUploader = new \common\helpers\PhotoUploader(AssestsManager::PHOTO_DIR_PRODUCT);                
                $photoUploader->entity = $this;
                $photoUploader->relationship = REL_PRODUCT_PICTURE;
                $photoUploader->upload($fileDateArray);
              }
            }
          }
          
          // update product delivery
          $this->updateProductDelivery($this->product_id);
          
          $this->updateProductDeliveryTown($this->product_id);
          // update product availability
          $this->updateProductAvailability($this->product_id);
          

        } catch (\Exception $e) {
          throw $e;
        }
        return true;
    }
    
    public function getPhoto(){
       return $this->hasOne(Photos::className(), ['photos_id' => 'photos_id'])->alias('sPP')->addSelect(['sPP.photos_id','sPP.photo_path'])
                 ->viaTable(PhotosMap::tableName().' pP', ['item_id' => 'product_id'], function($q){
                    $q->onCondition(['pP.relationship' => REL_PRODUCT_PICTURE]);
                });
    }
        
    public function getShop(){
       return $this->hasOne(Shop::className(), ['shop_id' => 'shop_id'])->alias('cS');
    }
    
    public function getShopName(){
       return $this->hasOne(Shop::className(), ['shop_id' => 'shop_id'])->alias('cSN');
    }
    
    public function getDelivery(){
       return $this->hasMany(ProductDelivery::className(), ['product_id' => 'product_id'])->alias('pD');
    }
    
    public function getAvailable(){
       return $this->hasMany(ProductAvailability::className(), ['product_id' => 'product_id'])->alias('pA')->orderBy('pA.availability_working_day_id ASC');
    }
        
    public function getReview(){
       return $this->hasOne(Review::className(), ['product_id' => 'product_id'])->alias('pR');
    }
    
    public function getTown(){
       return $this->hasMany(Town::className(), ['product_id' => 'product_id'])->alias('pT');
    }

    
    public function getOwner(){
        return $this->hasOne(User::className(), ['id' => 'createdby']);
    }
     
    public function getPhotos(){
       return $this->hasMany(Photos::className(), ['photos_id' => 'photos_id'])->alias('p')->addSelect(['p.photos_id','p.photo_path'])
                 ->viaTable(PhotosMap::tableName().' phM', ['item_id' => 'product_id'], function($q){
                    $q->onCondition(['phM.relationship' => REL_PRODUCT_PICTURE]);
                });
    }
    
    function updateProductDelivery($product_id) { 
       if(isset(Yii::$app->request->post()['Product']['delivery_working_day_id']))
        {
           
            // delete exsisting enteries
           if(isset(Yii::$app->request->post()['Product']['product_id'])){
               ProductDelivery::deleteAll(['product_id' => Yii::$app->request->post()['Product']['product_id']]);             
           }
            
            foreach (Yii::$app->request->post()['Product']['delivery_working_day_id'] as $key => $value) {                                 
                  $product_delivery = ProductDelivery::find()->andWhere(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id ,'delivery_working_day_id' => $value])->one();                  
                  if(!empty($product_delivery))
                  {
                      $product_delivery = ProductDelivery::find()->andWhere(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id ,'delivery_working_day_id' => $value])->one();
                  }
                  else
                  {
                      $product_delivery = new ProductDelivery();
                  }
                  $product_delivery = new ProductDelivery();
                  
                  $product_delivery->shop_id = Yii::$app->request->post()['Product']['shop_id'];
                  $product_delivery->product_id = $product_id;
                  $product_delivery->delivery_working_day_id = $value;
                  $product_delivery->delivery_start_time = Yii::$app->request->post()['Product'][$value]['delivery_start_time'];
                  $product_delivery->delivery_end_time = Yii::$app->request->post()['Product'][$value]['delivery_end_time'];
                  $product_delivery->validate();
                  $product_delivery->save();
              }
        }
    }
    
    function updateProductDeliveryTown($product_id) { 
       if(!empty(Yii::$app->request->post()['Product']['delivery_town']))
        {
           
            // delete exsisting enteries
            Town::deleteAll(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id]);
            
            foreach (Yii::$app->request->post()['Product']['delivery_town'] as $key => $value) { 
                  $product_delivery = Town::find()->andWhere(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id ,'delivery_town' => $value])->one();
                  if(!empty($product_delivery))
                  {
                      $product_delivery = Town::find()->andWhere(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id ,'delivery_town' => $value])->one();
                  }
                  else
                  {
                      $product_delivery = new Town();
                  }
                  $product_delivery->shop_id = Yii::$app->request->post()['Product']['shop_id'];
                  $product_delivery->product_id = $product_id;
                  $product_delivery->delivery_city = Yii::$app->request->post()['Product']['delivery_city'];
                  $product_delivery->delivery_town = Yii::$app->request->post()['Product']['delivery_town'][$key];
                  $product_delivery->delivery_charges = Yii::$app->request->post()['Product']['delivery_charges'][$key];
                  $product_delivery->validate();
                  //c($product_delivery->getErrors()); die;
                  $product_delivery->save();
              }
        }
    }
    
    public function getProductImage() { 
        return $this->hasMany(Photos::className(), ['photos_id' => 'photos_id'])
              ->viaTable('{{%photos_map}}', ['item_id' => 'product_id'], function($query){
                  $query->alias('pMS');
                  return $query->andOnCondition(['pMS.relationship' => REL_PRODUCT_PICTURE]);
              })->alias('pS');
    }
    
    
    // update product availability
    function updateProductAvailability($product_id) { 
       if(isset(Yii::$app->request->post()['Product']['availability_working_day_id']))
            {
                
                // delete exsisting enteries
                ProductAvailability::deleteAll(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id]); 
           
                foreach (Yii::$app->request->post()['Product']['availability_working_day_id'] as $key => $value) {
                      $shop_available = ProductAvailability::find()->andWhere(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id, 'availability_working_day_id' => $value])->one();
                      if(!empty($shop_available))
                      {
                        $shop_available = ProductAvailability::find()->andWhere(['shop_id' => Yii::$app->request->post()['Product']['shop_id'],'product_id' => $product_id, 'availability_working_day_id' => $value])->one();
                      }
                      else
                      {
                        $shop_available = new ProductAvailability();
                      }
                      $shop_available->shop_id = Yii::$app->request->post()['Product']['shop_id'];
                      $shop_available->product_id = $product_id;
                      $shop_available->availability_working_day_id = $value;
                      $shop_available->availability_start_time = Yii::$app->request->post()['Product'][$value]['availability_start_time'];
                      $shop_available->availability_end_time = Yii::$app->request->post()['Product'][$value]['availability_end_time'];
                      $shop_available->save();
                  }
            }
    }
}
