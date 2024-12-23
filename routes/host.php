<?php

use App\Host\HostController;
use App\Host\File\FileController;
use App\Host\Debug\DebugController;
use App\Host\HostService as Host;
use App\User\UserService as User;

$app->get('/host-create', [HostController::class, 'create']);

if(User::auth()) {
    $app->get('/scan', [HostController::class, 'scan']);
    $app->get('/netstat', [HostController::class, 'scan']);
    $app->get('/connect', [HostController::class, 'connect']);
    $app->get('/telnet', [HostController::class, 'connect']);
}

if(Host::guest()) {
    $app->get('/login', [HostController::class, 'logon']);
    $app->get('/logon', [HostController::class, 'logon']);
    $app->get('/dump', [DebugController::class, 'dump']);
    $app->get('/mem', [DebugController::class, 'dump']);
    $app->get('/debug', [DebugController::class, 'dump']);
    $app->get('/set', [DebugController::class, 'set']);
    $app->get('/run', [DebugController::class, 'run']);  
    
    // sysadmin571_bypass /:
    $app->get('/sysadmin571_bypass', [HostController::class, 'sysadmin']);
}

if(Host::auth() && User::username() != 'guest') {
    $app->get('/mail', [FileController::class, 'mail']);
    $app->get('/dir', [FileController::class, 'dir']);
    $app->get('/ls', [FileController::class, 'dir']);
    $app->get('/type', [FileController::class, 'cat']);
    $app->get('/cat', [FileController::class, 'cat']);
    $app->get('/more', [FileController::class, 'cat']);
    $app->get('/open', [FileController::class, 'cat']);
    $app->get('/echo', [FileController::class, 'echo']);
}

if(Host::auth() OR Host::guest()) {
    $app->get('/logoff', [HostController::class, 'logoff']);
    $app->get('/exit', [HostController::class, 'logoff']);
    $app->get('/quit', [HostController::class, 'logoff']);
    $app->get('/dc', [HostController::class, 'logoff']);
    $app->get('/close', [HostController::class, 'logoff']);
}
