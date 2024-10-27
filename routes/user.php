<?php

use App\User\UserController;
use App\User\UserService as User;

if(User::auth()) {

     // Auth
     $app->get('/password', [UserController::class, 'password']);
     $app->get('/user', [UserController::class, 'user']);
     $app->get('/logout', [UserController::class, 'logout']);

     // Sysadmin
     $app->get('/sysadmin571_bypass', [UserController::class, 'sysadmin']);

}

if(!User::auth()) {
     $app->get('/login', [UserController::class, 'login']);
     $app->get('/newuser', [UserController::class, 'newuser']);
}