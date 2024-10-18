<?php

use App\Controllers\SystemController;
use App\Controllers\AuthController;

$app->get('/logon', [AuthController::class, 'logon']);
$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);
$app->get('/welcome', [SystemController::class, 'welcome']);
$app->get('/help', [SystemController::class, 'help']);