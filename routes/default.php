<?php

use App\Controllers\UserController;

$app->get('/version', [UserController::class, 'version']);
$app->get('/termlink', [UserController::class, 'termlink']);
$app->get('/welcome', [UserController::class, 'welcome']);
$app->get('/help', [UserController::class, 'help']);
$app->get('/uplink', [UserController::class, 'uplink']);
$app->get('/boot', [UserController::class, 'boot']);
$app->get('/logon', [UserController::class, 'logon']);