<?php

use App\System\SystemController;

// Home
$app->get('/', [SystemController::class, 'index']);

$app->get('/test', [SystemController::class, 'test']);
$app->get('/minify', [SystemController::class, 'minify']);

$app->get('/version', [SystemController::class, 'version']);
$app->get('/termlink', [SystemController::class, 'termlink']);
$app->get('/welcome', [SystemController::class, 'welcome']);
$app->get('/help', [SystemController::class, 'help']);
$app->get('/uplink', [SystemController::class, 'uplink']);
$app->get('/boot', [SystemController::class, 'boot']);