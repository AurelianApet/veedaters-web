<?php

namespace common\models;
use common\models\User;
use \common\models\TransactionRecords;
use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $user_id
 * @property string $order_type
 * @property string $order_date
 * @property string $order_time
 * @property integer $order_quantity
 * @property integer $order_price
 * @property integer $coupon_code
 * @property integer $order_admin_fee
 * @property integer $order_total
 * @property string $additional_note
 * @property integer $is_active
 * @property integer $createdby
 * @property string $createddate
 * @property integer $updatedby
 * @property string $updateddate
 */
class Order extends \common\models\ModelBase
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'user_id', 'order_type', 'order_quantity', 'order_price', 'order_admin_fee', 'order_total'], 'required'],
            [['product_id', 'user_id', 'order_quantity', 'is_active', 'createdby', 'updatedby'], 'integer'],
            [['order_date', 'order_time', 'createddate', 'updateddate'], 'safe'],
            [['additional_note','order_name','zip_code','order_address','start_time','end_time'], 'string'],
            [['order_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'product_id' => 'Product ID',
            'user_id' => 'User ID',
            'order_type' => 'Order Type',
            'order_date' => 'Order Date',
            'order_time' => 'Order Time',
            'order_quantity' => 'Order Quantity',
            'order_price' => 'Order Price',
            'coupon_code' => 'Coupon Code',
            'order_admin_fee' => 'Order Admin Fee',
            'order_total' => 'Order Total',
            'additional_note' => 'Additional Note',
            'is_active' => 'Is Active',
            'createdby' => 'Createdby',
            'createddate' => 'Createddate',
            'updatedby' => 'Updatedby',
            'updateddate' => 'Updateddate',
        ];
    }

    /**
     * @inheritdoc
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }
    
    public function getPhoto(){
       return $this->hasOne(Photos::className(), ['photos_id' => 'photos_id'])->alias('p')->addSelect(['p.photos_id','p.photo_path'])
                 ->viaTable(PhotosMap::tableName().' phM', ['item_id' => 'product_id'], function($q){
                    $q->onCondition(['phM.relationship' => REL_PRODUCT_PICTURE]);
                });
    }
    
    public function getProduct(){
       return $this->hasOne(Product::className(), ['product_id' => 'product_id'])->alias('cS');
    }
    
    public function getTown(){
       return $this->hasOne(Town::className(), ['product_id' => 'product_id'])->alias('pT');
    }
    
    public function getDelivery(){
       return $this->hasOne(ProductDelivery::className(), ['product_id' => 'product_id'])->alias('pD');
    }
    
    public function getPickup(){
       return $this->hasOne(ProductAvailability::className(), ['product_id' => 'product_id'])->alias('pA');
    }
    
    public function getUser(){
       return $this->hasOne(User::className(), ['id' => 'createdby'])->alias('oU');
    }

    public function getUserDetail(){
        return $this->hasOne(User::className(), ['id' => 'createdby'])->alias('oU');
    }
    
    public function getTransaction(){
        return $this->hasOne(TransactionRecords::className(), ['order_id' => 'order_id'])->alias('tO');
    }

    // public function getReview(){
    //     return $this->hasOne(Rsseview::className(), ['order_id' => 'order_id'])->alias('oR');
    //  }
}
