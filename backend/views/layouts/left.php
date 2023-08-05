<aside class="main-sidebar">

    <section class="sidebar">
        <?php if (Yii::$app->user->getId()) { ?>
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= $directoryAsset ?>/images/veedater.png" class="img-circle" alt="User Image"/>
                </div>
                <div class="pull-left info">
                    <p><?= ucfirst(Yii::$app->user->identity->username) ?></p>
                </div>
            </div>
        <?php } ?>
        <ul class="sidebar-menu">
            <?php if(Yii::$app->request->pathInfo == 'site/index' || Yii::$app->request->pathInfo == '')
                {
                    $class_site = "active";
                }
                else
                {
                    $class_site = "";
                }
                ?>
            <li class="<?=$class_site?>">
                <a href="<?=\yii\helpers\Url::to(['/site/index'], true)?>">
                    <i class="fa fa-dashboard"></i>  <span>Dashboard</span>
                </a>
            </li>
         <?php if(Yii::$app->request->pathInfo == 'user/index' 
                 || Yii::$app->request->pathInfo == 'user/view'
                 || Yii::$app->request->pathInfo == 'user/refund')
                {
                    $class_user = "active";
                }
                else
                {
                    $class_user = "";
                }
                ?>   
        <li class="<?=$class_user?>">
            <a  href="<?=\yii\helpers\Url::to(['/user/index'], true)?>">
                <i class="fa fa-users"></i>  <span>User Management</span>
            </a>
        </li>
        <?php if(Yii::$app->request->pathInfo == 'message/index' || Yii::$app->request->pathInfo == 'message/view')
                {
                    $class_message = "active";
                }
                else
                {
                    $class_message = "";
                }
                ?> 
             <li class="<?=$class_message?>">
                <a href="<?=\yii\helpers\Url::to(['/message/index'], true)?>">
                    <i class="fa fa-envelope-o"></i>  <span>Chats</span>
                </a>
            </li>    
            <?php if(Yii::$app->request->pathInfo == 'manage-video/index' || Yii::$app->request->pathInfo == 'manage-video/view')
                {
                    $class_video = "active";
                }
                else
                {
                    $class_video = "";
                }
                ?> 
             <li class="<?=$class_video?>">
                <a href="<?=\yii\helpers\Url::to(['/manage-video/index'], true)?>">
                    <i class="fa fa-video-camera"></i>  <span>Video</span>
                </a>
            </li>    
            <?php if(Yii::$app->request->pathInfo == 'manage-image/index' || Yii::$app->request->pathInfo == 'manage-image/view')
                {
                    $class_image = "active";
                }
                else
                {
                    $class_image = "";
                }
                ?> 
             <li class="<?=$class_image?>">
                <a href="<?=\yii\helpers\Url::to(['/manage-image/index'], true)?>">
                    <i class="fa fa-image"></i>  <span>Images</span>
                </a>
            </li>       
            <?php if(Yii::$app->request->pathInfo == 'manage-notification/index' || Yii::$app->request->pathInfo == 'manage-notification/view')
                {
                    $class_image = "active";
                }
                else
                {
                    $class_image = "";
                }
                ?> 
             <li class="<?=$class_image?>">
                <a href="<?=\yii\helpers\Url::to(['/manage-notification/index'], true)?>">
                    <i class="far fa-comments"></i>  <span>Notifications</span>
                </a>
            </li>       
            <?php if(Yii::$app->request->pathInfo == 'manage-subscription/index' || Yii::$app->request->pathInfo == 'manage-order/view')
                {
                    $class_image = "active";
                }
                else
                {
                    $class_image = "";
                }
                ?> 
             <li class="<?=$class_image?>">
                <a href="<?=\yii\helpers\Url::to(['/manage-subscription/index'], true)?>">
                    <i class="fa fa-shopping-cart"></i>  <span>Subscriptions</span>
                </a>
            </li>       
        </ul>
    </section>
</aside>
