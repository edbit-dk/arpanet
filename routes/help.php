<?php

use App\Help\HelpController;

use App\Host\HostService as Host;
use App\User\UserService as User;

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