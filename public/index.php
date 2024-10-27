<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

require BASE_PATH . '/bootstrap/app.php';

require BASE_PATH . '/routes/web.php';

$app->run();
