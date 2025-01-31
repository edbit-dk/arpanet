<?php

use App\System\SetupController;

$app->get('/setup/install', [SetupController::class, 'install']);
$app->get('/setup/system', [SetupController::class, 'system']);
$app->get('/setup/users', [SetupController::class, 'users']);
$app->get('/setup/hosts', [SetupController::class, 'hosts']);
$app->get('/setup/relations', [SetupController::class, 'relations']);
$app->get('/setup/folders', [SetupController::class, 'folders']);
$app->get('/setup/files', [SetupController::class, 'files']);