<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'controller' => [
            'v1/user',
            // Add controllers here
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
    // Example of a custom defined endpoint
    //'GET service-status' => 'site/status',
];
