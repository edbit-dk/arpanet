<?php
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$app->get('/', 'DefaultController:index')->setName('default');

// System
$app->get('/version', 'SystemController:version');
$app->get('/termlink', 'SystemController:termlink');
$app->get('/welcome', 'SystemController:welcome');

// Guest
$app->group('', function() {

    $this->get('/boot', 'SystemController:boot');

    // Auth
    $this->get('/register', 'AuthController:register');
    $this->get('/login', 'AuthController:login');

    // System
    $this->get('/uplink', 'SystemController:uplink');

})->add(new GuestMiddleware($c));


// Auth
$app->group('', function() {

    $this->get('/reboot', 'SystemController:boot');

    // Auth
    $this->get('/logout', 'AuthController:logout');

    // Commands
    $this->get('/help', 'CmdController:help');
    $this->get('/user', 'CmdController:user');
    $this->get('/accounts', 'CmdController:accounts');
    $this->get('/find', 'CmdController:find');
    $this->get('/dir', 'CmdController:dir');
    $this->get('/cd', 'CmdController:cd');
    $this->get('/more', 'CmdController:more');
    $this->get('/email', 'CmdController:email');

    $this->get('/servers', 'CmdController:servers');

    // Server
    $this->get('/connect', 'ServerController:connect');
    $this->get('/logon', 'ServerController:logon');
    $this->get('/logoff', 'ServerController:logoff');

})->add(new AuthMiddleware($c));
