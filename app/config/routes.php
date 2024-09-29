<?php
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$app->get('/', 'DefaultController:index')->setName('default');

// System
$app->get('/boot', 'SystemController:boot');
$app->get('/version', 'SystemController:version');
$app->get('/termlink', 'SystemController:termlink');
$app->get('/welcome', 'SystemController:welcome');

// Guest
$app->group('', function() {

    // Auth
    $this->get('/register', 'AuthController:register');
    $this->get('/login', 'AuthController:login');

    // System
    $this->get('/uplink', 'SystemController:uplink');

})->add(new GuestMiddleware($c));


// Auth
$app->group('', function() {

    // Auth
    $this->get('/logout', 'AuthController:logout');

    // Commands
    $this->get('/help', 'CmdController:help');
    $this->get('/user', 'CmdController:user');
    $this->get('/accounts', 'CmdController:accounts');
    $this->get('/logoff', 'CmdController:logoff');
    $this->get('/find', 'CmdController:find');
    $this->get('/dir', 'CmdController:dir');
    $this->get('/cd', 'CmdController:cd');
    $this->get('/more', 'CmdController:more');
    $this->get('/email', 'CmdController:email');

    $this->get('/servers', 'CmdController:servers');

    // Server
    $this->get('/connect', 'ServerController:connect');
    $this->get('/logon', 'ServerController:logon');

})->add(new AuthMiddleware($c));
