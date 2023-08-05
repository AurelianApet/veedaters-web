<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'projectname' => 'Veedater App',
    'projecturl' => 'http://localhost/veedater/backend/web/',
    'stripeTestPrivateKey' => 'sk_test_ha1yafp3BUbBzmuc1u4jqTCJ',
    'stripeTestPublicKey' => 'pk_test_tZGIOK0ezwLGPerGW5xnKOmP',
    'stripeLivePrivateKey' => 'sk_live_ZMAT1uHFGAXAo3uAOvU0F1PI',
    'stripeLivePublicKey' => 'pk_live_XoLVgXQfnK1L7wrP2KrVXjVz',
    'user.passwordResetTokenExpire' => 3600,
    'message_templates' => [
        'welcome_msg' => [
            'text' => 'Hey there,​ welcome to the Veedater family.Say ​hello, ​shoot ​us ​a ​message ​anytime!',
            'title' => ''
        ],
        'message_recieved' => [
            'text' => '{USERNAME} has sent You a new message!',
            'title' => ''
        ],
    ]
];
