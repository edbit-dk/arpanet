<?php

namespace App\Services;

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

        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Session
     */
    public function set(string $key, $value): Session
    {
        $_SESSION[$key] = $value;
        return $this;
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
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

}