<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\helpers\AssestsManager;
use yii\helpers\VarDumper;
use common\models\Subscription;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = $this->title;
$subs = Subscription::find()->all();
//Vardumper::dump($notify->user_id);     
?>

<div id="notification">
<h4>Subscribers</h4>
    <div class="col-sm-3 notify">
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
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="From" >Subscriber</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="To" >Plan</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Time" >Subscription Date</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Time" >Expiry Date</th>
                </tr>
                </thead>
                <tbody>
                <?php
              
                if(!empty($subs))
                {
                foreach ($subs as $value) {      
                //   Vardumper::dump($value->user_id);exit();
                ?>
                    <tr role="row" class="even">
                        <td class="sorting_1">
                            <?=!empty($value->user->name) ?$value->user->name : '';?>
                        </td>
                        <td class="sorting_1">     
                            <?=!empty($value->months) ?$value->months.' Months' : '';?>
                        </td>
                        <td class="sorting_1">
                            <?=!empty($value->createddate) ? date('h:i:s a', strtotime($value->createddate)) : '';?>
                        </td>
                        <td class="sorting_1">
                            <?=!empty($value->expires_on) ? date('h:i:s a', strtotime($value->expires_on)) : '';?>
                        </td>
                    </tr>
                <?php 
                } // for closed
                } else { ?>
                    <tr role="row" class="even">
                        <td></td>
                        <td>No one subscribed yet.</td>
                        <td></td>
                    </tr>
                <?php }?>                    
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
    </div>
</div>