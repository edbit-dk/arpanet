<?php

use App\System\SystemController;
use App\System\Help\HelpController;

use App\Host\HostService as Host;
use App\User\UserService as User;


// Home
$app->get('/', [SystemController::class, 'index']);

// Default
$app->get('/minify', [SystemController::class, 'minify']);
$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);

// Start
$app->get('/uplink', [SystemController::class, 'uplink']);
$app->get('/welcome', [SystemController::class, 'welcome']);

// Boot
$app->get('/boot', [SystemController::class, 'boot']);
$app->get('/reboot', [SystemController::class, 'boot']);

// Help
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