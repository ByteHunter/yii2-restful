<?php
return [
    [
        'class' => 'api\common\components\UrlRule',
        'controller' => 'v1/user',
        'extraPatterns' => [
            'OPTIONS login' => 'options',
        ],
    ],
    'status' => 'site/status',
    'GET verify/token' => 'site/verify-token',
    'OPTIONS verify/token' => 'site/options',
];
