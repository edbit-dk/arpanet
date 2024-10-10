<?php

$app->get('/', function() {
    global $c;
    $controller = new App\Controllers\DefaultController($c);
    return $controller->index();
});

$app->get('/get', function() {
    echo 'hello';
});

$app->post('/post', function() {
    echo 'Well, hello there!!';
});
