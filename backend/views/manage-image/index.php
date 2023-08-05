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

$this->title = 'IMAGES';
$this->params['breadcrumbs'][] = $this->title;
$image = Images::find()->addSelect(['image_url','userid'])->all();         
     
?>

<div id="image">
<h4>User Images</h4>
    <?php foreach($image as $key=>$values){
      // VarDumper::dump($values->userid); exit();     ?>
    <div class="col-sm-3 user-image">
        <?php     
        if(!empty($values->image_url))
        { 
        ?>
            <img src="<?=Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.'user/'.$values->userid.'/'.$values->image_url?>" style="width:300px;height:200px;"/>
        <?php } ?>
    </div>
    <?php } ?> 
</div>