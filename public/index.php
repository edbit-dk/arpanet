<?php

session_start();

define('PATH_ROOT', dirname(__DIR__));

require PATH_ROOT . '/vendor/autoload.php';

require PATH_ROOT . '/app/bootstrap.php';

$app->run();