<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Message;
use yii\data\Pagination; 
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\helpers\AssestsManager;
use yii\helpers\VarDumper;



/* @var $this yii\web\View */
/* @var $model backend\models\Message */


$id = '';
$id = isset($_GET['id']) ? $_GET['id']:"";
$new_msg = Message::find()->andWhere(['=','message_creator_id',$id])->count();
?>
 <?php $message = Message::find()->addSelect(['*'])->andWhere(['=','message_creator_id',$id])->all();
$count = count($message);
$this->title = 'Direct Chat';
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; 
?>
<div class="message-view">
        
    <!-- DIRECT CHAT -->
    <div class="box box-warning direct-chat direct-chat-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

            <div class="box-tools pull-right">
            <span data-toggle="tooltip" title="<?=$new_msg?> New Messages" class="badge bg-yellow"><?=$new_msg?></span>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <!-- <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts"
                    data-widget="chat-pane-toggle">
                <i class="fa fa-comments"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button> -->
            </div>
        </div>

        <!-- /.box-header -->

        <div class="box-body">

            <!-- Conversations are loaded here -->
        
            <div class="direct-chat-messages">
            <?php if(!empty($message))
                {
                foreach ($message as $key => $value) {      
                    // Vardumper::dump($value->createddate);exit();
                ?>
            <!-- Message. Default to the left -->
            <div class="direct-chat-msg">
                <div class="direct-chat-info clearfix">
                <span class="direct-chat-name pull-left"> <?php if($value->user) { ?>    
                                    <a href="<?=\yii\helpers\Url::to(['/user/view', 'id' => $value->user->id], true)?>"><?=!empty($value->user->username) ? $value->user->name : '';?></a>
                                <?php } ?>   </span>
                <span class="direct-chat-timestamp pull-right"> <?=!empty($value->createddate) ? date('m/d/Y', strtotime($value->createddate)) : '';?>  <?=!empty($value->createddate) ? date('h:i:s a', strtotime($value->createddate)) : '';?></span>
                </div>
                <!-- /.direct-chat-info -->
                <img class="direct-chat-img" src="<?=!empty($value->photos->photo_path)?Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.'user/'.$value->user->id.'/'.$value->photos->photo_path:''?>"
        alt="">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text"><?php //Vardumper::dump($value->reciever->id);exit(); ?>
                <?=!empty($value->message_body) ? implode(' ', array_slice(explode(' ', $value->message_body), 0, 140)) : '';?>
                <?= !empty($value->message_id)?Html::a('x', ['delete', 'id' => $value->message_id], [
            'class' => '','style'=>'float:right',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]):'' ?>
                </div>
                <!-- /.direct-chat-text -->
            </div>
            <!-- /.direct-chat-msg -->
            
            <!-- Message to the right -->
            <?php if(!empty($value->resmsg->message_id) && $value->resmsg->message_id>$key+1){?>
            <div class="direct-chat-msg right">
                <div class="direct-chat-info clearfix">
                <span class="direct-chat-name pull-right"><?php if($value->reciever) { ?>    
                                    <a href="<?=\yii\helpers\Url::to(['/user/view', 'id' => $value->reciever->id], true)?>"><?=!empty($value->reciever->username) ? $value->reciever->name : '';?></a>
                                <?php } ?></span>
                <span class="direct-chat-timestamp pull-left"><?=!empty($value->resmsg->createddate) ? date('m/d/Y', strtotime($value->resmsg->createddate)) : '';?>  <?=!empty($value->resmsg->createddate) ? date('h:i:s a', strtotime($value->createddate)) : '';?></span>
                </div>
                <!-- /.direct-chat-info -->
                <img class="direct-chat-img" src="<?=Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.'user/'.$value->reciever->id.'/'.$value->resphotos->photo_path?>" alt="message user image">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                <?=!empty($value->resmsg->message_id)? implode(' ', array_slice(explode(' ', $value->resmsg->message_body), 0, 140)) : '';?> 
                <?= !empty($value->resmsg->message_id)?Html::a('x', ['delete', 'id' => $value->resmsg->message_id], [
                    'class' => '','style'=>'float:right',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]):'' ?>
                </div> 
                <!-- /.direct-chat-text -->
            </div>
            <?php 
            }
                }
            }  ?>
            <!-- /.direct-chat-msg -->
        </div>
            <!--/.direct-chat-messages-->
        </div> 
        
    </div>
</div>
