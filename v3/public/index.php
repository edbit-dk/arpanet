<?php

session_cache_limiter(false);
session_start();

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/bootstrap.php';

$app->run();
