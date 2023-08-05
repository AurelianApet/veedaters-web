<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Message;
use \common\models\UserRoleMap;
use yii\widgets\LinkPager;
use yii\data\Pagination; 
use common\models\BlockList;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;

$admin_id = UserRoleMap::find()->andWhere(['user_role_id' => 'REL_ROLE_USER'])->one();
// $message = Message::find()
//             ->alias('uM')
//             ->addSelect(['uM.message_creator_id','uM.is_active','uM.message_body','uM.createddate','uM.createddate'])
//             //->andWhere(['!=','message_creator_id',$admin_id['user_id']])
//             ->joinWith(['user'],true,'RIGHT JOIN') 
//             ->joinWith(['conversation mC' => function($q){
//                 $q->andWhere(['mC.recipient_id' => Yii::$app->user->getId()]);
//             }],true,'RIGHT JOIN');
            //->all();
            //echo $message->createCommand()->rawSql;exit;
$message = Message::find()->addSelect(['*'])->andWhere(['=','message_parent_id',0])
->joinWith(['user'],true,'RIGHT JOIN')->all();
    
//VarDumper::dump($message); exit();
$count = count($message);
          
$pagination = new Pagination(['totalCount' => $count]);
// VarDumper::dump($pagination);exit();
// limit the query using the pagination and retrieve the articles
// $articles = $message->offset($pagination->offset)
//     ->limit($pagination->limit)
//     ->all();         
            
?>
<div class="message-index">
    <!-- <h1><?= Html::encode($this->title) ?></h1> 
    <a onclick="sendMessageFunction()" class="send-message" href="#">Compose a message</a>  
    <div class="alert alert-success" id="message-sent-success">
        <strong>Success!</strong> message sent
    </div>
    <section class="content send_message_container">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
                  <label for="user_id">Search User:</label>
                  <input placeholder="search user" type="text" class="form-control" id="search_user_field">
                  <div class="frmSearch">
                      <div id="suggesstion-box"></div>
                   </div>
                </div>
                <div class="form-group">
                  <label >Write message:</label>
                  <textarea placeholder="write message here..." class="form-control" id="message"></textarea>
                </div>
                <button onclick="sendMessage()"  class="btn btn-default btn-success">Send</button>
                <button  class="btn btn-default ">cancel</button>
            </div>
        </div>
        <input type="hidden" name="sender_id" id="sender_id">
    </section> -->

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
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
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Vendor" >Time</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Location" >User</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Location" >Message</th>
                </tr>
                </thead>
                <tbody>
                <?php
              
                if(!empty($message))
                {
                foreach ($message as $key => $value) {      
                  // Vardumper::dump($value->createddate);exit();
                ?>
                    <tr role="row" class="even">
                        <td class="sorting_1">
                            <?=!empty($value->createddate) ? date('m/d/Y', strtotime($value->createddate)) : '';?>
                        </td>
                        <td class="sorting_1">
                            <?=!empty($value->createddate) ? date('h:i:s a', strtotime($value->createddate)) : '';?>
                        </td>
                        <td>
                        <?php if($value->user) { ?>    
                            <a href="<?=\yii\helpers\Url::to(['/user/view', 'id' => $value->user->id], true)?>"><?=!empty($value->user->username) ? $value->user->username : '';?></a>
                        <?php } ?>   
                        </td>
                        <td>
                          
                            <?=!empty($value->message_body) ? implode(' ', array_slice(explode(' ', $value->message_body), 0, 20)) : '';?>
                        </td>
                        <td>
                            <a href="<?=\yii\helpers\Url::to(['/message/view', 'id' => $value->user->id], true)?>" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>
                    </tr>
                <?php 
                } // for closed
                } else { ?>
                    <tr role="row" class="even">
                        <td></td>
                        <td>No message found</td>
                        <td></td>
                    </tr>
                <?php }?>                    
                </tbody>
              </table></div></div></div>
                <?php 
echo LinkPager::widget([
    'pagination' => $pagination,
]);
?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      
</section>
</div>
