<?php

return [
    'settings' => [
        'path' => PATH_ROOT,
        'displayErrorDetails' => true,
        'view' =>  PATH_ROOT. '/app/views', [
                'cache' => false
        ],
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'terminal',
            'username' => 'root',
            'password' => 'mysql',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ]
    ]
];