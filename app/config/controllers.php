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

$c['ServerController'] = function ($c) {
    return new \App\Controllers\ServerController($c);
}; 

$c['DebugController'] = function ($c) {
    return new \App\Controllers\DebugController($c);
}; 

$c['FileController'] = function ($c) {
    return new \App\Controllers\FileController($c);
}; 