<?php

use App\Controllers\HostController;


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

if(host()->guest()) {

    $app->get('/dump', [HostController::class, 'dump']);
    $app->get('/set', [HostController::class, 'set']);
    $app->get('/run', [HostController::class, 'run']);    
}


if(host()->auth()) {
    // Host
    $app->get('/connect', [HostController::class, 'connect']);
    $app->get('/telnet', [HostController::class, 'connect']);
    $app->get('/logoff', [HostController::class, 'logoff']);
    $app->get('/exit', [HostController::class, 'logoff']);
    
}
