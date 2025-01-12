<?php

Lib\Session::start();

$c = new Lib\Container();

$c->set('config', function() {
    return require BASE_PATH . '/config/settings.php';
});

$config = $c->config;

date_default_timezone_set($config['timezone']);

$c->set('view', function($c) {
    return new Lib\View($c->config['views']);
});

$c->set('request', function() {
    return new Lib\Request();
});

$c->set('session', function() {
    return new Lib\Session();
});

$c->set('access', function($c) {
    return new Lib\Access($c->config['path'] .'/public/.htaccess', $c->config['email'], $c->config['whitelist']);
});

$c->set('user', function() {
    return new App\User\UserService();
});

$c->set('host', function() {
    return new App\Host\HostService();
});

$db = new \Illuminate\Database\Capsule\Manager;
$db->addConnection($config['db']);
$db->setAsGlobal();
$db->bootEloquent();

$c->set('db', function($db) {
    return $db;
});

Lib\Session::set('music', $config['music']);

$css = filemtime($config['path'] . '/public/css/app.min.css');
$js = filemtime($config['path'] . '/public/js/app.min.js');
$hash = $css . $js;
Lib\Session::set('hash', $hash);

$app = new Lib\Router($c->request, $c);
$app->notFound($config['views'] . '404.php');