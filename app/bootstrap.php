<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';


$app = new \Slim\App(require __DIR__ . '/settings.php');

require __DIR__ . '/config/container.php';

require __DIR__ . '/config/env.php';

require __DIR__ . '/config/security.php';

require __DIR__ . '/config/database.php';

require __DIR__ . '/config/view.php';

require __DIR__ . '/config/controllers.php';

require __DIR__ . '/routes.php';