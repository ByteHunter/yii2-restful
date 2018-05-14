<?php
return [
    [
        'class' => 'api\common\components\UrlRule',
        'controller' => 'v1/user',
        'extraPatterns' => [
            'OPTIONS login' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'only' => ['options'],
        'suffix' => '/',
        'controller' => [
            'v1/user',
        ],
    ],
    'OPTIONS v1/verify/token' => 'site/options',
    "OPTIONS status" => "site/options",
    "OPTIONS options" => "site/options",
    "OPTIONS /" => "site/options",
    'GET status' => 'site/status',
    'GET v1/verify/token' => 'site/verify-token',
];
