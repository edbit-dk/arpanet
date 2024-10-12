<?php

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

require BASE_PATH . '/bootstrap/app.php';

require BASE_PATH . '/routes/web.php';

session()->start();

$app->run();
