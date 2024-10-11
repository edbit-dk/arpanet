<?php

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($c->get('config')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$c->set('db', function ($c) use ($capsule) {
    return $capsule;
});