<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Videos;
use yii\widgets\DetailView;
use common\models\Images;
use common\models\User;
use common\helpers\AssestsManager;
use yii\helpers\VarDumper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'VIDEOS';
$this->params['breadcrumbs'][] = $this->title;
$video = Videos::find()->addSelect(['video_url','userid'])->all();         
     
?>

 <div id="video">
    <h4>User Videos</h4>
    <?php foreach($video as $key=>$values){
       // VarDumper::dump($values->userid); exit();     ?>
    <div class="col-sm-3 user-video">
        <?php     
        if(!empty($values->video_url))
        { 
        ?>
        <video width="400" controls>
            
            <source src="<?=Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.'user/'.$values->userid.'/'.$values->video_url?>" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        <?php } ?> 
    </div>
    <?php } ?>
</div>