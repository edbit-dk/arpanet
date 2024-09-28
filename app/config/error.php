<?php

$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render(
            $response, 'error/404.twig', []
        );
    };
};

/*
$c['errorHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render(
            $response, 'error/500.twig', []
        )->withStatus(500);
    };
};
*/