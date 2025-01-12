<?php

return [
    'version' => 1.0,
    'path' => BASE_PATH,
    'date' => 'D M j H:i:s',
    'email' => '',
    'errors' => true,
    'public' => BASE_PATH . '/public/',
    'views' =>  BASE_PATH . '/resources/views/',
    'database' => BASE_PATH . '/database/',
    'timezone' => 'Europe/Copenhagen',
    'music' => [
        'public/sound/80s_pop.mp3',
        'public/sound/80s_pad.mp3',
        'public/sound/80s_disco.mp3',
        'public/sound/80s_synth.mp3'
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
    ],
    'whitelist' => [
        '194.45.79.27'
    ]
];