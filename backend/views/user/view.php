<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Videos;
use common\models\Images;
use common\models\User;
use common\helpers\AssestsManager;
use common\models\Product;
use yii\helpers\Url;
use common\models\Message;
use common\models\Order;
use common\models\Subscription;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
$videomodel = new Videos;
$imgmodel = new Images;
$user_id = '';
$user_id = isset($_GET['id']) ? $_GET['id']:"";
?>
<script src="https://js.stripe.com/v3/"></script>
<div class="user-view">
<div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#activity" data-toggle="tab">Profile</a></li>
            <li><a href="#images" data-toggle="tab">Images</a></li>
            <li><a href="#video" data-toggle="tab">Video</a></li>
            <li><a href="#preferences" data-toggle="tab">Preferences</a></li>
        </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <!-- Profile -->
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
                            <?php if(empty($model->plan)) { //Vardumper::dump($model->plan);exit();?>
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
                <!-- /.Profile -->
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="images">
              <h4>User Images</h4>
             <?php $images = Images::find()->addSelect(['image_url'])->andWhere(['userid' => Yii::$app->getRequest()->getQueryParam('id')])->all();       
                foreach($images as $key=>$values) { ?>
                <div class="user-images col-sm-4">
                    
                        <?php 
                        //VarDumper::dump();exit();
                        if(!empty($values))
                        {
                        ?>
                        <div width="200">
                        <img src="<?=Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.'user/'.Yii::$app->getRequest()->getQueryParam('id').'/'.$values->image_url ?>" style="width:300px;height:200px;">
                        </div>
                        <?php } ?>
                </div> <?php } ?>
              </div>
              <!-- /.tab-pane -->
            <!-- VarDumper::dump(Yii::$app->urlManagerBackend->baseUrl.AssestsManager::UPLOAD_PATH.'user/'.Yii::$app->user->getId().'/'.$video['video_url']); -->
              <div class="tab-pane" id="video">
              <h4>User Video</h4>
                <div class="user-video">
                        <?php
                        $video = Videos::find()->addSelect(['*'])->andWhere(['userid' => Yii::$app->getRequest()->getQueryParam('id')])->one();      
                        if(!empty($video['video_url']))
                        { 
                        ?>
                        <video width="400" controls>
                            
                            <source src="<?=Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.'user/'.Yii::$app->getRequest()->getQueryParam('id').'/'.$video['video_url']?>" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <?php } ?>
                       
                </div>
              </div>
              <div class="tab-pane" id="preferences">
                    <h4>Preferences</h4>
                        <div class="preferences">
                        <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>Field</th>
                            <th>Preference</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><span class="">Gender</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->gender)?$model->userPreferences->gender:'' ?></span></td>
                        </tr> 
                        <tr>  
                            <td><span class="">Minimum Age</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->min_age)?$model->userPreferences->min_age:'' ?></span></td>

                        </tr>  
                        <tr> 
                            <td><span class="">Maximum Age</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->max_age)?$model->userPreferences->max_age:'' ?></span></td>

                        </tr>  
                        <tr>  
                            <td><span class="">Distance</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->distance)?$model->userPreferences->distance:'' ?></span></td>

                        </tr>  
                        <tr>
                            <td><span class="">Religion</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->religion)?$model->userPreferences->religion:'' ?></span></td>

                        </tr> 
                        <tr>
                            <td><span class="">Sports</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->sports)?$model->userPreferences->sports:'' ?></span></td>

                        </tr>
                        <tr> 
                            <td><span class="">Minimum Income</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->min_income)?$model->userPreferences->min_income:'' ?></span></td>

                        </tr>
                        <tr>
                            <td><span class="">Maximum Income</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->max_income)?$model->userPreferences->max_income:'' ?></span></td>

                        </tr> 
                        <tr> 
                            <td><span class="">Style</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->style)?$model->userPreferences->style:'' ?></span></td>

                        </tr> 
                        <tr>
                            <td><span class="">Alcohol</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->alchohol)?$model->userPreferences->alchohol:'' ?></span></td>

                        </tr>
                        <tr>
                            <td><span class="">Smoke</span></td>
                            <td><span class="label label-success"><?= !empty($model->userPreferences->smoke)?$model->userPreferences->smoke:'' ?></span></td>

                        </tr>
                        <tr>
                        <td><span class="">Tatoo</span></span></td>
                        <td><span class="label label-success"><?= !empty($model->userPreferences->tatoo)?$model->userPreferences->tatoo:'' ?></span></td>
                        </tr>

                        </tbody>
                        </table>
                    </div>
              <!-- /.table-responsive -->
                </div>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->     
        </div>       
</div>

