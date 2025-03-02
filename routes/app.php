<?php

use App\AppController;
use App\Cron\CronController;
use App\API\APIController;

// App
$app->get('/', [AppController::class, 'home']);
$app->get('/main', [AppController::class, 'main']);
$app->get('/ver', [AppController::class, 'version']);

// API
$app->get('/api', [APIController::class, 'authorize']);

// Cron
$app->get('/minify', [CronController::class, 'minify']);
$app->get('/stats', [CronController::class, 'stats']);