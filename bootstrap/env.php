<?php

$container['environment'] = function () {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['REAL_SCRIPT_NAME'] = $scriptName;
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);

    return new Slim\Http\Environment($_SERVER);
};