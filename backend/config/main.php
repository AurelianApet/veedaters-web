<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'video_gallery' => [
            'class' => 'wolfguard\video_gallery\Module',
    ]],
    
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'enableCookieValidation' => true,
            'cookieValidationKey' => '123',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => false],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'urlManagerBackend' => [
            'class' => 'yii\web\urlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => 'http://upload.veedater.com',
        ],
        'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AAAAuxLsv2w:APA91bGZ9xg9s07mfZBJ8hMFdBYm57F_4GyiJTcPso9YoIxeTEOKPVxijCtg0hoGDvd9BtIztwfs8JKDEdA6bXnBnPikSpPtW_Wja6QKYYHT9nCKuQvzpDWG8dZD9LIEZfEFlEQ-hfhD' // Server API Key (you can get it here: https://firebase.google.com/docs/server/setup#prerequisites) 
        ],
    ],
    'params' => $params,
];
