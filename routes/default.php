<?php

use App\Controllers\SystemController;

$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);
$app->get('/welcome', [SystemController::class, 'welcome']);
$app->get('/help', [SystemController::class, 'help']);
$app->get('/uplink', [SystemController::class, 'uplink']);
$app->get('/boot', [SystemController::class, 'boot']);