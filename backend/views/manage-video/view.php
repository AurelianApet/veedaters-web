<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Shop;
use common\models\User;
use common\helpers\AssestsManager;
use backend\models\Product;
use \backend\models\Order;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$shop = '';
$shop = Shop::find()->andWhere(['shop_id' => $model->shop_id])->one();
$products = '';
$products = Product::find()->andWhere(['shop_id' => $model->shop_id])->all();

$productsIds = Product::find()->andWhere(['shop_id' => $model->shop_id])->asArray()->all();

$orders = '';
$orderCnt = 0;
if(count(array_column($productsIds,'product_id')) > 0)
{
    $orders = Order::find()
            ->andWhere(['IN','product_id',array_column($productsIds,'product_id')])
            ->all();
    
    $orderCnt = Order::find()
            ->andWhere(['IN','product_id',array_column($productsIds,'product_id')])
            ->andWhere(['IN','order_status', [REL_ORDER_STATUS_APPROVE,REL_ORDER_STATUS_READY,REL_ORDER_STATUS_WAITING_APPROVAL]])
            ->all();
}


$user = User::find()->andWhere(['id' => $shop->createdby])->one();


$connection = Yii::$app->getDb();
$command = $connection->createCommand("
        SELECT message.message_id,message.message_creator_id,message.message_type,message.message_creator_id,
        message_recipient.message_id,message_recipient.recipient_id, message.message_body
        FROM message 
        LEFT JOIN message_recipient 
        ON message.message_id=message_recipient.message_id 
        WHERE message_recipient.recipient_id=".$shop->createdby."
        AND message.message_type=".REL_MESSAGE_TYPE_SHOP."    
        ");


$messages = $command->queryAll();
?>
<div class="user-view">
    <section class="invoice">
      <!-- /.row -->
      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-7">
            <h1>Shop: <strong><?= !empty($shop->shop_title) ? $shop->shop_title : '' ?></strong></h1>
             <img src="<?=!empty($shop->shopImage[0]->photo_path) ? Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.$shop->shopImage[0]->photo_path.'' : '../images/no-image-avail-large.jpg' ?>" style="width:300px;height:200px;">
            <h4> Shop Description: <br><br> <?= !empty($shop->shop_description) ? $shop->shop_description :'';?></h4>
        </div>
        <!-- /.col -->
        <div class="col-xs-5">
            <h3 >Last Active : <?= !empty($shop->shopOwner->updateddate) ? date('m/d/Y', strtotime($shop->shopOwner->updateddate)) : ''?></h3>
            <h3>Joined Date : <?= !empty($shop->updateddate) ? date('m/d/Y', strtotime($shop->updateddate)) : ''?></h3>          
            <h3>Username : <?= !empty($shop->shopOwner) ? "<a href='" . \yii\helpers\Url::to(['user/view', 'id' =>$shop->shopOwner->id], true) . "'>".$shop->shopOwner->username."</a>"  : ''?></h3>          
            <h3>Delivery : $<?= !empty($shop->shop_delivery) ? $shop->shop_delivery : ''?></h3>          
            <h3>Address : <?= !empty($shop->shop_address) ? $shop->shop_address : ''?></h3>
            <h3>Shop Email : <?= !empty($shop->shopOwner) ? $shop->shopOwner->email : ''?></h3>
            <h3>Shop Phone : <?= !empty($shop->shopOwner->phone) ? $shop->shopOwner->phone : ''?></h3>
            <h3>Shop Deposit Information : <?= !empty($shop->shop_deposit_information) ? $shop->shop_deposit_information : ''?></h3>
        </div>
        <!-- /.col -->        
      </div>      
      <!-- /.row -->      
    </section>    
    <hr style="border:solid 1px #ccc;">
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
                    <table id="shop_order_history" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Date" >Date</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Order Number" >Order Number</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Username" >Username</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Price" >Price</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Location" >Location</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Status" >Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($orders))
                    {
                    foreach ($orders as $key => $value) {
                    ?>    
                    <tr role="row" class="even">
                      <td class="sorting_1"><?=!empty($value->createddate) ? date('m-d-Y',strtotime($value->createddate)):''?></td>
                      <td><a href="<?=\yii\helpers\Url::to(['/manage-order/view', 'id' => $value->order_id], true)?>"><?=$value->order_id?></a></td>
                      <td>
                          <a href="<?=\yii\helpers\Url::to(['/user/view', 'id' => $value->user_id], true)?>">
                            <?php 
                            $users = User::findOne(['id' => $value->user_id]);
                           
                            echo isset($users) ? $users->username : '';
                            ?>
                          </a>
                      </td>
                      <td><?=$value->order_price?></td>
                      <td><?=$value->order_address?></td>
                      <td>
                          <?php
                            switch ($value->order_status) {
                                case REL_ORDER_STATUS_WAITING_APPROVAL:
                                    //echo "Waiting for approval";
                                    echo "Active";
                                    break;
                                case REL_ORDER_STATUS_APPROVE:
                                    //echo "Approved";
                                    echo "Active";
                                    break;
                                case REL_ORDER_STATUS_READY:
                                    //echo "Ready";
                                    echo "Active";
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
                    <?php }  // if closed ?>
                    <?php } // for each closed ?>
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
    <hr style="border:solid 1px #4B4B4B;">
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <h3>Products</h3>
              <div>
                <div class="box-body">
                  <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                          <div class="col-sm-6">
                          </div>
                                </div>
                                  <div class="row">
                                  <div class="col-sm-12">
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Date Active" >Date Active</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Product Name" >Product Name</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Location" >Location</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Status" >Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($products))
                    {
                        foreach ($products as $key => $value) {
                            ?>
                          <tr role="row" class="even">
                              <td class="sorting_1"><?=!empty($value->createddate) ? date('m/d/Y', strtotime($value->createddate)) : '';?></td>
                              <td><a href='../manage-food/view?id=<?=$value->product_id?>'><?=!empty($value->product_title) ? $value->product_title : '';?></a></td>
                            <td><?=!empty($value->product_location_address) ? $value->product_location_address : '';?></td>
                            <td>
                            <?php
                                if($value->isactive == Product::$IS_ACTIVE)
                                {
                                    echo "Approved";
                                }
                                if($value->isactive == Product::$IN_ACTIVE)
                                {
                                    echo "Needs Approval";
                                } 
                            ?>    
                            </td>
                          </tr>  
                    <?php
                    } // for close
                    ?>    
                    <?php } else {  ?>
                        <tr role="row" class="odd">
                            <td>No Products</td>
                        </tr>
                    <?php } ?></tbody>
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
    <hr style="border:solid 1px #4B4B4B;">
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <h3>Blocks/Flags</h3>
              <div>
                <div class="box-body">
                  <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                          <div class="col-sm-6">
                          </div>
                                </div>
                                  <div class="row">
                                  <div class="col-sm-12">
                                  <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Date" >Date</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Block/Flag" >Block/Flag</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Username" >Username</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Reason" >Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--<tr role="row" class="even">
                      <td class="sorting_1">Gecko</td>
                      <td>Netscape Browser 8</td>
                      <td>Win 98SE+</td>
                      <td>1.7</td>
                    </tr><tr role="row" class="odd">
                      <td class="sorting_1">Gecko</td>
                      <td>Netscape Navigator 9</td>
                      <td>Win 98+ / OSX.2+</td>
                      <td>1.8</td>
                    </tr><tr role="row" class="even">
                      <td class="sorting_1">Gecko</td>
                      <td>Mozilla 1.0</td>
                      <td>Win 95+ / OSX.1+</td>
                      <td>1</td>
                    </tr>-->
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
    <hr style="border:solid 1px #4B4B4B;">
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <h3>Deposits</h3>
              <div>
                <div class="box-body">
                  <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                          <div class="col-sm-6">
                          </div>
                      </div>
                    <div class="row">
                    <div class="col-sm-12">
                    <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Date" >Date</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Amount Deposited" >Amount Deposited</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Payment Method" >Payment Method</th>
                        <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Details" >Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($orders))
                    {
                    foreach ($orders as $key => $value) {
                    ?>    
                    <tr role="row" class="even">
                      <td class="sorting_1"><?=!empty($value->createddate) ? date('m-d-Y',strtotime($value->createddate)):''?></td>
                      <td>$<?=$value->order_price?></td>
                      <td>Stripe</td>
                      <td><a href="<?=\yii\helpers\Url::to(['/manage-order/view', 'id' => $value->order_id], true)?>"><?=$value->order_id?></a></td>
                    </tr>
                    <?php }  // if closed ?>
                    <?php } // for each closed ?>
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
                 <div class="col-md-10">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><h4><?=!empty($user->username) ? $user->username:''?></h4></div>
                            <div class="panel-body" id="user-message-body-section" style="height: auto 0px;">
                             <?php if(!empty($messages)) { ?>  
                             <?php foreach ($messages as $key => $value) {
                                ?>  
                             <?php 
                             $dynamic_class = "right";
                             if(Yii::$app->user->getId() == $value['message_creator_id']){
                                 $dynamic_class = "left";
                              }
                              ?>   
                            <div class="clearfix">
                                  <blockquote class="me pull-<?=$dynamic_class;?>">
                                    <?=!empty($value['message_body']) ? $value['message_body'] : '';?>
                                  </blockquote>
                            </div>
                              <?php } // for closed ?> 
                              <?php } ?>  
                            </div>
                            <textarea data-recipient-id="<?=$model->createdby?>" class="single-user-message" placeholder="Type a message..."></textarea>
                        </div>
                 </div>
                 <div class="col-md-2"></div>
            </div>   
        </div>
        <!-- /.row -->
        <?php  if($shop->isactive == Shop::$IS_ACTIVE) { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-3">                    
                    <button data-shop-status="<?=Shop::$IN_ACTIVE?>" id="deactivate-shop" data-shop-id="<?=$shop->shop_id?>" class="deactivate-shop btn btn-primary btn btn-danger">DEACTIVATE SHOP</button>            
                </div>
                <div class="col-md-3">                    
                    <button data-shop-status="<?=Shop::$IS_ACTIVE?>" style="display: none;" id="activate-shop" data-shop-id="<?=$shop->shop_id?>" class="deactivate-shop btn btn-primary btn btn-success">REACTIVATE SHOP</button>
                </div>
                <div class="col-md-3">  
                    <?php
                    $erroMsg = '';
                    if($orderCnt == 0)
                    {
                        $disableBtn = '';
                    }
                    else
                    {
                        $disableBtn = 'disabled';
                        $erroMsg = "<span style='color:red;font-size:11px;'> Shop have active orders so you can not delete it now</span>";
                    }
                    ?>
                    <button <?=$disableBtn?> data-shop-status="<?=Shop::$IS_ACTIVE?>" id="delete-shop" data-shop-id="<?=$shop->shop_id?>" class="delete-shop btn btn-primary btn btn-danger">DELETE PERMANENTLY</button>
                    <?=$erroMsg;?>
                </div>
            </div>
        </div>            
        <?php } ?>
        <?php if($shop->isactive == Shop::$IN_ACTIVE) { ?>            
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php
//                    if(empty($shop->shop_address))
//                    { 
//                        $dynamicClass = 'disabled'; 
//                        echo "<span style='color:red;mar'> Shop is not set up yet</span>";
//                    }
//                    else
//                    {
//                        $dynamicClass = '';   
//                    }
                    ?>
                    <button data-shop-status="<?=Shop::$IS_ACTIVE?>"  id="activate-shop" data-shop-id="<?=$shop->shop_id?>" class="deactivate-shop btn btn-primary btn btn-success">REACTIVATE SHOP</button>                    
                </div>
                <div class="col-md-3">
                    <button data-shop-status="<?=Shop::$IN_ACTIVE?>" style="display: none;"  id="deactivate-shop" data-shop-id="<?=$shop->shop_id?>" class="deactivate-shop btn btn-primary btn btn-danger">DEACTIVATE SHOP</button>
                </div>
                <div class="col-md-3">
                    <button data-shop-status="<?=Shop::$IS_ACTIVE?>" id="delete-shop" data-shop-id="<?=$shop->shop_id?>" class="delete-shop btn btn-primary btn btn-danger">DELETE PERMANENTLY</button>
                </div>
            </div>
        </div>            
        <?php } ?>
    </section>
</div>
