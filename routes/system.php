<?php

use App\System\SystemController;
use App\System\Help\HelpController;

use App\Host\HostService as Host;
use App\User\UserService as User;


// Home
$app->get('/', [SystemController::class, 'index']);

$app->get('/test', [SystemController::class, 'test']);
$app->get('/minify', [SystemController::class, 'minify']);

$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);
$app->get('/welcome', [SystemController::class, 'welcome']);
$app->get('/uplink', [SystemController::class, 'uplink']);
$app->get('/boot', [SystemController::class, 'boot']);

if(!User::auth()) {
    $app->get('/help', [HelpController::class, 'visitor']);
}

if(User::auth() && !Host::auth()) {
    $app->get('/help', [HelpController::class, 'user']);
}

if(User::auth() && Host::guest()) {
    $app->get('/help', [HelpController::class, 'guest']);
}

if(User::auth() && Host::auth()) {
    $app->get('/help', [HelpController::class, 'host']);
}