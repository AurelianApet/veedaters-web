<?php
use \common\models\User;
use common\models\Videos;
use common\models\Images;
use common\models\Message;
use common\models\Notification;
use common\models\Subscription;
use \common\models\Blocklist;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '';

$users = User::find()->count();
$video = Videos::find()->count();
$images = Images::find()->count();
$chat = Message::find()->count();
$notifications = Notification::find()->count();
$subs = Subscription::find()->count();
$blocklist = Blocklist::find()->count(); 

//c($users); die;
?>
<div class="site-index">
    <div class="body-content">
           <section class="content-header">
            <h1> Dashboard</h1>
           </section>
        <section class="content">
            <div class="row">
              <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?=Url::to(['user/index'])?>">
                    <div class="info-box">
                      <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Users</span>
                        <span class="info-box-number"><?=$users?></span>
                      </div>
                    </div>
                </a>      
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <div class="col-md-3 col-sm-6 col-xs-12">
               <a href="<?=Url::to(['manage-video/index'])?>">   
               <!-- <a href="../manage-shop/index">  -->
               <div class="info-box">
                  <span class="info-box-icon bg-yellow"><i class="fa fa-video-camera"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Videos</span>
                    <span class="info-box-number"><?=$video?></span> (+0 new)
                  </div>
                  <!-- /.info-box-content -->
                </div>
               </a>    
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?=Url::to(['manage-image/index'])?>">     
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-image"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Images</span>
                    <span class="info-box-number"><?=$images; ?></span> (+0 new)
                  </div>
                </div>
                </a>    
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?=Url::to(['message/index'])?>">  
                <div class="info-box">
                  <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Chat</span>
                    <span class="info-box-number"><?=$chat?></span>
                  </div>
                </div>
                </a>    
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- Small boxes (Stat box) -->
            <div class="row">
              <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3><?=$subs?></h3>
                    <p>Subscriptions</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                  </div>
                  <a href="<?=Url::to(['manage-subscription/index'])?>" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                  </a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                  <div class="inner">
                    <h3>0</h3>
                    <p>Flags</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-flag"></i>
                  </div>
                  <a href="#" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                  </a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                  <div class="inner">
                    <h3><?=$blocklist?></h3>
                    <p>Blocks</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-ban"></i>
                  </div>
                  <a href="<?=Url::to(['manage-blocklist/index'])?>" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                  </a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3><?=$notifications?></h3>

                    <p>Notifications</p>
                  </div>
                  <div class="icon">
                    <i class="far fa-comments"></i>
                  </div>
                  <a href="<?=Url::to(['manage-notification/index'])?>" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                  </a>
                </div>
              </div>
              <!-- ./col -->
            </div>
            <!-- /.row -->
          <div>
      </div></section>    
    </div>
</div>
