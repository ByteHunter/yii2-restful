<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'controller' => [
            'v1/user',
        ],
        'extraPatterns' => [
            'GET per-page/<per-page>/page/<page>' => 'index',
            'GET page/<page>' => 'index',
            'GET <keys:keys>' => 'index',
        ],
    ],
];
