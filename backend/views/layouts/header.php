<?php
use yii\helpers\Html;
use common\models\Notification;
use common\models\Message;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
$notify = Notification::find()->count();
$msg = Message::find()->count();
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">'. Yii::$app->params['projectname'].'</span><span class="logo-lg">' . Yii::$app->params['projectname'] . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <?php
                    if(Yii::$app->user->getId()){
                ?>
                   <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="<?=Url::to(['message/index'])?>" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success"><?=$msg?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have <?=$msg?> Messages</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li>
                    <a href="<?=Url::to(['message/index'])?>">
                      <i class="fa fa-users text-aqua"></i> <?=$msg?> Messages
                    </a>
                  </li>
                </ul> 
               </li>
            </ul> 
           </li>
                 <!-- end message -->
                 <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning"><?=$notify?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have <?=$notify?> notifications</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li>
                    <a href="<?=Url::to(['manage-notification/index'])?>">
                      <i class="fa fa-users text-aqua"></i> <?=$notify?> Friend Request
                    </a>
                  </li>
                  <!-- <li>
                    <a href="#">
                      <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                      page and may cause design problems
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-red"></i> 5 new members joined
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-user text-red"></i> You changed your username
                    </a>
                  </li> -->
                </ul>
              </li>
              <!-- <li class="footer"><a href="#">View all</a></li> -->
            </ul>
          </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/images/veedater.png" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= Yii::$app->user->identity->username?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $directoryAsset ?>/images/veedater.png" class="img-circle"
                                 alt="User Image"/>
                            <p>
                                <?= Yii::$app->user->identity->username ?>
                                <small>Member since <?php  echo @Yii::$app->user->identity->createddate;?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
                
                <?php } ?>

                <!-- User Account: style can be found in dropdown.less -->
                <!--<li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>-->
            </ul>
        </div>
    </nav>
</header>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
        </div>

        <div class="modal-body">
            <p>You are about to delete one track, this procedure is irreversible.</p>
            <p>Do you want to proceed?</p>
            <p class="debug-url"></p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a class="btn btn-danger btn-ok">Delete</a>
        </div>
    </div>
</div>
</div>
