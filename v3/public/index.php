<?php

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap.php';

App\Services\Session::start();

$app->run();
