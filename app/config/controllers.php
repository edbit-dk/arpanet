<?php

$c['DefaultController'] = function ($c) {
    return new \App\Controllers\DefaultController($c);
}; 

$c['AuthController'] = function ($c) {
    return new \App\Controllers\AuthController($c);
}; 

$c['SystemController'] = function ($c) {
    return new \App\Controllers\SystemController($c);
}; 

$c['CmdController'] = function ($c) {
    return new \App\Controllers\CmdController($c);
}; 