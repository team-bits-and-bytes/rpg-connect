<?php
return [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true, // set to false in production
        'renderer' => [
            'template_path' => __DIR__ . '/../../templates/',
        ],
        'database' => [
            'driver' => 'mysql',
            'host' => getenv('IP'),
            'database' => 'c9',
            'username' => getenv('C9_USER'),
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ],
        'redis' => [
            'host' => getenv('IP'),
            'port' => 6379    
        ]
    ],
];