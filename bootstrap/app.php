<?php

$c = new Custom\Container();

$c->set('config', function() {
    return require BASE_PATH . '/config/settings.php';
});

$c->set('view', function($c) {
    return new Custom\View($c->config['views']);
});

$c->set('request', function() {
    return new Custom\Request();
});

$c->set('session', function() {
    return new Custom\Session();
});

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($c->config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$c->set('db', function($capsule) {
    return $capsule;
});

$c->set('user', function() {
    return new App\Services\UserService();
});

$c->set('host', function() {
    return new App\Services\HostService();
});

$app = new Custom\Router($c->request, $c);
$app->notFound($c->config['views'] . '404.php');
