<?php

$app->get('/', 'DefaultController:index')->setName('default');



$app->post('/register', 'AuthController:register')->setName('auth.register');



$app->post('/boot', 'SystemController:boot');
$app->post('/welcome', 'SystemController:welcome');
$app->post('/help', 'SystemController:help');
$app->post('/uplink', 'SystemController:uplink');