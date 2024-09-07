<?php

$container['validator'] = function ($container) {
    return new \App\Validation\Validator;
}; 