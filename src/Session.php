<?php

namespace Lib;

class Session
{

    public static $cacheExpire = null;
    public static $cacheLimiter = null;

    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {

            if (self::$cacheLimiter !== null) {
                session_cache_limiter(self::$cacheLimiter);
            }

            if (self::$cacheExpire !== null) {
                session_cache_expire(self::$cacheExpire);
            }

            session_start();
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        if (self::has($key)) {
            return $_SESSION[$key];
        }

        return false;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Session
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
        return new static;
    }

    public static function remove(string $key): void
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function clear(): void
    {
        session_unset();
        session_destroy();
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function empty(string $key): bool
    {
        if(self::has($key)) {
            return empty(self::get($key));
        }

        return true;
    }

}