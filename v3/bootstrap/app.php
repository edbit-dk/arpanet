<?php

$c = new App\Providers\Container();

$c->set('config', function() {
    return require BASE_PATH . '/config/settings.php';
});

$c->set('view', function($c) {
    return new App\Providers\View($c->config['views']);
});

$c->set('request', function() {
    return new App\Providers\Request();
});

$c->set('session', function() {
    return new App\Providers\Session();
});

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($c->config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$c->set('db', function($capsule) {
    return $capsule;
});

$c->set('auth', function() {
    return new App\Services\UserService();
});

$c->set('mainframe', function() {
    return new App\Services\ServerService();
});

$app = new App\Providers\Router($c->request, $c);
$app->notFound($c->config['views'] . '404.php');
