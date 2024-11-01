<?php

use App\System\SystemController;

$app->get('/test', [SystemController::class, 'test']);
$app->get('/minify', [SystemController::class, 'minify']);

// User
require BASE_PATH . '/routes/user.php';

// Host
require BASE_PATH . '/routes/host.php';
