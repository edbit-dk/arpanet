<?php

use App\Debug\DebugController;
use App\User\UserService as User;

if(User::auth()) {
    $app->get('/dump', [DebugController::class, 'dump']);
    $app->get('/mem', [DebugController::class, 'dump']);
    $app->get('/debug', [DebugController::class, 'dump']);
}