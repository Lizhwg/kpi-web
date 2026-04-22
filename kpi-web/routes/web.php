<?php

return [
    'GET' => [
        '/' => 'AuthController@dashboard',
        '/login' => 'AuthController@login',
        '/logout' => 'AuthController@logout', // KHAI BÁO ĐỂ HẾT LỖI 404
        '/register' => 'AuthController@registerForm',
        '/dashboard' => 'AuthController@dashboard',
        '/mapping/review' => 'MappingController@review',
        '/mapping/detail' => 'MappingController@detail',
    ],
    'POST' => [
        '/login' => 'AuthController@handleLogin',
        '/register' => 'AuthController@handleRegister',
        '/mapping/review-approve' => 'MappingController@reviewApprove', 
    '/mapping/review-edit' => 'MappingController@reviewEdit',
    ]
];
