<?php

use Lib\Request;
use Lib\Input;

$c->set('request', function() {
    return new Request();
});

Input::request($c->request);

$app = new Lib\Router($c->request, $c);
$app->notFound($c->config['views'] . '404.php');