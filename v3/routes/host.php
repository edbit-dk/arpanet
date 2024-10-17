<?php

use App\Controllers\HostController;
use App\Controllers\CmdController;
use App\Controllers\DebugController;


if(host()->guest()) {

    $app->get('/dump', [DebugController::class, 'dump']);
    $app->get('/set', [DebugController::class, 'set']);
    $app->get('/run', [DebugController::class, 'run']);
    $app->get('/logon', [HostController::class, 'logon']);
    
}


if(host()->auth()) {

    
}
