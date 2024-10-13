<?php

use App\Controllers\DefaultController;
use App\Controllers\SystemController;
use App\Controllers\ServerController;
use App\Controllers\AuthController;
use App\Controllers\CmdController;

$app->get('/', [DefaultController::class, 'index']);


// System
$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);
$app->get('/welcome', [SystemController::class, 'welcome']);

// Guest
if(!auth()->check()) {
    // Auth
    $app->get('/register', [AuthController::class, 'register']);
    $app->get('/login', [AuthController::class, 'login']);

    // System
    $app->get('/boot', [SystemController::class, 'boot']);
    $app->get('/uplink', [SystemController::class, 'uplink']);
}


// Auth
if(auth()->check()) {

    $app->get('/reboot', [SystemController::class, 'boot']);
    $app->get('/help', [CmdController::class, 'help']);

    // Auth
    $app->get('/logout', [AuthController::class, 'logout']);

    // Server
    $app->get('/connect', [ServerController::class, 'connect']);
    $app->get('/logon', [ServerController::class, 'logon']);
    $app->get('/logoff', [ServerController::class, 'logoff']);
}