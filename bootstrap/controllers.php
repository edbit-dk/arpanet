<?php

$container['DefaultController'] = function ($container) {
    return new \App\Controllers\DefaultController($container);
}; 

$container['AuthController'] = function ($container) {
    return new \App\Controllers\AuthController($container);
}; 

$container['SystemController'] = function ($container) {
    return new \App\Controllers\SystemController($container);
}; 