<?php

namespace Lib;

class Request
{
    public $method;
    public $uri;
    public $query;
    public $input;
    public $base;

    public function __construct()
    {
        // Capture the request method (GET, POST, etc.)
        $this->method = $_SERVER['REQUEST_METHOD'];

        // Parse the URI
        $this->uri = $this->request();

        // Parse query parameters (if any)
        $this->query = $_GET;

        // Parse query parameters (if any)
        $this->input = $_POST;

        // Extract the base path (e.g., if deployed in a subdirectory)
        $this->base = $this->root();
    }

    /**
     * Get the current URI, stripping the base path and query strings.
     *
     * @return string
     */
    private function request(): string
    {
        $cwd = basename(dirname(getcwd()));
        $uri = basename($_SERVER['REQUEST_URI']);
        $uri = rtrim($uri, '/');  // Remove trailing slashes for consistency
        
        if ($cwd == $uri) {
            $uri = str_replace($cwd, '/', $uri);
        } else {
            $uri = '/' . $uri;
        }

        return $uri = strtok($uri, '?'); // Strip query string
    }

    /**
     * Get the base path (if the application is running in a subdirectory).
     *
     * @return string
     */
    public function root(): string
    {
        return dirname($_SERVER['SCRIPT_NAME']);
    }

    /**
     * Get a specific query parameter by name.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->query[$key] ?? null;
    }

    /**
     * Get a specific query parameter by name.
     *
     * @param string $key
     * @return mixed|null
     */
    public function post(string $key)
    {
        return $this->input[$key] ?? null;
    }

    /**
     * Return the full request path, including the base path.
     *
     * @return string
     */
    public function full(): string
    {
        return $this->base . $this->uri;
    }
}
