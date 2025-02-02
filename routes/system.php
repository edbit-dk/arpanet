<?php

use App\API\APIController;
use App\System\SystemController;
use App\System\CronController;
use App\Email\EmailController;
use App\Help\HelpController;

use App\Host\HostService as Host;
use App\User\UserService as User;


// Home
$app->get('/', [SystemController::class, 'home']);
$app->get('/api', [APIController::class, 'authorize']);

// Boot
$app->get('/boot', [SystemController::class, 'boot']);
$app->get('/reboot', [SystemController::class, 'boot']);

// Start
$app->get('/main', [SystemController::class, 'main']);
$app->get('/uplink', [SystemController::class, 'uplink']);
$app->get('/reset', [SystemController::class, 'main']);
$app->get('/term', [SystemController::class, 'mode']);
$app->get('/ver', [SystemController::class, 'version']);

// Cron
$app->get('/minify', [CronController::class, 'minify']);
$app->get('/stats', [CronController::class, 'stats']);

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