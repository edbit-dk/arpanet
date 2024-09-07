<?php

use Respect\Validation\Validator as v;

$container['validator'] = function ($container) {
    return new \App\Services\Validator;
};

v::with('App\\Validation\\Rules\\');


// Config Auth
$container['auth'] = function ($container) {
    return new App\Services\Auth;
};