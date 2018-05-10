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
    'status' => 'site/status',
    'GET v1/verify/token' => 'site/verify-token',
    'OPTIONS v1/verify/token' => 'site/options',
];
