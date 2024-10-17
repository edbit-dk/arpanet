<?php

use App\Controllers\SystemController;
use App\Controllers\HostController;
use App\Controllers\AuthController;
use App\Controllers\CmdController;
use App\Controllers\DebugController;


// Guest
if(!auth()->check()) {
 
    // Auth
    $app->get('/newuser', [AuthController::class, 'newuser']);
    $app->get('/login', [AuthController::class, 'login']);

    // System
    $app->get('/boot', [SystemController::class, 'boot']);
    $app->get('/uplink', [SystemController::class, 'uplink']);

}