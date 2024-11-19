<?php

use App\User\UserController;
use App\User\UserService as User;
use App\Host\HostService as Host;

$app->get('/connection', [UserController::class, 'connection']);

if(User::auth()) {
     // Auth
     $app->get('/password', [UserController::class, 'password']);
     $app->get('/user', [UserController::class, 'user']);
}

if(!User::auth()) {
     $app->get('/login', [UserController::class, 'login']);
     $app->get('/newuser', [UserController::class, 'newuser']);
}

if(User::auth() && !Host::auth() && !Host::guest()) {
     $app->get('/logout', [UserController::class, 'logout']);
}