<?php

use App\Controllers\SystemController;
use App\Controllers\HostController;
use App\Controllers\AuthController;
use App\Controllers\CmdController;

if(auth()->check()) {
    
$app->get('/reboot', [SystemController::class, 'boot']);

// Auth
$app->get('/password', [AuthController::class, 'password']);
$app->get('/user', [AuthController::class, 'user']);
$app->get('/logout', [AuthController::class, 'logout']);


// Host
$app->get('/connect', [HostController::class, 'connect']);
$app->get('/telnet', [HostController::class, 'connect']);
$app->get('/scan', [HostController::class, 'scan']);
$app->get('/logon', [HostController::class, 'logon']);
$app->get('/logoff', [HostController::class, 'logoff']);
$app->get('/exit', [HostController::class, 'logoff']);


// Sysadmin
$app->get('/sysadmin571_bypass', [HostController::class, 'sysadmin']);

}