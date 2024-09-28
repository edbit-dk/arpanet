<?php

$c['view'] = function ($c) {
    $view = new \Slim\Views\Twig($c['settings']['view']);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->router,
        $c->request->getUri()
    ));

    return $view;
};