<?php

use Lib\Session;

Session::start();

Session::set('music', $c->config['music']);

Session::set('hash', filemtime($c->config['path'] . '/public/css/app.min.css') 
                    . filemtime($c->config['path'] . '/public/js/app.min.js'));

$c->set('session', function() {
    return new Lib\Session();
});