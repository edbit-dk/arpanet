<?php

$c->set('request', function() {
    return new Lib\Request();
});

$app = new Lib\Router($c->request, $c);
$app->notFound($c->config['views'] . '404.php');