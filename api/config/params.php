<?php
return [
    'adminEmail' => 'admin@example.com',
    'api_methods' => [
        'user' => [
            'signup' => [
                'description' => 'api method to signup as an email user on veedater',
                'status' => 'active',
                'params_prefix' => 'User',
                'required_params' => ['(string) email','(string) username','(string) password'],
                'response_body' => ['is_success' => ['type' => 'boolean'],'user_id' => ['type' => 'integer']]
            ],
            'social-signup' => [
                'description' => 'method for signing up into veedater via social API (facebook/tweeter/google). NOTE: this method is moved to \'user/login\'',
                'status' => 'depricated',
                'params_prefix' => 'User',
                'required_params' => ['(int) social_id', '(string) social_media_type', '(string) name','(string) email'],
            ],
            'social-login' => [
                'description' => 'method for logging into veedater via social API (facebook/tweeter/google). NOTE: this method is moved to \'user/login\'',
                'status' => 'depricated',
                'params_prefix' => 'User',
                'required_params' => ['(int) social_id', '(string) social_media_type'],
            ],
            'login' => [
                'description' => 'method for login to veedater app. Same method is used for to login via facebook/tweeter',
                'status' => 'active',
                'params_prefix' => 'User',
                'required_params' => ['(string) social_media_type', '(string) username', '(string) password'],
                
            ],
            'user-detail' => [
                'description' => 'method used for getting detail of single user. Returns a user object data',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => ['(int) id'],
                
            ],
            'forgotpassword' => [
                'description' => 'method for sending the reset password link to email if registered on the platform',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => ['(string) email'],
            ],
            'get-profile' => [
                'description' => 'returns the details of a current user including metadata',
                'status' => 'active',
                'params_prefix' => 'User',
                'required_params' => [],
            ],
            'list' => [
                'description' => 'returns the list of all users excluding the current and blocked users. This method accepts parameters for filtering list',
                'status' => 'active',
                'params_prefix' => 'User',
                'required_params' => [],
                'optional_params' => [
                    'distance' => '(string) eg: 0-20/10-30',
                    'gender' => '(string) male/female',
                    'age' => '(string) 10-30/18-45',
                    'lat' => '(string) 35.649',
                    'lng' => '(string) 76.649'
                    ]
            ],
            'favlist' => [
                'description' => 'returns the list of users that the current user has liked',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => [],
            ],
            'video' => [
                'description' => 'returns the video object of the current user',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => [],
            ],
            'profile-update' => [
                'description' => 'method used to update the current user details including the metadata',
                'status' => 'active',                
                'update_multiple_models' => 'yes',
                'params_prefix' => [
                    'User' => [
                        'required_params' => [],
                        'optional_params' => ['(string) address', '(string) name', '(FILE) user_photo', '(int) photo_id (used for delete photo by id)']
                    ],
                    'UserMeta' => [
                        'required_params' => [],
                        'optional_params' => [
                            '(string) ' . REL_USER_ABOUT,
                            '(string) ' . REL_USER_DOB,
                            '(string) ' . REL_USER_STATUS,
                            '(string) ' . REL_USER_GENDER,
                            '(string) ' . REL_USER_RELIGION,
                            '(string) ' . REL_USER_NATION,
                            '(string) ' . REL_USER_SPORT,
                            '(string) ' . REL_USER_TRAVEL,
                            '(string) ' . REL_USER_INCOME,
                            '(string) ' . REL_USER_STYLE,
                            '(string) ' . REL_USER_SMOKE,
                            '(string) ' . REL_USER_BEER,
                            '(string) ' . REL_USER_LAT,
                            '(string) ' . REL_USER_LNG
                        ]
                    ],
                ]
            ],
            'review' => [
                'description' => 'method used to add a like rating to a user. Sets a rating like to on a specific user by the current user ',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => ['(int) review', 'user_id'],
            ],
            'block' => [
                'description' => 'method for blocking a user.',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => ['(int) user_id'],
            ],
            'unblock' => [
                'description' => 'unblocks a single|multiple user/s.',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => ['(array)|(int) user_id'],
            ],
            'block-list' => [
                'description' => 'returns the list of all blocked user by the current user',
                'status' => 'active',                
                'params_prefix' => 'User',
                'required_params' => [],
            ]
        ]
    ]
];
