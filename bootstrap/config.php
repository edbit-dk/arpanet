<?php

$c->set('config', function() {
    return require BASE_PATH . '/config/settings.php';
});

date_default_timezone_set($c->config['timezone']);

ini_set('memory_limit', $c->config['memory_limit']); 