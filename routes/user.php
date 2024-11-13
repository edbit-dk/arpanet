<?php

use App\User\UserController;
use App\User\UserService as User;

if(User::auth()) {

     // Auth
     $app->get('/password', [UserController::class, 'password']);
     $app->get('/user', [UserController::class, 'user']);
     $app->get('/logout', [UserController::class, 'logout']);
     $app->get('/connection', [UserController::class, 'connection']);

}

if(!User::auth()) {
     $app->get('/login', [UserController::class, 'login']);
     $app->get('/newuser', [UserController::class, 'newuser']);
}