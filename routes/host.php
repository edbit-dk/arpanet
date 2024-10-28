<?php

use App\Host\HostController;
use App\Host\Debug\DebugController;
use App\Host\HostService as Host;
use App\User\UserService as User;

// Home
$app->get('/', [HostController::class, 'index']);

$app->get('/host-create', [HostController::class, 'create']);

$app->get('/version', [HostController::class, 'version']);
$app->get('/termlink', [HostController::class, 'termlink']);
$app->get('/welcome', [HostController::class, 'welcome']);
$app->get('/help', [HostController::class, 'help']);
$app->get('/uplink', [HostController::class, 'uplink']);
$app->get('/boot', [HostController::class, 'boot']);
$app->get('/scan', [HostController::class, 'scan']);

if(User::auth()) {
    $app->get('/logon', [HostController::class, 'logon']);
    $app->get('/connect', [HostController::class, 'connect']);
    $app->get('/telnet', [HostController::class, 'connect']);
}

if(Host::guest()) {

    $app->get('/dump', [DebugController::class, 'dump']);
    $app->get('/set', [DebugController::class, 'set']);
    $app->get('/run', [DebugController::class, 'run']);    
}


if(Host::auth()) {
    // Host
    $app->get('/echo', [HostController::class, 'echo']);
    $app->get('/logout', [HostController::class, 'logout']);
    $app->get('/exit', [HostController::class, 'logout']);
    
}
