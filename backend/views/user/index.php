<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index user-management">

    
    <div class="row custom-user-filer">
        <div class="col-lg-4">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-lg-4">
            <form method="GET"> 
            <input 
                placeholder="Search" 
                class="form-control" 
                name="UserSearch[username]" 
                type="text"
                value="<?=isset($_GET['UserSearch']['username']) ? $_GET['UserSearch']['username']:''?>"
                >
            <input type="hidden" name="submit" id="submit" />
            </form>
        </div> 
        <div class="col-lg-4">
            <form method="GET" class="user-manage-filter-form" name="user-manage-filter-form" id="user-manage-filter-form">
                <select name="sort" class="form-control" id='user_manage_filter' >
                    <option value="">All</option>                    
                    <option value="username" <?=isset($_GET['sort']) && $_GET['sort']=='username' ? "selected=selected":""?>>A-Z</option>
                    <option value="-username" <?=isset($_GET['sort']) && $_GET['sort']=='-username' ? "selected=selected":""?>>Z-A</option>
                    <!-- <option value="createddate" <?=isset($_GET['sort']) && $_GET['sort']=='createddate' ? "selected=selected":""?>>Newest</option>
                    <option value="-createddate" <?=isset($_GET['sort']) && $_GET['sort']=='-createddate' ? "selected=selected":""?>>Oldest</option> -->
                </select>    
            </form>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           
            [
                'label' => 'Username',
                'attribute' => 'username',
                'format' => 'html',
                'value' => function($data) {
                    return "<a href='" . \yii\helpers\Url::to(['/user/view', 'id' => $data->id], true) . "'>".$data->username."</a>";
                }
            ],
             'name',
            // 'is_active',
            // 'createdby',
            // 'updatedby',
            // 'updateddate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
