<?php

use App\System\SystemController;

use App\Host\HostService as Host;

use App\File\FileController;
use App\Folder\FolderController;

// Boot
$app->get('/boot', [SystemController::class, 'boot']);
$app->get('/reboot', [SystemController::class, 'boot']);

// Start
$app->get('/uplink', [SystemController::class, 'main']);
$app->get('/reset', [SystemController::class, 'main']);
$app->get('/term', [SystemController::class, 'mode']);

$app->get('/ftp', [FileController::class, 'ftp']);


if(Host::auth() && !Host::guest()) {

    $app->get('/type', [FileController::class, 'cat']);
    $app->get('/cat', [FileController::class, 'cat']);
    $app->get('/more', [FileController::class, 'cat']);
    $app->get('/open', [FileController::class, 'cat']);

    $app->get('/echo', [FileController::class, 'echo']);
    $app->get('/cd', [FolderController::class, 'cd']);
    $app->get('/dir', [FileController::class, 'ls']);
    $app->get('/ls', [FileController::class, 'ls']);
}