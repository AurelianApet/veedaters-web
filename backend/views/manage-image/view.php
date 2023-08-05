<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Shop;
use common\models\User;
use common\helpers\AssestsManager;
use backend\models\Product;
use yii\helpers\Url;
use \backend\models\Order;

/* @var $this yii\web\View */
/* @var $model common\models\User */


$product = '';
$product = Product::find()->andWhere(['product_id' => $model->product_id])->one();

$order = '';
$order = Order::find()->andWhere(['product_id' => $model->product_id])->all();


$shop_name = Shop::find()->andWhere(['shop_id' => $product->shop_id])->one();

$product_delivery_fee = '';
$product_delivery_fee = backend\models\Town::find()->andWhere(['product_id' => $model->product_id])
        ->all();

?>
<div class="user-view">
    <section class="invoice">
      <!-- /.row -->
      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-12">
            <h1>Product: <strong><?= !empty($product->product_title) ? $product->product_title : '' ?></strong></h1>
            <?php 
                if(!empty($product->productImage))
                { 
                foreach ($product->productImage as $key => $value) {    
                ?>
            <img src="<?=!empty($value->photo_path) ? Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.$value->photo_path.'' : '../images/no-image-avail-large.jpg' ?>" style="width:200px;height:200px;">
            <?php   } // for closed    ?>
           <?php   }    ?>
                
        </div>
      </div>
      <!-- /.row -->      
    </section>    
    <hr class="dark-border">
    <section class="invoice">
        <div class="row">
          <div class="col-xs-7">
              <h4><strong>Product Name</strong></h4> 
              <p><?= !empty($product->product_title) ? $product->product_title : '' ?></p>
              <hr class="lighter-border">
              <h4><strong>Product Description</strong></h4> 
              <p><?= !empty($product->product_description) ? $product->product_description : '' ?></p>
              <hr class="lighter-border">
              <h4><strong>Product Ingredients</strong></h4> 
              <p><?= !empty($product->product_ingredients) ? $product->product_ingredients : '' ?></p>
              <hr class="lighter-border">
              <h4><strong>Cancellation Policy</strong></h4> 
              <p><?= !empty($product->product_cancellation_policy) ? $product->product_cancellation_policy : '' ?></p>
              <hr class="lighter-border">              
              <h4><strong>Shop Name</strong></h4> 
              <p><a href="<?=\yii\helpers\Url::to(['manage-shop/view', 'id' => $product->shop_id], true)?>"><?= !empty($shop_name->shop_title) ? $shop_name->shop_title : '' ?></a></p>
              <hr class="lighter-border">              
          </div>
          <div class="col-xs-5">
                <h4><strong>Product Price</strong></h4> 
                <p><?= !empty($product->product_price) ? "$".$product->product_price : '' ?></p>
                <h4><strong>Product Quantity</strong></h4> 
                <p><?= !empty($product->product_quantity) ? $product->product_quantity : '' ?></p>
                <hr class="lighter-border">
                <h4><strong>Address</strong></h4> 
                <p><?= !empty($product->product_location_address) ? $product->product_location_address : '' ?></p>
                <h4><strong>Coupons</strong></h4> 
                <p><?= !empty($product->product_quantity) ? $product->product_quantity : '' ?></p>
                <hr class="lighter-border">
                <h4><strong>Delivery Eligibility :</strong></h4> 
                <div class="defaultStatusDeliveryEligibility">
                    <?php 
                        if($product->product_shipping_status == 'true')
                        {
                       ?>
                       <a  href="javascript:void(0)" onclick="deliveryEligibility('Yes',<?=$product->product_id?>)">Yes</a>
                       <?php
                       }
                        if($product->product_shipping_status == 'false')
                        {
                        ?>    
                       <a  href="javascript:void(0)" onclick="deliveryEligibility('No',<?=$product->product_id?>)">No</a>
                      <?php } ?>
                </div>        
                <div class="changeStatusDeliveryEligibility">
                    <select class="form-control" onchange="setStatusDeliveryEligibility(this.value)" name="product_shipping_status" id="product_shipping_status">
                        <option <?php if($product->product_shipping_status == 'true') { ?> selected="selected" <?php } ?>  value="true">Yes</option>
                        <option <?php if($product->product_shipping_status == 'false') { ?> selected="selected" <?php } ?> value="false">No</option>
                    </select>
                </div> 
                <h4><strong>Delivery :</strong></h4> 
                <div class="defaultStatusDeliveryOption">
                    <?php 
                        if($product->product_delivery == 1)
                        {
                       ?>
                       <a  href="javascript:void(0)" onclick="deliveryOption('1',<?=$product->product_id?>)">Vendor</a>
                       <?php
                       }
                        if($product->product_delivery == 0)
                        {
                        ?>    
                       <a  href="javascript:void(0)" onclick="deliveryOption('0',<?=$product->product_id?>)">Vendors</a>
                      <?php } ?>
                </div> 
                <div class="changeStatusDeliveryOption">
                    <select class="form-control" onchange="setStatusDeliveryOption(this.value)" name="product_shipping_status" id="product_shipping_status">
                        <option <?php if($product->product_delivery == 1) { ?> selected="selected" <?php } ?>  value="1">Vendor</option>
                        <option <?php if($product->product_delivery == 0) { ?> selected="selected" <?php } ?> value="0">Vendors</option>
                    </select>
                </div> 
                <p></p>
                <h4><strong>Delivery Fee :</strong></h4> 
                <table class="delivery-fee">
                    <thead>
                        <th>Delivery City</th>
                        <th>Delivery Town</th>
                        <th>Fee</th>
                    </thead>
                    <tbody>
                        <?php if(!empty($product_delivery_fee)) { ?>
                        <?php foreach ($product_delivery_fee as $key => $value) { ?>
                        <tr class="delivery-fee-row-<?=$value->town_id?>">
                            <td><?=$value->delivery_city?></td>
                            <td><?=$value->delivery_town?></td>
                            <td style="width:30%;">
                                <a class="changeStatusDeliveryChargeLink" onclick="changeDeliveryCharges(<?=$value->town_id?>)"><?=$value->delivery_charges?></a>
                                <div class="changeStatusDeliveryCharge">
                                    <input 
                                        name="deliveryChargeValue" 
                                        id="deliveryChargeValue" 
                                        type="number"
                                        min="1"
                                        onkeydown="javascript: return event.keyCode == 69 ? false : true"
                                        onblur="setDeliveryCharge(this.value,<?=$value->town_id?>)"
                                        >
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php } else {  ?>
                        <tr class="delivery-fee-row">
                            <td></td>
                            <td><p>No records found</p></td>
                            <td></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <p></p>
                <hr class="lighter-border">
          </div>
        </div>
    </section>
    <hr class="lighter-border">
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <h3>Order History</h3>
              <div>
                <div class="box-body">
                  <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                          <div class="col-sm-6">
                          </div>
                                </div>
                                  <div class="row">
                                  <div class="col-sm-12">
                    <table id="product-order" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Date" >Date</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Order Number" >Order Number</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Username" >Coupon</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Location" >Location</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Status" >Status</th>
                    </tr>
                    </thead>
                    <tbody>
                       <?php if(!empty($order)) { ?> 
                        <?php foreach ($order as $key => $value) {?>
                        <tr role="row" class="even">
                            <td class="sorting_1"><?=date('m/d/Y', strtotime($value->createddate))?></td>
                            <td><a href="<?=\yii\helpers\Url::to(['manage-order/view', 'id' => $value->order_id], true)?>"><?=$value->order_id?></a></td>
                          <td>-</td>
                          <td><?=$value->order_address?></td>
                          <td>
                              <?php
                                   switch ($value->order_status) {
                                    case REL_ORDER_STATUS_WAITING_APPROVAL:
                                    echo "Waiting for approval";    
                                        break;
                                    case REL_ORDER_STATUS_APPROVE:
                                    echo "Approve";    
                                        break;
                                    case REL_ORDER_STATUS_READY:
                                    echo "Ready";    
                                        break;
                                    case REL_ORDER_STATUS_COMPLETED:
                                    echo "Completed";    
                                        break;
                                    default:
                                        break;
                                   }
                              
                              ?>
                          </td>
                        </tr>
                       <?php } ?>
                       <?php } ?>
                    </tbody>
                  </table></div></div></div>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
    </section>
    
    <section class="content">
        <div class="container">
            <div class="row">
                <input type="hidden" name="product_id" id="product_id" value="<?=$product->product_id?>">
                <div class="col-xs-12 custom-center-class">
                    <?php if($product->isactive == Product::$IS_ACTIVE) { ?>
                        <button data-product-status="<?=Product::$IN_ACTIVE?>" id="deactivate-product" data-product-id="<?=$product->product_id?>" class="deactivate-product btn btn-primary btn btn-danger">DEACTIVATE PRODUCT</button>
                        <button data-product-status="<?=Product::$IS_ACTIVE?>" style="display: none;" id="activate-product" data-product-id="<?=$product->product_id?>" class="deactivate-product btn btn-primary btn btn-success">ACTIVATE PRODUCT</button>
                    <?php } ?>
                    <?php if($product->isactive == Product::$IN_ACTIVE) { ?>
                        <button data-product-status="<?=Product::$IS_ACTIVE?>"  id="activate-product" data-product-id="<?=$product->product_id?>" class="deactivate-product btn btn-primary btn btn-success">ACTIVATE PRODUCT</button>
                        <button data-product-status="<?=Product::$IN_ACTIVE?>" style="display: none;"  id="deactivate-product" data-product-id="<?=$product->product_id?>" class="deactivate-product btn btn-primary btn btn-danger">DEACTIVATE PRODUCT</button>
                    <?php } ?>
                </div>    
            </div>    
        </div>
    </section>
</div>
