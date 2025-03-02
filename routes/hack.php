<?php

use App\Hack\HackController;
use App\User\UserService as User;

if(User::auth()) {
    $app->get('/dump', [HackController::class, 'dump']);
    $app->get('/mem', [HackController::class, 'dump']);
    $app->get('/debug', [HackController::class, 'dump']);
}