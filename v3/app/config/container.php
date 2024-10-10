<?php

$c = new App\Services\Container();

$c->set('config', function() {
    $settings = require BASE_PATH . '/app/config/settings.php';
    return $settings;
});