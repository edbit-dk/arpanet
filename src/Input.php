<?php

namespace Lib;

class Input
{
    protected static Request $request;

    /**
     * Initialize the Input class with a Request instance.
     *
     * @param Request $request
     */
    public static function request(Request $request)
    {
        self::$request = $request;
    }

    /**
     * Sanitize input data.
     *
     * @param mixed $data
     * @return mixed
     */
    protected static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlentities(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get a value from either GET or POST data.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return self::sanitize(self::$request->query[$key] ?? self::$request->input[$key] ?? $default);
    }

    /**
     * Get a value from GET parameters only.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function query(string $key, $default = null)
    {
        return self::sanitize(self::$request->query[$key] ?? $default);
    }

    /**
     * Get a value from POST parameters only.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function post(string $key, $default = null)
    {
        return self::sanitize(self::$request->input[$key] ?? $default);
    }

    /**
     * Get the entire request data as an array.
     *
     * @return array
     */
    public static function all(): array
    {
        return self::sanitize(array_merge(self::$request->query, self::$request->input));
    }

    /**
     * Check if a key exists in the request data.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset(self::$request->query[$key]) || isset(self::$request->input[$key]);
    }
}
