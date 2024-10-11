<?php

use App\Controllers\DefaultController;

$app->get('/', function($app) {
    view('app.php');
});

$app->get('/boot', function($app) {
    view('robco/boot.txt');
});

$app->get('/welcome', function($app) {
    view('robco/welcome.txt');
});



/*
$app->get('/welcome', [DefaultController::class, 'index']);
*/  