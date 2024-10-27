<?php

Lib\Session::start();

$c = new Lib\Container();

$c->set('config', function() {
    return require BASE_PATH . '/config/settings.php';
});

$c->set('view', function($c) {
    return new Lib\View($c->config['views']);
});

$c->set('request', function() {
    return new Lib\Request();
});

$c->set('session', function() {
    return new Lib\Session();
});

$c->set('db', function($capsule) {
    return $capsule;
});

$c->set('user', function() {
    return new App\User\UserService();
});

$c->set('host', function() {
    return new App\Host\HostService();
});

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($c->config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$app = new Lib\Router($c->request, $c);
$app->notFound($c->config['views'] . '404.php');
