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

function view($name) {
    global $c;

    $view = include $c->get('config')['views'] . $name;

    echo $view; 
}

function url() {

    $basePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
    // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
    $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($basePath));

    // Don't take query params into account on the URL
    if (strstr($uri, '?')) {
        $uri = substr($uri, 0, strpos($uri, '?'));
    }

    // Remove trailing slash + enforce a slash at the start
    echo '/' . trim($uri, '/');
}