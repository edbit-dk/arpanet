<?php

$c = new App\Services\Container();

$c->set('config', function() {
    $settings = require BASE_PATH . '/config/settings.php';
    return $settings;
});

// Bootstrap the request and router
$request = new App\Services\Request();

$app = new App\Services\Router($request, $c);
