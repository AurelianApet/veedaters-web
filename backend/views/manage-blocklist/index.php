<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\helpers\AssestsManager;
use yii\helpers\VarDumper;
use common\models\Blocklist;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = $this->title;
$block = Blocklist::find()->all();
//Vardumper::dump($notify->user_id);     
?>

<div id="notification">
<h4>Block List</h4>
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
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="From" >Blocked User</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="To" >Blocked By</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Time" >Blocked At</th>
                </tr>
                </thead>
                <tbody>
                <?php
              
                if(!empty($block))
                {
                foreach ($block as $value) {      
                //   Vardumper::dump($value->user_id);exit();
                ?>
                    <tr role="row" class="even">
                        <td class="sorting_1">
                            <?=!empty($value->blocked_id) ?$value->blockedUser->name : '';?>
                        </td>
                        <td class="sorting_1">     
                            <?=!empty($value->blocked_by_id) ?$value->blockedByUser->name : '';?>
                        </td>
                        <td class="sorting_1">
                            <?=!empty($value->created_at) ? date('h:i:s a', strtotime($value->created_at)) : '';?>
                        </td>
                    </tr>
                <?php 
                } // for closed
                } else { ?>
                    <tr role="row" class="even">
                        <td></td>
                        <td>No user blocked</td>
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