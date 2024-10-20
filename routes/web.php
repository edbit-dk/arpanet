<?php

use App\Controllers\DefaultController;

// Home
$app->get('/', [DefaultController::class, 'index']);


// Default
require BASE_PATH . '/routes/default.php';

// Test
require BASE_PATH . '/routes/test.php';

// Guest
require BASE_PATH . '/routes/guest.php';

// Auth
require BASE_PATH . '/routes/auth.php';

// Host
require BASE_PATH . '/routes/host.php';
