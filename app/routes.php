<?php

$app->get('/', 'DefaultController:index')->setName('default');



$app->get('/register', 'AuthController:register')->setName('auth.register');



$app->get('/boot', 'SystemController:boot');
$app->get('/welcome', 'SystemController:welcome');
$app->get('/terminal', 'SystemController:terminal');
$app->get('/help', 'SystemController:help');
$app->get('/version', 'SystemController:version');
$app->get('/uplink', 'SystemController:uplink');

