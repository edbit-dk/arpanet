<?php

return [
    'path' => BASE_PATH,
    'errors' => true,
    'views' =>  BASE_PATH . '/resources/views/',
    'database' => BASE_PATH . '/database/',
    'music' => [
        'public/music/80s_pop.mp3',
        'public/music/80s_pad.mp3',
        'public/music/80s_disco.mp3',
        'public/music/80s_synth.mp3'
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
];