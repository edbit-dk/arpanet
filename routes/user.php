<?php

use App\Controllers\UserController;

if(auth()->check()) {

     // Auth
     $app->get('/password', [UserController::class, 'password']);
     $app->get('/user', [UserController::class, 'user']);
     $app->get('/logout', [UserController::class, 'logout']);

     // Sysadmin
     $app->get('/sysadmin571_bypass', [UserController::class, 'sysadmin']);

}

if(!auth()->check()) {
     $app->get('/login', [UserController::class, 'login']);
     $app->get('/newuser', [UserController::class, 'newuser']);
}