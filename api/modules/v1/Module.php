<?php
namespace api\modules\v1;

use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use filsh\yii2\oauth2server\Server;

/**
 * restful module definition class
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = true;
    }
    
    public function beforeAction($action) {   
        return parent::beforeAction($action);
    }
    
    


}
