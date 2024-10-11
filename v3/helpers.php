<?php

function dd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function app($name) {
    global $c;

    return $c->get($name);
}

function view($name, $data = []) {
    global $c;

    $data;

    return include $c->get('config')['views'] . $name;
}

function base_url() {

    echo $_SERVER['REQUEST_URI'] . 'public';
}