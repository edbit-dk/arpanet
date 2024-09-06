<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';


$app = new \Slim\App(require __DIR__ . '/../app/settings.php');

require __DIR__ . '/../bootstrap/container.php';

require __DIR__ . '/../bootstrap/env.php';

require __DIR__ . '/../bootstrap/database.php';

require __DIR__ . '/../bootstrap/view.php';

require __DIR__ . '/../bootstrap/controllers.php';

require __DIR__ . '/../app/routes.php';