<?php

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig($container['settings']['view']);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};