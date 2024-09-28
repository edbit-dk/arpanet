<?php

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($c['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$c['db'] = function ($c) use ($capsule) {
    return $capsule;
};