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

function config($name) {
    return app('config')[$name];
}

function timestamp($date = false) {

    if(is_string($date)) {
        return date(config('date'), strtotime($date));
    } 

    if(is_int($date)) {
        return date(config('date'), $date);
    }

    if($date) {
        return date(config('date'), time());
    }
    
}

function request() {
    return app('request');
}

function session() {
    return app('session');
}

function user() {
    return app('user');
}

function auth() {
    return user()->data();
}

function host() {
    return app('host');
}

function db() {
    return app('db');
}

function access() {
    return app('access');
}

function view($name, $data = []) {

    return app('view')->render($name, $data);
}

function text($name) {
    return file_get_contents(config('public') . "text/$name");
}

function base_url() {

    echo $_SERVER['REQUEST_URI'] . 'public';
}

function parse_request($data = '') {

    $data = request()->get($data);

    if(is_null($data)) {
        $data = '';
    }

    return explode(' ', trim($data));
}