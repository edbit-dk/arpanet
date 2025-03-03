<?php

// Setup default constants
define('BASE_PATH', dirname(__DIR__));
define('TIMESTAMP', time());

/// Bootstrap app
require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/app.php';
require BASE_PATH . '/routes/web.php';

// Run app
$app->run();