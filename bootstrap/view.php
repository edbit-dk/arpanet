<?php

$c->set('view', function($c) {
    return new Lib\View($c->config['views']);
});

$c->set('access', function($c) {
    return new Lib\Access($c->config['path'] .'/public/.htaccess', 
    $c->config['email'], 
    $c->config['whitelist']);
});