<?php

namespace App\Services;

class Router
{
    public $routes = [];
    public $request;
    public $container;

    /**
     * Constructor: Initialize the router with the request.
     *
     * @param Request $request
     */
    public function __construct(Request $request, Container $container)
    {
        $this->request = $request;
        $this->container = $container;
    }

    public function get(string $uri, callable|array $fn) : void
    {
        if ($this->request->method === 'GET') {
            $this->register($uri, $fn);
        }
    }

    public function post(string $uri, callable|array $fn) : void
    {
        if ($this->request->method === 'POST') {
            $this->register($uri, $fn);
        }
    }

    public function register(string $uri, $fn)
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $uri);
        $this->routes[$pattern] = [
            'callback' => $fn,
            'params' => $this->extractRouteParams($uri)
        ];
    }

    private function extractRouteParams(string $uri): array
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $uri, $matches);
        return $matches[1];
    }

    public function match(): ?array
    {
        foreach ($this->routes as $pattern => $route) {
            if (preg_match('#^' . $pattern . '$#', $this->request->uri, $matches)) {
                array_shift($matches);
                return [
                    'callback' => $route['callback'],
                    'params' => array_combine($route['params'], $matches)
                ];
            }
        }
        return null;
    }

    public function run()
    {
        $matchedRoute = $this->match();

        if ($matchedRoute) {
            $handler = $matchedRoute['callback'];
            $params = $matchedRoute['params'];

            if ($handler instanceof \Closure) {
                $handler($this, ...array_values($params));
            } elseif (is_array($handler) && count($handler) === 2) {
                [$controller, $method] = $handler;

                if (is_string($controller) && class_exists($controller)) {
                    $controller = new $controller($this);
                }

                if (is_object($controller) && method_exists($controller, $method)) {
                    call_user_func([$controller, $method], $this, ...array_values($params));
                } else {
                    throw new \Exception("Method $method not found in controller " . get_class($controller));
                }
            } else {
                throw new \Exception("Invalid route handler");
            }
        } else {
            echo "404 - Route not found";
        }
    }
}
