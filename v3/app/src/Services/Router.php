<?php

namespace App\Services;

class Router
{
    /**
     * The request we're working with.
     *
     * @var string
     */
    public $request;

    /**
     * The $routes array will contain our URI's and callbacks.
     * @var array
     */
    public $routes = [];

    public $root = '/';

    /**
     * For this example, the constructor will be responsible
     * for parsing the request.
     *
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->root = basename(dirname(getcwd()));

        /**
         * This is a very (VERY) simple example of parsing
         * the request. We use the $_SERVER superglobal to
         * grab the URI.
         */
        if($this->root == basename($request['REQUEST_URI'])) {
            $this->request = str_replace($this->root, '/', basename($request['REQUEST_URI']));
        } else {
            $this->request = '/'. basename($request['REQUEST_URI']);
        }

    }

    /**
     * Add a route and callback to our $routes array.
     *
     * @param string $uri
     * @param Callable $fn
     */
    public function get(string $uri, \Closure $fn) : void
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->routes[$uri] = $fn;
        }
    }

     /**
     * Add a route and callback to our $routes array.
     *
     * @param string $uri
     * @param Callable $fn
     */
    public function post(string $uri, \Closure $fn) : void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->routes[$uri] = $fn;
        }
    }

    /**
     * Determine is the requested route exists in our
     * routes array.
     *
     * @param string $uri
     * @return boolean
     */
    public function has(string $uri) : bool
    {
        return array_key_exists($uri, $this->routes);
    }

    /**
     * Run the router.
     *
     * @return mixed
     */
    public function run()
    {
        if($this->has($this->request)) {
            $this->routes[$this->request]->call($this);
        }

        return http_response_code(404);
    }
}