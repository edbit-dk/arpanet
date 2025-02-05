<?php

$db = new \Illuminate\Database\Capsule\Manager;
$db->addConnection($c->config['db']);
$db->setAsGlobal();
$db->bootEloquent();

$c->set('db', function($db) {
    return $db;
});

$c->set('user', function() {
    return new App\User\UserService();
});

$c->set('host', function() {
    return new App\Host\HostService();
});