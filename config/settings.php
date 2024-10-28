<?php

return [
    'path' => BASE_PATH,
    'errors' => true,
    'views' =>  BASE_PATH . '/resources/views/',
    'database' => BASE_PATH . '/database/',
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
];