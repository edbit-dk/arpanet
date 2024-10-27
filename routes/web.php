<?php

use App\Test\TestController;

$app->get('/test', [TestController::class, 'index']);

// User
require BASE_PATH . '/routes/user.php';

// Host
require BASE_PATH . '/routes/host.php';
