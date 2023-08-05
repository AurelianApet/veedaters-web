<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Shop;
use common\models\User;
use common\helpers\AssestsManager;
use common\models\Product;
use yii\helpers\Url;
use common\models\Message;
use common\models\Order;
use common\models\Subscription;

/* @var $this yii\web\View */
/* @var $model common\models\User */
//$model = new User();

$user_id = '';
$user_id = isset($_GET['id']) ? $_GET['id']:"";
?>
<script src="https://js.stripe.com/v3/"></script>
<div class="user-view">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">User Management</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#"> Profile <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Images</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Videos</a>
                </li>
            </ul>
        </div>
    </nav>
    <section class="invoice">
      <!-- /.row -->
      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-7">
            <input type="hidden" name="user_id" id="user_id" value="<?=$user_id?>">
            <h3>Username: <strong><?= !empty($model->username) ? $model->username : '' ?></strong></h3>
            <img src="<?=!empty($model->ruserPhoto->photo_path) ? Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.$model->ruserPhoto->photo_path.'' : '../images/no-image-avail-large.jpg' ?>" style="width:300px;height:200px;">
            <ul class="user-images">
                <?php if(!empty($model->userPhoto)) { ?>
                <?php foreach ($model->userPhoto as $key => $value) {
                 ?>
                <li class="photo-id-<?=$value['photos_id']?>">
                    <a>
                        <span data-photoid="<?=$value['photos_id']?>" class="delete-user-photo"><i class="fa fa-trash" aria-hidden="true"></i></span>
                        <img src="<?=!empty($value['photo_path']) ? Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.$value['photo_path'].'' : '../images/no-image-avail-large.jpg' ?>" style="width:300px;height:200px;">
                    </a>
                </li>
                <?php }  // for each?>
                <?php } // if closed ?>
            </ul>
            <br />
            <br />
            <div class="user-video">
                <?php
                if(!empty($model->userVideo->video_url))
                {
                if(file_exists(Yii::$app->basePath.AssestsManager::UPLOAD_PATH.$model->userVideo->video_url))
                { 
                ?>
                <h4>User Video</h4>
                <video width="400" controls>
                    <source src="<?=Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.$model->userVideo->video_url?>" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>
                <?php } ?>
                <?php } ?>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-5">
            <table class="table">
                <tbody>
                    <tr>
                        <td><strong>Last Active</strong></td>
                        <td><?= !empty($model->updateddate) ? date('m/d/Y', strtotime($model->updateddate)) : ''?></td>
                    </tr>
                    <tr>
                        <td><strong>Location</strong></td>
                        <td><?= !empty($model->address) ? $model->address : ''?></td>
                    </tr>
                    <tr>
                        <td><strong>Email Address</strong></td>
                        <td><?= !empty($model->email) ? $model->email : ''?></td>
                    </tr>
                    <tr>
                        <td><strong>Date Joined</strong></td>
                        <td><?= !empty($model->createddate) ? date('m/d/Y', strtotime($model->createddate)) : ''?></td>
                    </tr>
                    <?php if(!empty($model->userMetaAll)) { ?>
                    <?php foreach ($model->userMetaAll as $key => $value) {
                    ?>
                    <tr>
                        <td><strong><?=ucfirst($value->meta_key)?></strong></td>
                        <td><?=$value->meta_value?></td>
                    </tr>
                    <?php } // for each closed ?>
                    <?php } // if closed ?>
                    <tr>
                        <td><strong>User Premium Status</strong></td>
                        <td>
                            <?php if(empty($model->plan)) { ?>
                            <div class="form-group">
                                <select class="form-control" name="stripePlans" id="stripePlans">
                                    <option value="">select plans</option>
                                    <?php if(!empty($stripePlans->data)) { ?>
                                    <?php foreach ($stripePlans->data as $key => $value) {?>
                                    <option value="<?=$value->id?>-<?=$value->interval_count?>"><?=$value->interval_count?> <?=$value->interval?></option>
                                    <?php } // for each closed ?>
                                    <?php } else {?>
                                    <option value="">No plans</option>
                                    <?php }?>
                                </select>
                            </div>
                            <?php } else {  ?>
                               <?=$model->plan->months;?> Months membership plan
                            <?php }?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <form action="/charge" method="post" id="payment-form">
            <div class="form-row">
              <label for="card-element">
                Credit or debit card
              </label>
              <div id="stripe-card-element">
                    <!-- a Stripe Element will be inserted here. -->
              </div>

              <!-- Used to display form errors -->
              <div id="card-errors" role="alert"></div>
            </div>
            <div class="loader custom-loader"></div>
            <button type="submit" class="subscribe-btn btn btn-primary">Make Premium</button>
            
          </form>
            <div class="alert alert-danger" id="planError">
                Please select a plan
            </div>
        </div>
      </div>
            <!-- /.row -->      
    </section> 
    
    <section class="content">
        <div class="custom-center-class">
        <?php if($model->is_active == User::$IS_ACTIVE) { ?>
            <button data-user-status="<?=User::$IN_ACTIVE?>" id="deactivate-user" data-user-id="<?=$model->id?>" class="deactivate-user btn btn-primary btn btn-danger">DEACTIVATE USER</button>
            <button data-user-status="<?=User::$IS_ACTIVE?>" style="display: none;" id="activate-user" data-user-id="<?=$model->id?>" class="deactivate-user btn btn-primary btn btn-success">ACTIVATE USER</button>
        <?php } ?>
        <?php if($model->is_active == User::$IN_ACTIVE) { ?>
            <button data-user-status="<?=User::$IS_ACTIVE?>"  id="activate-user" data-user-id="<?=$model->id?>" class="deactivate-user btn btn-primary btn btn-success">ACTIVATE USER</button>
            <button data-user-status="<?=User::$IN_ACTIVE?>" style="display: none;"  id="deactivate-user" data-user-id="<?=$model->id?>" class="deactivate-user btn btn-primary btn btn-danger">DEACTIVATE USER</button>
        <?php } ?>
        </div>    
        <!-- /.row -->
    </section>   
    
</div>
