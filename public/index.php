<?php

define('BASE_PATH', dirname(__DIR__));
define('TIMESTAMP', time());

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL

ini_set('ignore_repeated_errors', TRUE); // always use TRUE

ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', BASE_PATH . '/storage/logs/errors.log'); // Logging file path

require BASE_PATH . '/vendor/autoload.php';

require BASE_PATH . '/bootstrap/app.php';

require BASE_PATH . '/routes/web.php';

$app->run();