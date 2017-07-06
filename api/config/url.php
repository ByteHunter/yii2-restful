<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'controller' => [
            'v1/user',
            // Add controllers here
        ],
        'extraPatterns' => [
            'GET per-page/<per-page>/page/<page>' => 'index',
            'GET page/<page>' => 'index',
            'GET <keys:keys>' => 'index',
        ],
    ],
    // Example of compound primary key
    /*[
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'controller' => 'v1/compound-model',
        'extraPatterns' => [
            'GET test' => 'compund-model/test',
        ],
        'tokens' => [
            //'{id}' => '<id:\\d+,\\w+>',
            //'{id}' => '<id:[\\w\\-]+>',
            //'{id}' => '<id:\\d+,\\d+>',
        ],
    ]*/
    // Example of a custom defined endpoints
    //'GET service-status' => 'site/status',
    //'GET v1/<keys:keys>/<controller>' => 'v1/<controller>/index',
];
