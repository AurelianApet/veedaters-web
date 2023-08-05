<?php

namespace backend\controllers;

use Yii;
use common\models\Message;
use common\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\MessageRecipient;
use yii\filters\VerbFilter;

class ManageSubscriptionController extends Controller
{
      public function actionIndex()
    {
        return $this->render('index');
    }
} 
