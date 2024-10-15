<?php

use App\Controllers\DefaultController;
use App\Controllers\SystemController;
use App\Controllers\HostController;
use App\Controllers\AuthController;
use App\Controllers\CmdController;
use App\Controllers\DebugController;

$app->get('/', [DefaultController::class, 'index']);
$app->get('/test', [DefaultController::class, 'test']);

$app->get('/host-create', [HostController::class, 'create']);


// System
$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);
$app->get('/welcome', [SystemController::class, 'welcome']);

// Guest
if(!auth()->check()) {
    // Auth
    $app->get('/newuser', [AuthController::class, 'newuser']);
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
    $app->get('/password', [AuthController::class, 'password']);
    $app->get('/user', [AuthController::class, 'user']);
    $app->get('/logout', [AuthController::class, 'logout']);

    // Debug
    $app->get('/dump', [DebugController::class, 'dump']);
    $app->get('/set', [DebugController::class, 'set']);
    $app->get('/run', [DebugController::class, 'run']);

    // Host
    $app->get('/connect', [HostController::class, 'connect']);
    $app->get('/telnet', [HostController::class, 'connect']);
    $app->get('/logon', [HostController::class, 'logon']);
    $app->get('/logoff', [HostController::class, 'logoff']);
    $app->get('/exit', [HostController::class, 'logoff']);


    // CMD
    $app->get('/scan', [CmdController::class, 'scan']);
}