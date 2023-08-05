<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ]
    ],
	'modules' => [
        'admin' => [
            'class' => 'app\rest\Module',
        ],
    ],       
];
