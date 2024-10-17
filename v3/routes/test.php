<?php

use App\Controllers\HostController;
use App\Controllers\DefaultController;

$app->get('/test', [DefaultController::class, 'test']);

$app->get('/host-create', [HostController::class, 'create']);
