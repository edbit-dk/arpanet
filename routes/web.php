<?php

use App\Controllers\TestController;

$app->get('/test', [TestController::class, 'index']);

// User
require BASE_PATH . '/routes/user.php';

// Host
require BASE_PATH . '/routes/host.php';
