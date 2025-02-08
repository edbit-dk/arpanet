<?php

use App\CronController;
use App\API\APIController;
use App\System\SystemController;
use App\Email\EmailController;
use App\Help\HelpController;

use App\Host\HostService as Host;
use App\User\UserService as User;

// Setup
$app->get('/setup/install', [SystemController::class, 'install']);
$app->get('/setup/system', [SystemController::class, 'system']);
$app->get('/setup/users', [SystemController::class, 'users']);
$app->get('/setup/hosts', [SystemController::class, 'hosts']);
$app->get('/setup/relations', [SystemController::class, 'relations']);
$app->get('/setup/folders', [SystemController::class, 'folders']);
$app->get('/setup/files', [SystemController::class, 'files']);


// Home
$app->get('/', [SystemController::class, 'home']);
$app->get('/api', [APIController::class, 'authorize']);

// Boot
$app->get('/boot', [SystemController::class, 'boot']);
$app->get('/reboot', [SystemController::class, 'boot']);

// Start
$app->get('/main', [SystemController::class, 'main']);
$app->get('/uplink', [SystemController::class, 'main']);
$app->get('/reset', [SystemController::class, 'main']);
$app->get('/term', [SystemController::class, 'mode']);
$app->get('/ver', [SystemController::class, 'version']);

// Cron
$app->get('/minify', [SystemController::class, 'minify']);
$app->get('/stats', [SystemController::class, 'stats']);

// Help
if(!User::auth()) {
    $app->get('/help', [HelpController::class, 'visitor']);
}

if(User::auth() && !Host::auth()) {
    $app->get('/help', [HelpController::class, 'user']);
}

if(User::auth() && Host::guest()) {
    $app->get('/help', [HelpController::class, 'guest']);
}

if(User::auth() && Host::auth()) {
    $app->get('/help', [HelpController::class, 'host']);
}